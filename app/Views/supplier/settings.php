<?php
/**
 * Supplier Settings Page
 * Allows suppliers to manage their account settings and change password
 */
?>
<?php include __DIR__ . '/layout-header.php'; ?>

<style>
.settings-container {
    max-width: 800px;
    margin: 0 auto;
}
.pw-rules-box { margin-top: 8px; display: flex; flex-direction: column; gap: 2px; }
.pw-rule      { font-size: 13px; display: flex; align-items: center; gap: 6px; padding: 2px 0; transition: color 0.2s; }
.pw-rule-fail { color: #9ca3af; }
.pw-rule-ok   { color: #00b207; }
.pw-rule i    { font-size: 12px; }
.pw-match-error { font-size: 13px; color: #ef4444; margin-top: 6px; display: none; }

.settings-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.settings-card .card-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 8px 8px 0 0;
}

.settings-card .card-header h2 {
    margin: 0;
    font-size: 18px;
    color: #1f2937;
}

.settings-card .card-header i {
    margin-right: 8px;
    color: #00b207;
}

.settings-card .card-body {
    padding: 24px;
}

.info-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row label {
    font-weight: 600;
    color: #6b7280;
    min-width: 150px;
}

.info-row span {
    color: #1f2937;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-group input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-group input:focus {
    outline: none;
    border-color: #00b207;
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
}

.form-group small {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #6b7280;
}

.required {
    color: #ef4444;
}

.form-actions {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #00b207 0%, #008505 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 178, 7, 0.3);
}

.btn i {
    margin-right: 6px;
}

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 16px;
    font-size: 14px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

@media (max-width: 900px) {
    .form-row { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .address-suggestions { max-height: 180px; }
}

.form-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    background: #fff;
    transition: border-color 0.2s;
}

.form-group select:focus {
    outline: none;
    border-color: #00b207;
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
}

.form-group input:disabled {
    background: #f3f4f6;
    color: #6b7280;
    cursor: not-allowed;
}

.last-login-info {
    font-size: 13px;
    color: #6b7280;
    margin-left: 16px;
}

.form-actions {
    display: flex;
    align-items: center;
}

/* Address Autocomplete */
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

.address-suggestions.active {
    display: block;
}

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

.address-suggestion-item:last-child {
    border-bottom: none;
}

.address-suggestion-item:hover,
.address-suggestion-item.highlighted {
    background: #f0fdf4;
}

.address-suggestion-item i {
    color: #00b207;
    margin-top: 3px;
    flex-shrink: 0;
}

.address-suggestion-item .suggestion-main {
    font-weight: 500;
}

.address-suggestion-item .suggestion-detail {
    font-size: 12px;
    color: #6b7280;
}

.address-autocomplete-wrapper small i {
    margin-right: 4px;
    color: #00b207;
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

.pref-option input[type="radio"] {
    display: none;
}

.pref-option i {
    color: #00b207;
}

.address-loading {
    padding: 12px 14px;
    text-align: center;
    color: #6b7280;
    font-size: 13px;
}

.password-wrapper {
    position: relative;
}

.password-wrapper input {
    padding-right: 44px;
}

.password-toggle {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #6b7280;
    padding: 4px 8px;
    font-size: 16px;
    transition: color 0.2s;
}

.password-toggle:hover {
    color: #00b207;
}

/* Notification & Sound card */
.notif-pref-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 6px 0;
}
.notif-pref-info { flex: 1; }
.notif-pref-label {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}
.notif-pref-label i { color: #00b207; }
.notif-pref-desc { font-size: 13px; color: #6b7280; line-height: 1.5; }

/* Toggle switch */
.toggle-switch { position: relative; display: inline-block; width: 46px; height: 26px; flex-shrink: 0; cursor: pointer; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-track {
    position: absolute; inset: 0;
    background: #d1d5db; border-radius: 26px;
    transition: background 0.2s;
}
.toggle-track::before {
    content: '';
    position: absolute;
    width: 20px; height: 20px;
    left: 3px; bottom: 3px;
    background: white; border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.toggle-switch input:checked + .toggle-track { background: #00b207; }
.toggle-switch input:checked + .toggle-track::before { transform: translateX(20px); }

/* Test & status */
.btn-test-sound {
    background: none;
    border: 1px solid #00b207;
    color: #00b207;
    border-radius: 8px;
    padding: 7px 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background 0.15s, color 0.15s;
}
.btn-test-sound:hover { background: #00b207; color: white; }

.notif-status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
}
.notif-status-badge.granted  { background: #d1fae5; color: #059669; }
.notif-status-badge.default  { background: #fef3c7; color: #92400e; }
.notif-status-badge.denied   { background: #fee2e2; color: #dc2626; }
</style>

<div class="supplier-main-content">
    <div class="page-header">
        <h1><i class="fas fa-cog"></i> <?= $fr ? 'Paramètres' : 'Settings' ?></h1>
        <p><?= $fr ? 'Gérez les paramètres et la sécurité de votre compte' : 'Manage your account settings and security' ?></p>
    </div>

    <div class="settings-container">
        <!-- Profile Information (Editable) -->
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-user"></i> <?= $fr ? 'Informations du profil' : 'Profile Information' ?></h2>
            </div>
            <div class="card-body">
                <form id="profileForm">
                    <?= csrfField() ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="company_name"><?= $fr ? "Nom de l'entreprise" : 'Company Name' ?> <span class="required">*</span></label>
                            <input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($supplier['company_name'] ?? $supplier['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_person"><?= $fr ? 'Personne-contact' : 'Contact Person' ?></label>
                            <input type="text" id="contact_person" name="contact_person" value="<?= htmlspecialchars($supplier['contact_person'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email_display"><?= $fr ? 'Courriel' : 'Email' ?></label>
                            <input type="email" id="email_display" value="<?= htmlspecialchars($supplier['email']) ?>" disabled>
                            <small><?= $fr ? "Contactez l'administrateur pour modifier le courriel" : 'Contact admin to change email' ?></small>
                        </div>
                        <div class="form-group">
                            <label for="phone"><?= $fr ? 'Téléphone' : 'Phone' ?></label>
                            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($supplier['phone'] ?? '') ?>" pattern="[\d\s\-\(\)\+]{10,}" title="<?= $fr ? 'Entrez un numéro de téléphone valide (au moins 10 chiffres)' : 'Enter a valid phone number (at least 10 digits)' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tax_number"><?= $fr ? 'Numéro TPS / TVQ' : 'HST / GST Number' ?></label>
                            <input type="text" id="tax_number" name="tax_number" value="<?= htmlspecialchars($supplier['tax_number'] ?? '') ?>" placeholder="123456789RT0001">
                            <small><?= $fr ? "Votre numéro d'entreprise de l'Agence du revenu du Canada" : 'Your Canada Revenue Agency business number' ?></small>
                        </div>
                        <div class="form-group"><!-- spacer --></div>
                    </div>

                    <hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">
                    <h3 style="font-size: 16px; color: #374151; margin-bottom: 16px;"><i class="fas fa-map-marker-alt" style="color: #00b207; margin-right: 8px;"></i><?= $fr ? 'Adresse' : 'Address' ?></h3>

                    <div class="form-group address-autocomplete-wrapper">
                        <label for="address"><?= $fr ? 'Adresse de rue' : 'Street Address' ?></label>
                        <div style="position: relative;">
                            <input type="text" id="address" name="address" value="<?= htmlspecialchars($supplier['address'] ?? '') ?>" placeholder="<?= $fr ? 'Commencez à saisir une adresse...' : 'Start typing an address...' ?>" autocomplete="off">
                            <div id="addressSuggestions" class="address-suggestions"></div>
                        </div>
                        <small id="addressHint"><i class="fas fa-magic"></i> <?= $fr ? 'Tapez pour rechercher — sélectionner une suggestion remplit tous les champs ci-dessous' : 'Type to search — selecting a suggestion auto-fills all fields below' ?></small>
                        <input type="hidden" id="latitude" name="latitude" value="<?= htmlspecialchars($supplier['latitude'] ?? '') ?>">
                        <input type="hidden" id="longitude" name="longitude" value="<?= htmlspecialchars($supplier['longitude'] ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city"><?= $fr ? 'Ville' : 'City' ?></label>
                            <input type="text" id="city" name="city" value="<?= htmlspecialchars($supplier['city'] ?? '') ?>" placeholder="Toronto">
                        </div>
                        <div class="form-group">
                            <label for="province"><?= $fr ? 'Province' : 'Province' ?></label>
                            <select id="province" name="province">
                                <option value=""><?= $fr ? 'Sélectionner la province' : 'Select Province' ?></option>
                                <?php
                                $provinces = [
                                    'AB' => 'Alberta', 'BC' => 'British Columbia', 'MB' => 'Manitoba',
                                    'NB' => 'New Brunswick', 'NL' => 'Newfoundland and Labrador',
                                    'NS' => 'Nova Scotia', 'NT' => 'Northwest Territories',
                                    'NU' => 'Nunavut', 'ON' => 'Ontario', 'PE' => 'Prince Edward Island',
                                    'QC' => 'Quebec', 'SK' => 'Saskatchewan', 'YT' => 'Yukon'
                                ];
                                $currentProvince = $supplier['province'] ?? '';
                                foreach ($provinces as $code => $name):
                                ?>
                                    <option value="<?= $code ?>" <?= ($currentProvince === $code || $currentProvince === $name) ? 'selected' : '' ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="postal_code"><?= $fr ? 'Code postal' : 'Postal Code' ?></label>
                            <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($supplier['postal_code'] ?? '') ?>" placeholder="M5V 2T6" maxlength="7" pattern="[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d" title="<?= $fr ? 'Code postal canadien (ex. M5V 2T6)' : 'Canadian postal code (e.g. M5V 2T6)' ?>">
                        </div>
                        <div class="form-group">
                            <label for="country"><?= $fr ? 'Pays' : 'Country' ?></label>
                            <input type="text" id="country" name="country" value="<?= htmlspecialchars($supplier['country'] ?? 'Canada') ?>">
                        </div>
                    </div>

                    <div id="profileMessage" class="alert" style="display: none;"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?= $fr ? 'Enregistrer le profil' : 'Save Profile' ?>
                        </button>
                        <span class="last-login-info"><?= $fr ? 'Dernière connexion :' : 'Last login:' ?> <?= $supplier['last_login_at'] ? date('F j, Y g:i A', strtotime($supplier['last_login_at'])) : 'Never' ?></span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-university"></i> <?= $fr ? 'Informations de paiement' : 'Payment Information' ?></h2>
            </div>
            <div class="card-body">
                <p style="font-size:13px;color:#6b7280;margin-bottom:20px;">
                    <?= $fr ? 'Fournissez vos coordonnées bancaires afin que nous puissions traiter les paiements pour vos bons de commande. Toutes les informations sont chiffrées et accessibles uniquement par le personnel autorisé d\'OCSAPP.' : 'Provide your banking details so we can process payments for your purchase orders. All information is encrypted and only accessible by authorized OCSAPP staff.' ?>
                </p>
                <form id="bankingForm">
                    <?= csrfField() ?>

                    <!-- Payment Preference -->
                    <div class="form-group">
                        <label><?= $fr ? 'Mode de paiement préféré' : 'Payment Preference' ?></label>
                        <div class="payment-pref-options">
                            <?php $pref = $supplier['payment_preference'] ?? ''; ?>
                            <label class="pref-option <?= $pref === 'eft' ? 'active' : '' ?>">
                                <input type="radio" name="payment_preference" value="eft" <?= $pref === 'eft' ? 'checked' : '' ?>>
                                <i class="fas fa-exchange-alt"></i> <?= $fr ? 'TVE / Virement direct' : 'EFT / Direct Deposit' ?>
                            </label>
                            <label class="pref-option <?= $pref === 'interac' ? 'active' : '' ?>">
                                <input type="radio" name="payment_preference" value="interac" <?= $pref === 'interac' ? 'checked' : '' ?>>
                                <i class="fas fa-envelope"></i> <?= $fr ? 'Virement Interac' : 'Interac e-Transfer' ?>
                            </label>
                            <label class="pref-option <?= $pref === 'cheque' ? 'active' : '' ?>">
                                <input type="radio" name="payment_preference" value="cheque" <?= $pref === 'cheque' ? 'checked' : '' ?>>
                                <i class="fas fa-file-invoice-dollar"></i> <?= $fr ? 'Chèque' : 'Cheque' ?>
                            </label>
                        </div>
                    </div>

                    <!-- EFT Fields -->
                    <div id="eftFields" class="banking-section" style="display:<?= $pref === 'eft' ? 'block' : 'none' ?>;">
                        <hr style="margin:16px 0;border:none;border-top:1px solid #e5e7eb;">
                        <h3 style="font-size:15px;color:#374151;margin-bottom:16px;"><i class="fas fa-landmark" style="color:#00b207;margin-right:8px;"></i><?= $fr ? 'Détails TVE / Virement direct' : 'EFT / Direct Deposit Details' ?></h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bank_name"><?= $fr ? 'Nom de la banque' : 'Bank Name' ?> <span class="required">*</span></label>
                                <input type="text" id="bank_name" name="bank_name" value="<?= htmlspecialchars($supplier['bank_name'] ?? '') ?>" placeholder="ex. TD, RBC, BMO">
                            </div>
                            <div class="form-group">
                                <label for="bank_account_holder"><?= $fr ? 'Nom du titulaire du compte' : 'Account Holder Name' ?> <span class="required">*</span></label>
                                <input type="text" id="bank_account_holder" name="bank_account_holder" value="<?= htmlspecialchars($supplier['bank_account_holder'] ?? '') ?>" placeholder="<?= $fr ? 'Nom sur le compte' : 'Name on account' ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bank_transit"><?= $fr ? 'Numéro de transit (5 chiffres)' : 'Transit Number (5 digits)' ?></label>
                                <input type="text" id="bank_transit" name="bank_transit" value="<?= htmlspecialchars($supplier['bank_transit'] ?? '') ?>" placeholder="12345" maxlength="5" pattern="\d{5}">
                            </div>
                            <div class="form-group">
                                <label for="bank_institution"><?= $fr ? "Numéro d'institution (3 chiffres)" : 'Institution Number (3 digits)' ?></label>
                                <input type="text" id="bank_institution" name="bank_institution" value="<?= htmlspecialchars($supplier['bank_institution'] ?? '') ?>" placeholder="004" maxlength="3" pattern="\d{3}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bank_account"><?= $fr ? 'Numéro de compte' : 'Account Number' ?></label>
                                <input type="text" id="bank_account" name="bank_account" value="<?= htmlspecialchars($supplier['bank_account'] ?? '') ?>" placeholder="7-12 digits" maxlength="12">
                            </div>
                            <div class="form-group">
                                <label for="bank_account_type"><?= $fr ? 'Type de compte' : 'Account Type' ?></label>
                                <select id="bank_account_type" name="bank_account_type">
                                    <option value=""><?= $fr ? 'Sélectionner le type' : 'Select type' ?></option>
                                    <option value="chequing" <?= ($supplier['bank_account_type'] ?? '') === 'chequing' ? 'selected' : '' ?>><?= $fr ? 'Chèques' : 'Chequing' ?></option>
                                    <option value="savings"  <?= ($supplier['bank_account_type'] ?? '') === 'savings'  ? 'selected' : '' ?>><?= $fr ? 'Épargne' : 'Savings' ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Interac Fields -->
                    <div id="interacFields" class="banking-section" style="display:<?= $pref === 'interac' ? 'block' : 'none' ?>;">
                        <hr style="margin:16px 0;border:none;border-top:1px solid #e5e7eb;">
                        <h3 style="font-size:15px;color:#374151;margin-bottom:16px;"><i class="fas fa-envelope" style="color:#00b207;margin-right:8px;"></i><?= $fr ? 'Détails Virement Interac' : 'Interac e-Transfer Details' ?></h3>
                        <div class="form-group">
                            <label for="interac_email"><?= $fr ? 'Courriel Virement' : 'e-Transfer Email' ?> <span class="required">*</span></label>
                            <input type="email" id="interac_email" name="interac_email" value="<?= htmlspecialchars($supplier['interac_email'] ?? '') ?>" placeholder="paiements@votreentreprise.ca">
                            <small><?= $fr ? "L'adresse courriel que vous utilisez pour recevoir les virements Interac" : 'The email address you use to receive Interac e-Transfers' ?></small>
                        </div>
                    </div>

                    <!-- Cheque notice -->
                    <div id="chequeFields" class="banking-section" style="display:<?= $pref === 'cheque' ? 'block' : 'none' ?>;">
                        <hr style="margin:16px 0;border:none;border-top:1px solid #e5e7eb;">
                        <div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:14px 18px;border-radius:6px;">
                            <p style="margin:0;font-size:14px;color:#92400e;">
                                <?= $fr ? '<strong>Les paiements par chèque</strong> seront envoyés par courrier à l\'adresse de votre profil. Veuillez vous assurer que votre adresse ci-dessus est exacte et à jour.' : '<strong>Cheque payments</strong> will be mailed to the address on your profile. Please ensure your street address above is accurate and up to date.' ?>
                            </p>
                        </div>
                    </div>

                    <div id="bankingMessage" class="alert" style="display: none;"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?= $fr ? 'Enregistrer les informations de paiement' : 'Save Payment Info' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Subscription Plan (read-only) -->
        <?php
        $settingsPkg = $supplier['subscription_package'] ?? 'Essential';
        $settingsPkgColors = ['Essential'=>'#00b207','Experience'=>'#3b82f6','Prestige'=>'#7c3aed','Enterprise'=>'#1f2937'];
        $settingsPkgColor  = $settingsPkgColors[$settingsPkg] ?? '#00b207';
        $settingsCommission = $supplier['commission_rate'] ?? '12.00';
        $settingsPkgFeatures = $fr ? [
            'Essential'  => ["Jusqu'à 50 produits", 'Placement standard', 'Soutien par courriel', 'Paiement hebdomadaire'],
            'Experience' => ["Jusqu'à 200 produits", 'Placement amélioré', 'Soutien prioritaire', 'Paiement hebdomadaire'],
            'Prestige'   => ["Jusqu'à 500 produits", 'Placement premium', 'Gestionnaire de compte dédié', 'Paiement bimensuel'],
            'Enterprise' => ['Produits illimités', 'Placement prioritaire', 'Intégration sur mesure', 'Calendrier personnalisé'],
        ] : [
            'Essential'  => ['Up to 50 products', 'Standard search placement', 'Email support', 'Weekly payout'],
            'Experience' => ['Up to 200 products', 'Enhanced search placement', 'Priority email support', 'Weekly payout'],
            'Prestige'   => ['Up to 500 products', 'Premium search placement', 'Dedicated account manager', 'Bi-weekly payout'],
            'Enterprise' => ['Unlimited products', 'Top search placement', 'White-glove onboarding', 'Custom payout schedule'],
        ];
        $currentFeatures = $settingsPkgFeatures[$settingsPkg] ?? $settingsPkgFeatures['Essential'];
        ?>
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-star"></i> <?= $fr ? "Forfait d'abonnement" : 'Subscription Plan' ?></h2>
            </div>
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="display:inline-flex;align-items:center;gap:7px;background:<?= $settingsPkgColor ?>;color:white;padding:6px 18px;border-radius:20px;font-size:14px;font-weight:700;letter-spacing:.5px;">
                            <i class="fas fa-star" style="font-size:11px;"></i> <?= htmlspecialchars($settingsPkg) ?>
                        </span>
                        <span style="font-size:13px;color:#6b7280;"><?= $settingsCommission ?>% <?= $fr ? 'taux de commission' : 'commission rate' ?></span>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;margin-bottom:20px;">
                    <?php foreach ($currentFeatures as $feature): ?>
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;">
                        <i class="fas fa-check-circle" style="color:<?= $settingsPkgColor ?>;flex-shrink:0;"></i>
                        <?= htmlspecialchars($feature) ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="border-top:1px solid #e5e7eb;padding-top:16px;">
                    <p style="font-size:13px;color:#6b7280;margin:0 0 12px;"><?= $fr ? 'Vous souhaitez changer de forfait ? Contactez notre équipe de succès fournisseur.' : 'Want to upgrade or change your plan? Contact our seller success team.' ?></p>
                    <a href="mailto:sellers@ocsapp.ca?subject=Package%20Upgrade%20Request%20-%20<?= urlencode($settingsPkg) ?>" style="display:inline-flex;align-items:center;gap:6px;background:#00b207;color:white;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="fas fa-arrow-up"></i> <?= $fr ? 'Demander une mise à niveau' : 'Request Upgrade' ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications & Sound -->
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-bell"></i> <?= $fr ? 'Notifications et son' : 'Notifications &amp; Sound' ?></h2>
            </div>
            <div class="card-body">

                <!-- Sound toggle -->
                <div class="notif-pref-row">
                    <div class="notif-pref-info">
                        <div class="notif-pref-label"><i class="fas fa-volume-up"></i> <?= $fr ? 'Sons de notification' : 'Notification Sounds' ?></div>
                        <div class="notif-pref-desc"><?= $fr ? "Jouer un son lorsqu'une nouvelle commande ou alerte arrive." : 'Play a chime when a new order or alert arrives.' ?></div>
                    </div>
                    <label class="toggle-switch" title="Toggle notification sounds">
                        <input type="checkbox" id="soundToggle" onchange="onSoundToggle(this.checked)">
                        <span class="toggle-track"></span>
                    </label>
                </div>

                <!-- Test sound button -->
                <div style="margin-top:8px;margin-bottom:20px;">
                    <button type="button" class="btn-test-sound" onclick="testSound()">
                        <i class="fas fa-play-circle"></i> <?= $fr ? 'Tester le son' : 'Test Sound' ?>
                    </button>
                    <span id="testSoundMsg" style="margin-left:10px;font-size:13px;color:#6b7280;display:none;"></span>
                </div>

                <hr style="border:none;border-top:1px solid #e5e7eb;margin:0 0 20px;">

                <!-- Browser Notifications toggle -->
                <div class="notif-pref-row">
                    <div class="notif-pref-info">
                        <div class="notif-pref-label"><i class="fas fa-desktop"></i> <?= $fr ? 'Notifications du navigateur' : 'Browser Notifications' ?></div>
                        <div class="notif-pref-desc"><?= $fr ? "Afficher une fenêtre contextuelle même lorsque vous regardez un autre onglet." : "Show a system popup even when you're looking at another tab." ?></div>
                    </div>
                    <span id="browserNotifStatus" class="notif-status-badge"></span>
                </div>
                <div id="browserNotifAction" style="margin-top:10px;"></div>

            </div>
        </div>

        <!-- Change Password -->
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-lock"></i> <?= $fr ? 'Modifier le mot de passe' : 'Change Password' ?></h2>
            </div>
            <div class="card-body">
                <form id="passwordForm">
                    <?= csrfField() ?>

                    <div class="form-group">
                        <label for="current_password"><?= $fr ? 'Mot de passe actuel' : 'Current Password' ?> <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="current_password" name="current_password" required>
                            <button type="button" class="password-toggle" data-target="current_password" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="new_password"><?= $fr ? 'Nouveau mot de passe' : 'New Password' ?> <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="new_password" name="new_password" required minlength="8">
                            <button type="button" class="password-toggle" data-target="new_password" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="pw-rules-box" id="pwRulesBox"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password"><?= $fr ? 'Confirmer le nouveau mot de passe' : 'Confirm New Password' ?> <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                            <button type="button" class="password-toggle" data-target="confirm_password" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="pw-match-error" id="pwMatchError"><?= $fr ? 'Les mots de passe ne correspondent pas.' : 'Passwords do not match.' ?></div>
                    </div>

                    <div id="passwordMessage" class="alert" style="display: none;"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?= $fr ? 'Mettre à jour le mot de passe' : 'Update Password' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const messageDiv = document.getElementById('passwordMessage');
    const submitBtn = e.target.querySelector('button[type="submit"]');

    // Client-side validation
    if (newPassword.length < 8) {
        showMessage('passwordMessage', <?= json_encode($fr ? 'Le nouveau mot de passe doit contenir au moins 8 caractères.' : 'New password must be at least 8 characters.') ?>, 'error');
        return;
    }

    if (newPassword !== confirmPassword) {
        showMessage('passwordMessage', <?= json_encode($fr ? 'Les mots de passe ne correspondent pas.' : 'New passwords do not match.') ?>, 'error');
        return;
    }

    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';

    try {
        const formData = new FormData(this);
        const response = await fetch('<?= url('supplier/update-password') ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showMessage('passwordMessage', data.message, 'success');
            // Clear form
            document.getElementById('passwordForm').reset();
        } else {
            showMessage('passwordMessage', data.message || <?= json_encode($fr ? 'Erreur lors de la mise à jour du mot de passe.' : 'Error updating password.') ?>, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage('passwordMessage', <?= json_encode($fr ? 'Une erreur est survenue. Veuillez réessayer.' : 'An error occurred. Please try again.') ?>, 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> <?= addslashes($t['sup_update_password'] ?? 'Update Password') ?>';
    }
});

function showMessage(targetId, message, type) {
    const messageDiv = document.getElementById(targetId);
    messageDiv.textContent = message;
    messageDiv.className = 'alert alert-' + type;
    messageDiv.style.display = 'block';

    if (type === 'success') {
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    }
}

// ── Password rules checker ───────────────────────────────────────────────────
(function() {
  var pwRules = [
    { id: 'rule-length',  test: function(pw) { return pw.length >= 8; },             label: <?= json_encode($fr ? '8 caractères minimum' : 'At least 8 characters') ?> },
    { id: 'rule-upper',   test: function(pw) { return /[A-Z]/.test(pw); },           label: <?= json_encode($fr ? 'Une lettre majuscule (A-Z)' : 'One uppercase letter (A-Z)') ?> },
    { id: 'rule-lower',   test: function(pw) { return /[a-z]/.test(pw); },           label: <?= json_encode($fr ? 'Une lettre minuscule (a-z)' : 'One lowercase letter (a-z)') ?> },
    { id: 'rule-number',  test: function(pw) { return /[0-9]/.test(pw); },           label: <?= json_encode($fr ? 'Un chiffre (0-9)' : 'One number (0-9)') ?> },
    { id: 'rule-special', test: function(pw) { return /[^A-Za-z0-9]/.test(pw); },   label: <?= json_encode($fr ? 'Un caractère spécial (!@#$%^&*)' : 'One special character (!@#$%^&*)') ?> },
  ];

  var box = document.getElementById('pwRulesBox');
  var newPwInput = document.getElementById('new_password');
  var confirmInput = document.getElementById('confirm_password');
  var matchError = document.getElementById('pwMatchError');

  // Build rule elements immediately so they're always visible
  pwRules.forEach(function(r) {
    var el = document.createElement('div');
    el.id = r.id;
    el.className = 'pw-rule pw-rule-fail';
    el.innerHTML = '<i class="fas fa-circle-xmark"></i> ' + r.label;
    box.appendChild(el);
  });

  function updateRules(pw) {
    pwRules.forEach(function(r) {
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

// Profile form submit
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';

    try {
        const formData = new FormData(this);
        const response = await fetch('<?= url('supplier/update-profile') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            showMessage('profileMessage', data.message, 'success');
        } else {
            showMessage('profileMessage', data.message || 'Error updating profile', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage('profileMessage', 'An error occurred. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> <?= addslashes($t['sup_save_profile'] ?? 'Save Profile') ?>';
    }
});

// ── Address Autocomplete (Nominatim) ──
(function() {
    const addressInput = document.getElementById('address');
    const suggestionsBox = document.getElementById('addressSuggestions');
    const cityInput = document.getElementById('city');
    const provinceSelect = document.getElementById('province');
    const postalInput = document.getElementById('postal_code');
    const countryInput = document.getElementById('country');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    // Province name → code mapping
    const provinceMap = {
        'alberta': 'AB', 'british columbia': 'BC', 'manitoba': 'MB',
        'new brunswick': 'NB', 'newfoundland and labrador': 'NL',
        'nova scotia': 'NS', 'northwest territories': 'NT',
        'nunavut': 'NU', 'ontario': 'ON', 'prince edward island': 'PE',
        'quebec': 'QC', 'québec': 'QC', 'saskatchewan': 'SK', 'yukon': 'YT'
    };

    let debounceTimer = null;
    let highlightedIndex = -1;
    let currentResults = [];

    addressInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 3) {
            hideSuggestions();
            return;
        }

        debounceTimer = setTimeout(() => searchAddress(query), 350);
    });

    // Keyboard navigation
    addressInput.addEventListener('keydown', function(e) {
        if (!suggestionsBox.classList.contains('active')) return;
        const items = suggestionsBox.querySelectorAll('.address-suggestion-item');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            highlightedIndex = Math.min(highlightedIndex + 1, items.length - 1);
            updateHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            highlightedIndex = Math.max(highlightedIndex - 1, 0);
            updateHighlight(items);
        } else if (e.key === 'Enter' && highlightedIndex >= 0) {
            e.preventDefault();
            selectSuggestion(currentResults[highlightedIndex]);
        } else if (e.key === 'Escape') {
            hideSuggestions();
        }
    });

    function updateHighlight(items) {
        items.forEach((item, i) => {
            item.classList.toggle('highlighted', i === highlightedIndex);
        });
        if (items[highlightedIndex]) {
            items[highlightedIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    // EN abbreviations + FR abbreviations
    const abbreviations = {
        // English
        'hwy': 'Highway', 'blvd': 'Boulevard', 'ave': 'Avenue',
        'st': 'Street', 'dr': 'Drive', 'rd': 'Road', 'crt': 'Court',
        'cres': 'Crescent', 'pl': 'Place', 'ln': 'Lane', 'pkwy': 'Parkway',
        'cir': 'Circle', 'terr': 'Terrace', 'ct': 'Court',
        // French
        'aut': 'Autoroute', 'ch': 'Chemin', 'boul': 'Boulevard',
        'rte': 'Route', 'mtee': 'Montée', 'cote': 'Côte'
    };

    // Bilingual road name translations (EN→FR for Quebec OSM data)
    const bilingualNames = {
        'trans-canada': 'Transcanadienne',
        'trans canada': 'Transcanadienne'
    };

    function expandAbbreviations(q) {
        return q.replace(/\b(\w+)\b/g, (match) => abbreviations[match.toLowerCase()] || match);
    }

    function translateToFrench(q) {
        let translated = q;
        // Expand abbreviations first
        translated = expandAbbreviations(translated);
        // Apply bilingual name swaps
        const lower = translated.toLowerCase();
        for (const [en, fr] of Object.entries(bilingualNames)) {
            if (lower.includes(en)) {
                translated = translated.replace(new RegExp(en, 'gi'), fr);
            }
        }
        // "Highway" → "Autoroute" for Quebec context
        translated = translated.replace(/\bHighway\b/gi, 'Autoroute');
        return translated;
    }

    async function nominatimSearch(query, viewbox) {
        const params = new URLSearchParams({
            q: query,
            format: 'json',
            addressdetails: 1,
            limit: 8
        });
        // Bias results toward Quebec/Eastern Canada without hard country lock
        if (viewbox) {
            params.set('viewbox', viewbox);
            params.set('bounded', 0); // prefer but don't exclude
        }
        const response = await fetch('https://nominatim.openstreetmap.org/search?' + params, {
            headers: { 'User-Agent': 'OCSAPP-Supplier/1.0' }
        });
        const data = await response.json();
        // Filter to Canada only
        return data.filter(r => r.address && r.address.country_code === 'ca');
    }

    // Viewbox covering Quebec + Eastern Ontario + Maritimes
    const CANADA_VIEWBOX = '-80.0,42.0,-57.0,62.0';

    async function searchAddress(query) {
        suggestionsBox.innerHTML = '<div class="address-loading"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
        suggestionsBox.classList.add('active');

        try {
            // Strategy 1: search with Quebec context first
            let data = await nominatimSearch(query + ', Quebec', CANADA_VIEWBOX);

            // Strategy 2: try French translation (trans canada hwy → Autoroute Transcanadienne)
            if (data.length === 0) {
                const french = translateToFrench(query);
                if (french !== query) {
                    data = await nominatimSearch(french + ', Quebec', CANADA_VIEWBOX);
                }
            }

            // Strategy 3: expand abbreviations (hwy→Highway, aut→Autoroute, etc.)
            if (data.length === 0) {
                const expanded = expandAbbreviations(query);
                if (expanded !== query) {
                    data = await nominatimSearch(expanded + ', Quebec', CANADA_VIEWBOX);
                }
            }

            // Strategy 4: broaden to all of Canada
            if (data.length === 0) {
                data = await nominatimSearch(query, CANADA_VIEWBOX);
            }

            // Strategy 5: abbreviation expansion + all Canada
            if (data.length === 0) {
                const expanded = expandAbbreviations(query);
                if (expanded !== query) {
                    data = await nominatimSearch(expanded, CANADA_VIEWBOX);
                }
            }

            currentResults = data;
            highlightedIndex = -1;

            if (data.length === 0) {
                suggestionsBox.innerHTML = '<div class="address-loading">No results found — try adding the city name</div>';
                return;
            }

            suggestionsBox.innerHTML = data.map((item, i) => {
                const addr = item.address || {};
                const street = [addr.house_number, addr.road].filter(Boolean).join(' ');
                const city = addr.city || addr.town || addr.village || addr.municipality || '';
                const province = addr.state || '';
                return `
                    <div class="address-suggestion-item" data-index="${i}">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <div class="suggestion-main">${street || item.display_name.split(',')[0]}</div>
                            <div class="suggestion-detail">${[city, province, addr.postcode].filter(Boolean).join(', ')}</div>
                        </div>
                    </div>
                `;
            }).join('');

            // Click handlers
            suggestionsBox.querySelectorAll('.address-suggestion-item').forEach(item => {
                item.addEventListener('click', function() {
                    const index = parseInt(this.dataset.index);
                    selectSuggestion(currentResults[index]);
                });
            });

        } catch (error) {
            console.error('Address search error:', error);
            suggestionsBox.innerHTML = '<div class="address-loading">Search failed — try again</div>';
        }
    }

    function selectSuggestion(result) {
        const addr = result.address || {};

        // Street address
        const street = [addr.house_number, addr.road].filter(Boolean).join(' ');
        addressInput.value = street || result.display_name.split(',')[0];

        // City
        const city = addr.city || addr.town || addr.village || addr.municipality || '';
        cityInput.value = city;

        // Province — match to select option
        const stateLower = (addr.state || '').toLowerCase();
        const provinceCode = provinceMap[stateLower] || '';
        if (provinceCode) {
            provinceSelect.value = provinceCode;
        }

        // Postal code
        postalInput.value = addr.postcode || '';

        // Country
        countryInput.value = addr.country || 'Canada';

        // Coordinates
        latInput.value = result.lat || '';
        lngInput.value = result.lon || '';

        hideSuggestions();

        // Brief highlight on filled fields
        [cityInput, provinceSelect, postalInput, countryInput].forEach(el => {
            el.style.transition = 'background 0.3s';
            el.style.background = '#d1fae5';
            setTimeout(() => { el.style.background = ''; }, 1200);
        });
    }

    function hideSuggestions() {
        suggestionsBox.classList.remove('active');
        suggestionsBox.innerHTML = '';
        currentResults = [];
        highlightedIndex = -1;
    }

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!addressInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            hideSuggestions();
        }
    });
})();

// ── Banking form submit ──
document.getElementById('bankingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    try {
        const response = await fetch('<?= url('supplier/update-banking') ?>', {
            method: 'POST',
            body: new FormData(this)
        });
        const data = await response.json();
        showMessage('bankingMessage', data.message || (data.success ? 'Saved!' : 'Error'), data.success ? 'success' : 'error');
    } catch (err) {
        showMessage('bankingMessage', 'An error occurred. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> <?= addslashes($t['sup_save_payment'] ?? 'Save Payment Info') ?>';
    }
});

// ── Payment preference toggle ──
document.querySelectorAll('input[name="payment_preference"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.banking-section').forEach(function(s) { s.style.display = 'none'; });
        document.querySelectorAll('.pref-option').forEach(function(o) { o.classList.remove('active'); });
        this.closest('.pref-option').classList.add('active');
        const map = { eft: 'eftFields', interac: 'interacFields', cheque: 'chequeFields' };
        if (map[this.value]) document.getElementById(map[this.value]).style.display = 'block';
    });
});

// Password visibility toggles
document.querySelectorAll('.password-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const input = document.getElementById(this.dataset.target);
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            this.setAttribute('aria-label', 'Hide password');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            this.setAttribute('aria-label', 'Show password');
        }
    });
});
</script>

<script>
// ── Notification & Sound Settings ────────────────────────────────────────────
var _ns = {
  soundPlayed:      <?= json_encode($fr ? 'Son joué !' : 'Sound played!') ?>,
  soundUnavailable: <?= json_encode($fr ? 'Fonction sonore non disponible.' : 'Sound function not available.') ?>,
  notSupported:     <?= json_encode($fr ? 'Non pris en charge' : 'Not supported') ?>,
  notSupportedMsg:  <?= json_encode($fr ? 'Votre navigateur ne prend pas en charge les notifications.' : 'Your browser does not support desktop notifications.') ?>,
  enabled:          <?= json_encode($fr ? 'Activées' : 'Enabled') ?>,
  enabledMsg:       <?= json_encode($fr ? "Vous recevrez des notifications pour les nouvelles commandes et alertes." : "You'll receive desktop notifications for new orders and alerts.") ?>,
  blocked:          <?= json_encode($fr ? 'Bloquées' : 'Blocked') ?>,
  blockedMsg:       <?= json_encode($fr ? "Notifications bloquées. Allez dans les paramètres du site de votre navigateur pour les autoriser." : "Notifications are blocked. Go to your browser's site settings to allow them for this site.") ?>,
  notEnabled:       <?= json_encode($fr ? 'Non activées' : 'Not enabled') ?>,
  enableBtn:        <?= json_encode($fr ? 'Activer les notifications' : 'Enable Browser Notifications') ?>,
};
(function initNotifSettings() {

  // ── Sound toggle ────────────────────────────────────────────────────────────
  var soundToggle = document.getElementById('soundToggle');
  var soundEnabled = localStorage.getItem('sup_sound_enabled') !== 'off'; // on by default
  soundToggle.checked = soundEnabled;

  window.onSoundToggle = function(checked) {
    localStorage.setItem('sup_sound_enabled', checked ? 'on' : 'off');
    // When enabling, also unlock AudioContext + dismiss the enable banner
    if (checked) {
      if (typeof _unlockAudio === 'function') _unlockAudio();
      localStorage.setItem('sup_notif_enabled', '1');
      var banner = document.getElementById('supNotifEnableBanner');
      if (banner) banner.style.display = 'none';
    }
  };

  // ── Test sound ──────────────────────────────────────────────────────────────
  window.testSound = function() {
    var msg = document.getElementById('testSoundMsg');
    // Unlock audio on this gesture (required by browser)
    if (typeof _unlockAudio === 'function') _unlockAudio();
    localStorage.setItem('sup_notif_enabled', '1');
    localStorage.setItem('sup_sound_enabled', 'on');
    soundToggle.checked = true;
    var banner = document.getElementById('supNotifEnableBanner');
    if (banner) banner.style.display = 'none';

    if (typeof playChimeFooter === 'function') {
      playChimeFooter();
      msg.textContent = _ns.soundPlayed;
    } else if (typeof window.playChime === 'function') {
      window.playChime();
      msg.textContent = _ns.soundPlayed;
    } else {
      msg.textContent = _ns.soundUnavailable;
    }
    msg.style.display = 'inline';
    setTimeout(function() { msg.style.display = 'none'; }, 2500);
  };

  // ── Browser notification status ─────────────────────────────────────────────
  var statusBadge  = document.getElementById('browserNotifStatus');
  var actionArea   = document.getElementById('browserNotifAction');

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
    Notification.requestPermission().then(function(result) {
      renderNotifStatus();
    });
  };

  renderNotifStatus();

})();
</script>

<?php include __DIR__ . '/layout-footer.php'; ?>
