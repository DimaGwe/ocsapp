<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$translations = [
    'en' => [
        'page_title'              => 'Create account - Business Central - OCSAPP',
        'hero_eyebrow'            => 'BUSINESS CENTRAL',
        'hero_p'                  => 'Register your organization for procurement and distribution services through Business Central.',
        'card_kicker'             => 'BUSINESS APPLICATION',
        'card_h2'                 => 'Register your business',
        'card_p'                  => 'Provide your business, Québec registration and account information for review.',
        'sec_company'             => 'Company Information',
        'sec_company_desc'        => 'Tell us about your business.',
        'company_name'            => 'Company Name',
        'company_ph'              => 'Enter your company name',
        'sec_legal'               => 'Quebec Legal Identity Verification',
        'sec_legal_desc'          => 'Provide your Quebec business registration details for verification.',
        'legal_info'              => 'Business records are verified through the',
        'legal_info2'             => 'Your NEQ can be found on your business registration documents.',
        'neq_label'               => 'NEQ (Enterprise Number)',
        'neq_hint'                => '10-digit identification number from the Registraire des entreprises',
        'legal_name'              => 'Legal Name',
        'legal_name_ph'           => 'Full legal name as registered',
        'legal_name_hint'         => 'Must comply with the Charter of the French Language',
        'operating_names'         => 'Operating Name(s)',
        'operating_names_ph'      => 'Trade names or DBA names (if different from legal name)',
        'operating_names_hint'    => 'Separate multiple names with commas',
        'reg_address'             => 'Registered Office Address',
        'reg_street_ph'           => 'Street address',
        'reg_city_ph'             => 'Montreal',
        'reg_province'            => 'Province',
        'reg_postal'              => 'Postal Code',
        'sec_docs'                => 'Verification Documents',
        'sec_docs_desc'           => 'You can upload your business registration document now or later from your portal. A document is required before your account can be fully approved.',
        'doc_cert'                => 'Certificate of Incorporation / Declaration of Registration',
        'doc_cert_hint'           => 'PDF, JPG, or PNG - max 5MB.',
        'doc_later'               => 'Skipping for now? You can upload it anytime from your portal settings.',
        'doc_upload'              => 'Click to upload',
        'doc_drag'                => 'or drag and drop',
        'doc_secure'              => 'Your documents are securely stored and only used for verification purposes.',
        'sec_contact'             => 'Contact Person',
        'sec_contact_desc'        => 'Who is the primary contact for this account?',
        'first_name'              => 'First Name',
        'first_name_ph'           => 'First name',
        'last_name'               => 'Last Name',
        'last_name_ph'            => 'Last name',
        'email'                   => 'Email',
        'phone'                   => 'Phone',
        'sec_address'             => 'Delivery Address',
        'sec_address_desc'        => 'Where should deliveries be made?',
        'street'                  => 'Street Address',
        'street_ph'               => '123 Business Street',
        'city'                    => 'City',
        'city_ph'                 => 'Montreal',
        'province'                => 'Province',
        'select_province'         => 'Select Province',
        'postal_code'             => 'Postal Code',
        'sec_security'            => 'Create Your Account Password',
        'sec_security_desc'       => 'Set a strong password to protect your account.',
        'password'                => 'Password',
        'password_ph'             => 'Minimum 10 characters',
        'confirm_password'        => 'Confirm Password',
        'confirm_ph'              => 'Re-enter your password',
        'pw_match_error'          => 'Passwords do not match',
        'terms_agree'             => 'I agree to the',
        'terms_link'              => 'Terms of Service',
        'and'                     => 'and',
        'privacy_link'            => 'Privacy Policy',
        'submit'                  => 'Submit Application',
        'already_account'         => 'Already have an account?',
        'sign_in'                 => 'Sign In',
        'back_dist'               => 'Back to Distribution Portal',
        'same_as_registered'      => 'Same as registered office address',
        'pw_rule_length'          => 'At least 10 characters',
        'pw_rule_upper'           => 'One uppercase letter (A-Z)',
        'pw_rule_lower'           => 'One lowercase letter (a-z)',
        'pw_rule_number'          => 'One number (0-9)',
        'pw_rule_special'         => 'One special character (!@#$%^&*)',
    ],
    'fr' => [
        'page_title'              => 'Créer un compte - Entreprise Centrale - OCSAPP',
        'hero_eyebrow'            => 'ENTREPRISE CENTRALE',
        'hero_p'                  => 'Inscrivez votre organisation pour les services d\'approvisionnement et de distribution via Entreprise Centrale.',
        'card_kicker'             => 'DEMANDE ENTREPRISE',
        'card_h2'                 => 'Inscrire votre entreprise',
        'card_p'                  => 'Fournissez les informations sur votre entreprise, votre immatriculation au Québec et votre compte pour révision.',
        'sec_company'             => 'Informations sur l\'entreprise',
        'sec_company_desc'        => 'Parlez-nous de votre entreprise.',
        'company_name'            => 'Nom de l\'entreprise',
        'company_ph'              => 'Entrez le nom de votre entreprise',
        'sec_legal'               => 'Vérification d\'identité juridique (Québec)',
        'sec_legal_desc'          => 'Fournissez vos coordonnées d\'inscription au Québec pour vérification.',
        'legal_info'              => 'Les dossiers d\'entreprise sont vérifiés auprès du',
        'legal_info2'             => 'Votre NEQ se trouve sur vos documents d\'immatriculation.',
        'neq_label'               => 'NEQ (Numéro d\'entreprise du Québec)',
        'neq_hint'                => 'Numéro à 10 chiffres du Registraire des entreprises',
        'legal_name'              => 'Nom légal',
        'legal_name_ph'           => 'Nom légal complet tel qu\'inscrit',
        'legal_name_hint'         => 'Doit être conforme à la Charte de la langue française',
        'operating_names'         => 'Nom(s) commercial(aux)',
        'operating_names_ph'      => 'Noms commerciaux ou DBA (si différents du nom légal)',
        'operating_names_hint'    => 'Séparez plusieurs noms par des virgules',
        'reg_address'             => 'Adresse du siège social',
        'reg_street_ph'           => 'Adresse',
        'reg_city_ph'             => 'Montréal',
        'reg_province'            => 'Province',
        'reg_postal'              => 'Code postal',
        'sec_docs'                => 'Documents de vérification',
        'sec_docs_desc'           => 'Vous pouvez télécharger votre document d\'immatriculation maintenant ou plus tard depuis votre portail. Un document est requis avant que votre compte puisse être entièrement approuvé.',
        'doc_cert'                => 'Certificat de constitution / Déclaration d\'immatriculation',
        'doc_cert_hint'           => 'PDF, JPG ou PNG - max 5 Mo.',
        'doc_later'               => 'Vous passez pour l\'instant? Vous pouvez le télécharger à tout moment depuis les paramètres de votre portail.',
        'doc_upload'              => 'Cliquez pour télécharger',
        'doc_drag'                => 'ou glissez-déposez',
        'doc_secure'              => 'Vos documents sont stockés de façon sécurisée et utilisés uniquement à des fins de vérification.',
        'sec_contact'             => 'Personne contact',
        'sec_contact_desc'        => 'Qui est le contact principal pour ce compte?',
        'first_name'              => 'Prénom',
        'first_name_ph'           => 'Prénom',
        'last_name'               => 'Nom de famille',
        'last_name_ph'            => 'Nom de famille',
        'email'                   => 'Adresse courriel',
        'phone'                   => 'Numéro de téléphone',
        'sec_address'             => 'Adresse de livraison',
        'sec_address_desc'        => 'À quelle adresse les livraisons doivent-elles être effectuées?',
        'street'                  => 'Adresse',
        'street_ph'               => '123 rue des Affaires',
        'city'                    => 'Ville',
        'city_ph'                 => 'Montréal',
        'province'                => 'Province',
        'select_province'         => 'Sélectionnez une province',
        'postal_code'             => 'Code postal',
        'sec_security'            => 'Créer votre mot de passe',
        'sec_security_desc'       => 'Définissez un mot de passe solide pour protéger votre compte.',
        'password'                => 'Mot de passe',
        'password_ph'             => 'Minimum 10 caractères',
        'confirm_password'        => 'Confirmer le mot de passe',
        'confirm_ph'              => 'Répétez votre mot de passe',
        'pw_match_error'          => 'Les mots de passe ne correspondent pas',
        'terms_agree'             => 'J\'accepte les',
        'terms_link'              => 'Conditions d\'utilisation',
        'and'                     => 'et la',
        'privacy_link'            => 'Politique de confidentialité',
        'submit'                  => 'Soumettre la demande',
        'already_account'         => 'Vous avez déjà un compte?',
        'sign_in'                 => 'Se connecter',
        'back_dist'               => 'Retour au Portail de Distribution',
        'same_as_registered'      => 'Même adresse que le siège social',
        'pw_rule_length'          => '10 caractères minimum',
        'pw_rule_upper'           => 'Une lettre majuscule (A-Z)',
        'pw_rule_lower'           => 'Une lettre minuscule (a-z)',
        'pw_rule_number'          => 'Un chiffre (0-9)',
        'pw_rule_special'         => 'Un caractère spécial (!@#$%^&*)',
    ],
];
$tr = $translations[$currentLang] ?? $translations['en'];
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#00B207">
    <title><?= $tr['page_title'] ?></title>
    <?= csrfMeta() ?>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/pages/portal-register.css') ?>">
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; }

        /* ── Leftover component styling not covered by portal-register.css ── */
        .form-group label .required { color: #ef4444; }
        .form-group input.error, .form-group select.error { border-color: #ef4444 !important; }
        .error-text { color: #ef4444; font-size: 12px; margin-top: 4px; }

        .neq-input-wrapper input { padding-right: 52px !important; }
        .neq-counter {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            font-size: 12px; color: #9ca3af; pointer-events: none;
        }

        .pw-input-wrap input { padding-right: 44px !important; }
        .pw-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 16px; padding: 0; line-height: 1;
        }
        .pw-toggle:hover { color: #6b7280; }
        .pw-rule { font-size: 13px; display: flex; align-items: center; gap: 6px; padding: 2px 0; transition: color 0.2s; }
        .pw-rule-fail { color: #9ca3af; }
        .pw-rule-ok   { color: #00b207; }
        .pw-rule i    { font-size: 12px; }

        .doc-upload-area { position: relative; cursor: pointer; }
        .doc-upload-area input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        .doc-upload-icon { font-size: 28px; color: #9ca3af; margin-bottom: 8px; }
        .doc-upload-area.has-file .doc-upload-icon { color: #00b207; }
        .doc-upload-text { font-size: 14px; color: #6b7280; }
        .doc-upload-text strong { color: #374151; }
        .doc-file-name { margin-top: 6px; font-size: 13px; font-weight: 600; word-break: break-all; }

        .form-group input:disabled,
        .form-group select:disabled {
            background: #f3f4f6; color: #9ca3af; border-color: #e5e7eb !important; cursor: not-allowed;
        }

        .submit-section { padding-top: 8px; }
        .btn-submit .spinner {
            display: none; width: 20px; height: 20px;
            border: 2px solid rgba(255,255,255,0.3); border-top-color: white;
            border-radius: 50%; animation: spin 0.6s linear infinite;
        }
        .btn-submit.loading .spinner { display: inline-block; }
        .btn-submit.loading .btn-text { display: none; }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 640px) {
            .doc-upload-area { padding: 20px 12px; }
        }
    </style>
</head>
<body>
<header class="auth-topbar">
  <a class="auth-logo" href="<?= url('/') ?>" aria-label="OCSAPP">OCSAPP</a>
  <nav class="auth-toplinks" aria-label="<?= $fr ? "Navigation d'inscription" : 'Registration navigation' ?>">
    <a href="<?= url('/') ?>"><?= $fr ? 'Écosystème' : 'Ecosystem' ?></a>
    <a href="<?= url('marketplace-central') ?>"><?= $fr ? 'Marché Central' : 'Market Central' ?></a>
    <a class="central-link" href="<?= url('distribution') ?>"><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></a>
  </nav>
</header>

<main class="register-shell">
  <section class="brand-panel" aria-label="<?= $fr ? 'Entreprise Centrale' : 'Business Central' ?>">
    <div class="brand-content">
      <div class="eyebrow"><?= $tr['hero_eyebrow'] ?></div>
      <div class="central-icon"><img src="<?= asset('images/about/central-business.png') ?>" alt="<?= $fr ? 'Entreprise Centrale' : 'Business Central' ?>"></div>
      <h1><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></h1>
      <p><?= $tr['hero_p'] ?></p>
      <p class="ecosystem-note"><strong><?= $fr ? 'Un seul écosystème OCSAPP.' : 'One OCSAPP ecosystem.' ?></strong><br><?= $fr ? 'Votre compte est lié au Central correspondant à votre rôle.' : 'Your account is connected to the Central associated with your role.' ?></p>
    </div>
  </section>

  <section class="form-panel">
    <div class="register-card">
      <div class="card-head">
        <div class="card-kicker"><?= $tr['card_kicker'] ?></div>
        <h2><?= $tr['card_h2'] ?></h2>
        <p><?= $tr['card_p'] ?></p>
      </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($errors['general']) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('distribution/register') ?>" id="registerForm" enctype="multipart/form-data" novalidate>
        <?= csrfField() ?>

        <!-- Section 1: Company Information -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-building"></i> <?= $tr['sec_company'] ?>
            </div>
            <p class="form-section-desc"><?= $tr['sec_company_desc'] ?></p>

            <div class="form-group">
                <label><?= $tr['company_name'] ?> <span class="required">*</span></label>
                <input type="text"
                       name="company_name"
                       value="<?= htmlspecialchars($old['company_name'] ?? '') ?>"
                       class="<?= isset($errors['company_name']) ? 'error' : '' ?>"
                       placeholder="<?= $tr['company_ph'] ?>"
                       required>
                <?php if (isset($errors['company_name'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['company_name']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section 2: Quebec Legal Identity -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-landmark"></i> <?= $tr['sec_legal'] ?>
            </div>
            <p class="form-section-desc"><?= $tr['sec_legal_desc'] ?></p>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <?= $tr['legal_info'] ?>
                <a href="https://www.registreentreprises.gouv.qc.ca" target="_blank" rel="noopener">Registraire des entreprises du Qu&eacute;bec</a>.
                <?= $tr['legal_info2'] ?>
            </div>

            <div class="form-group">
                <label><?= $tr['neq_label'] ?> <span class="required">*</span></label>
                <div class="neq-input-wrapper">
                    <input type="text"
                           name="neq_number"
                           id="neqInput"
                           required
                           maxlength="10"
                           minlength="10"
                           pattern="[0-9]{10}"
                           placeholder="1234567890"
                           value="<?= htmlspecialchars($old['neq_number'] ?? '') ?>"
                           class="<?= isset($errors['neq_number']) ? 'error' : '' ?>"
                           oninput="updateNeqCounter(this)">
                    <span class="neq-counter" id="neqCounter">0/10</span>
                </div>
                <span class="hint"><?= $tr['neq_hint'] ?></span>
                <?php if (isset($errors['neq_number'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['neq_number']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label><?= $tr['legal_name'] ?> <span class="required">*</span></label>
                <input type="text"
                       name="legal_name"
                       required
                       maxlength="255"
                       placeholder="<?= $tr['legal_name_ph'] ?>"
                       value="<?= htmlspecialchars($old['legal_name'] ?? '') ?>"
                       class="<?= isset($errors['legal_name']) ? 'error' : '' ?>">
                <span class="hint"><?= $tr['legal_name_hint'] ?></span>
                <?php if (isset($errors['legal_name'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['legal_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label><?= $tr['operating_names'] ?></label>
                <input type="text"
                       name="operating_names"
                       maxlength="500"
                       placeholder="<?= $tr['operating_names_ph'] ?>"
                       value="<?= htmlspecialchars($old['operating_names'] ?? '') ?>">
                <span class="hint"><?= $tr['operating_names_hint'] ?></span>
            </div>

            <div class="form-group">
                <label><?= $tr['reg_address'] ?> <span class="required">*</span></label>
                <input type="text"
                       id="reg-street"
                       name="registered_address_street"
                       required
                       maxlength="255"
                       placeholder="<?= $tr['reg_street_ph'] ?>"
                       value="<?= htmlspecialchars($old['registered_address_street'] ?? '') ?>"
                       class="<?= isset($errors['registered_address_street']) ? 'error' : '' ?>">
                <?php if (isset($errors['registered_address_street'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['registered_address_street']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><?= $tr['city'] ?> <span class="required">*</span></label>
                    <input type="text"
                           id="reg-city"
                           name="registered_address_city"
                           required
                           maxlength="100"
                           placeholder="<?= $tr['reg_city_ph'] ?>"
                           value="<?= htmlspecialchars($old['registered_address_city'] ?? '') ?>"
                           class="<?= isset($errors['registered_address_city']) ? 'error' : '' ?>">
                    <?php if (isset($errors['registered_address_city'])): ?>
                        <div class="error-text"><?= htmlspecialchars($errors['registered_address_city']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label><?= $tr['reg_province'] ?></label>
                    <select id="reg-province" name="registered_address_province">
                        <option value="QC" <?= ($old['registered_address_province'] ?? 'QC') === 'QC' ? 'selected' : '' ?>>Qu&eacute;bec</option>
                        <option value="ON" <?= ($old['registered_address_province'] ?? '') === 'ON' ? 'selected' : '' ?>>Ontario</option>
                        <option value="BC" <?= ($old['registered_address_province'] ?? '') === 'BC' ? 'selected' : '' ?>>British Columbia</option>
                        <option value="AB" <?= ($old['registered_address_province'] ?? '') === 'AB' ? 'selected' : '' ?>>Alberta</option>
                        <option value="MB" <?= ($old['registered_address_province'] ?? '') === 'MB' ? 'selected' : '' ?>>Manitoba</option>
                        <option value="SK" <?= ($old['registered_address_province'] ?? '') === 'SK' ? 'selected' : '' ?>>Saskatchewan</option>
                        <option value="NS" <?= ($old['registered_address_province'] ?? '') === 'NS' ? 'selected' : '' ?>>Nova Scotia</option>
                        <option value="NB" <?= ($old['registered_address_province'] ?? '') === 'NB' ? 'selected' : '' ?>>New Brunswick</option>
                        <option value="NL" <?= ($old['registered_address_province'] ?? '') === 'NL' ? 'selected' : '' ?>>Newfoundland and Labrador</option>
                        <option value="PE" <?= ($old['registered_address_province'] ?? '') === 'PE' ? 'selected' : '' ?>>Prince Edward Island</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="max-width: 180px;">
                <label><?= $tr['reg_postal'] ?> <span class="required">*</span></label>
                <input type="text"
                       id="reg-postal"
                       name="registered_address_postal"
                       required
                       maxlength="10"
                       placeholder="H2X 1Y4"
                       pattern="[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d"
                       value="<?= htmlspecialchars($old['registered_address_postal'] ?? '') ?>"
                       class="<?= isset($errors['registered_address_postal']) ? 'error' : '' ?>">
                <?php if (isset($errors['registered_address_postal'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['registered_address_postal']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section 3: Verification Documents -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-file-alt"></i> <?= $tr['sec_docs'] ?>
                <span style="margin-left:10px;background:#f0fdf4;color:#059669;border:1px solid #bbf7d0;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;vertical-align:middle;">Optional</span>
            </div>
            <p class="form-section-desc"><?= $tr['sec_docs_desc'] ?></p>

            <div class="doc-upload-group">
                <label><?= $tr['doc_cert'] ?></label>
                <div class="doc-upload-area" id="dropArea1">
                    <input type="file"
                           name="doc_certificate"
                           id="doc_certificate"
                           accept=".pdf,.jpg,.jpeg,.png"
                           onchange="handleFileSelect(this, 'dropArea1')">
                    <div class="doc-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div class="doc-upload-text">
                        <strong><?= $tr['doc_upload'] ?></strong> <?= $tr['doc_drag'] ?>
                    </div>
                    <div class="doc-file-name" id="fileName1"></div>
                </div>
                <div class="doc-upload-hint"><?= $tr['doc_cert_hint'] ?></div>
                <?php if (isset($errors['doc_certificate'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['doc_certificate']) ?></div>
                <?php endif; ?>
            </div>

            <div class="info-box" style="margin-top: 8px;">
                <p><i class="fas fa-shield-alt"></i> <?= $tr['doc_secure'] ?></p>
                <p style="margin-top:8px;"><i class="fas fa-folder-open"></i> <?= $tr['doc_later'] ?></p>
            </div>
        </div>

        <!-- Section 4: Contact Person -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-user"></i> <?= $tr['sec_contact'] ?>
            </div>
            <p class="form-section-desc"><?= $tr['sec_contact_desc'] ?></p>

            <div class="form-row">
                <div class="form-group">
                    <label><?= $tr['first_name'] ?> <span class="required">*</span></label>
                    <input type="text"
                           name="first_name"
                           value="<?= htmlspecialchars($old['first_name'] ?? '') ?>"
                           class="<?= isset($errors['first_name']) ? 'error' : '' ?>"
                           placeholder="<?= $tr['first_name_ph'] ?>"
                           required>
                    <?php if (isset($errors['first_name'])): ?>
                        <div class="error-text"><?= htmlspecialchars($errors['first_name']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label><?= $tr['last_name'] ?> <span class="required">*</span></label>
                    <input type="text"
                           name="last_name"
                           value="<?= htmlspecialchars($old['last_name'] ?? '') ?>"
                           class="<?= isset($errors['last_name']) ? 'error' : '' ?>"
                           placeholder="<?= $tr['last_name_ph'] ?>"
                           required>
                    <?php if (isset($errors['last_name'])): ?>
                        <div class="error-text"><?= htmlspecialchars($errors['last_name']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><?= $tr['email'] ?> <span class="required">*</span></label>
                    <input type="email"
                           name="email"
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                           class="<?= isset($errors['email']) ? 'error' : '' ?>"
                           placeholder="business@company.com"
                           required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-text"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label><?= $tr['phone'] ?> <span class="required">*</span></label>
                    <input type="tel"
                           name="phone"
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                           class="<?= isset($errors['phone']) ? 'error' : '' ?>"
                           placeholder="(514) 555-0123"
                           required>
                    <?php if (isset($errors['phone'])): ?>
                        <div class="error-text"><?= htmlspecialchars($errors['phone']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Section 5: Delivery Address -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-location-dot"></i> <?= $tr['sec_address'] ?>
            </div>
            <p class="form-section-desc"><?= $tr['sec_address_desc'] ?></p>

            <div class="checkbox-group" style="margin-bottom: 20px;">
                <input type="checkbox" id="sameAsRegistered" onchange="syncDeliveryAddress(this.checked)">
                <label for="sameAsRegistered"><?= $tr['same_as_registered'] ?></label>
            </div>

            <div class="form-group">
                <label><?= $tr['street'] ?> <span class="required">*</span></label>
                <input type="text"
                       id="del-street"
                       name="delivery_street"
                       value="<?= htmlspecialchars($old['delivery_street'] ?? '') ?>"
                       class="<?= isset($errors['delivery_street']) ? 'error' : '' ?>"
                       placeholder="<?= $tr['street_ph'] ?>"
                       required>
                <?php if (isset($errors['delivery_street'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['delivery_street']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><?= $tr['city'] ?> <span class="required">*</span></label>
                    <input type="text"
                           id="del-city"
                           name="delivery_city"
                           value="<?= htmlspecialchars($old['delivery_city'] ?? '') ?>"
                           class="<?= isset($errors['delivery_city']) ? 'error' : '' ?>"
                           placeholder="<?= $tr['city_ph'] ?>"
                           required>
                    <?php if (isset($errors['delivery_city'])): ?>
                        <div class="error-text"><?= htmlspecialchars($errors['delivery_city']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label><?= $tr['province'] ?> <span class="required">*</span></label>
                    <select id="del-province"
                            name="delivery_province"
                            class="<?= isset($errors['delivery_province']) ? 'error' : '' ?>"
                            required>
                        <option value=""><?= $tr['select_province'] ?></option>
                        <option value="QC" <?= ($old['delivery_province'] ?? '') === 'QC' ? 'selected' : '' ?>>Quebec</option>
                        <option value="ON" <?= ($old['delivery_province'] ?? '') === 'ON' ? 'selected' : '' ?>>Ontario</option>
                        <option value="BC" <?= ($old['delivery_province'] ?? '') === 'BC' ? 'selected' : '' ?>>British Columbia</option>
                        <option value="AB" <?= ($old['delivery_province'] ?? '') === 'AB' ? 'selected' : '' ?>>Alberta</option>
                        <option value="MB" <?= ($old['delivery_province'] ?? '') === 'MB' ? 'selected' : '' ?>>Manitoba</option>
                        <option value="SK" <?= ($old['delivery_province'] ?? '') === 'SK' ? 'selected' : '' ?>>Saskatchewan</option>
                        <option value="NS" <?= ($old['delivery_province'] ?? '') === 'NS' ? 'selected' : '' ?>>Nova Scotia</option>
                        <option value="NB" <?= ($old['delivery_province'] ?? '') === 'NB' ? 'selected' : '' ?>>New Brunswick</option>
                        <option value="NL" <?= ($old['delivery_province'] ?? '') === 'NL' ? 'selected' : '' ?>>Newfoundland and Labrador</option>
                        <option value="PE" <?= ($old['delivery_province'] ?? '') === 'PE' ? 'selected' : '' ?>>Prince Edward Island</option>
                        <option value="NT" <?= ($old['delivery_province'] ?? '') === 'NT' ? 'selected' : '' ?>>Northwest Territories</option>
                        <option value="YT" <?= ($old['delivery_province'] ?? '') === 'YT' ? 'selected' : '' ?>>Yukon</option>
                        <option value="NU" <?= ($old['delivery_province'] ?? '') === 'NU' ? 'selected' : '' ?>>Nunavut</option>
                    </select>
                    <?php if (isset($errors['delivery_province'])): ?>
                        <div class="error-text"><?= htmlspecialchars($errors['delivery_province']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group" style="max-width: 180px;">
                <label><?= $tr['postal_code'] ?> <span class="required">*</span></label>
                <input type="text"
                       id="del-postal"
                       name="delivery_postal_code"
                       value="<?= htmlspecialchars($old['delivery_postal_code'] ?? '') ?>"
                       class="<?= isset($errors['delivery_postal_code']) ? 'error' : '' ?>"
                       placeholder="H1A 1A1"
                       maxlength="7"
                       required>
                <?php if (isset($errors['delivery_postal_code'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['delivery_postal_code']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section 6: Password -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fas fa-lock"></i> <?= $tr['sec_security'] ?>
            </div>
            <p class="form-section-desc"><?= $tr['sec_security_desc'] ?></p>

            <div class="form-row">
                <div class="form-group">
                    <label><?= $tr['password'] ?> <span class="required">*</span></label>
                    <div class="pw-input-wrap">
                        <input type="password"
                               name="password"
                               id="distPassword"
                               class="<?= isset($errors['password']) ? 'error' : '' ?>"
                               placeholder="<?= $tr['password_ph'] ?>"
                               minlength="10"
                               required>
                        <button type="button" class="pw-toggle" onclick="togglePassword('distPassword', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-text"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label><?= $tr['confirm_password'] ?> <span class="required">*</span></label>
                    <div class="pw-input-wrap">
                        <input type="password"
                               name="password_confirmation"
                               id="distPasswordConfirm"
                               class="<?= isset($errors['password_confirmation']) ? 'error' : '' ?>"
                               placeholder="<?= $tr['confirm_ph'] ?>"
                               minlength="10"
                               required>
                        <button type="button" class="pw-toggle" onclick="togglePassword('distPasswordConfirm', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password_confirmation'])): ?>
                        <div class="error-text"><?= htmlspecialchars($errors['password_confirmation']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Strength rules -->
            <div id="pwStrengthBox" style="margin-top: 6px; margin-bottom: 10px;"></div>

            <!-- Match error -->
            <p id="passwordMatchError" style="display:none; color:#ef4444; font-size:13px; margin-top:4px;">
                <i class="fas fa-exclamation-circle"></i> <?= $tr['pw_match_error'] ?>
            </p>

            <!-- Terms -->
            <div class="checkbox-group">
                <input type="checkbox" name="terms" id="terms" required <?= isset($old['terms']) && $old['terms'] ? 'checked' : '' ?>>
                <label for="terms">
                    <?= $tr['terms_agree'] ?>
                    <a href="<?= url('terms') ?>" target="_blank"><?= $tr['terms_link'] ?></a>
                    <?= $tr['and'] ?>
                    <a href="<?= url('privacy') ?>" target="_blank"><?= $tr['privacy_link'] ?></a>
                </label>
            </div>
            <?php if (isset($errors['terms'])): ?>
                <div class="error-text" style="margin-top: -12px; margin-bottom: 12px;"><?= htmlspecialchars($errors['terms']) ?></div>
            <?php endif; ?>

            <!-- Submit -->
            <div class="submit-section">
                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="spinner"></span>
                    <span class="btn-text">
                        <i class="fas fa-paper-plane"></i> <?= $tr['submit'] ?>
                    </span>
                </button>
            </div>
        </div>

    </form>
      <div class="apply-links"><a href="<?= url('distribution/login') ?>"><?= $fr ? 'Déjà inscrit? Se connecter' : 'Already registered? Sign in' ?></a></div>
      <div class="security-note"><?= $fr
        ? 'Pour votre sécurité, ne saisissez jamais d\'information de carte de paiement et ne partagez pas un mot de passe existant en dehors des champs OCSAPP désignés.'
        : 'For your security, never enter payment card information or share an existing password outside the designated OCSAPP fields.' ?></div>
      <div class="legal-links">
        <a href="<?= url('privacy') ?>"><?= $fr ? 'Confidentialité' : 'Privacy' ?></a> ·
        <a href="<?= url('terms') ?>"><?= $fr ? 'Conditions' : 'Terms' ?></a> ·
        <a href="<?= url('cookies') ?>"><?= $fr ? 'Cookies' : 'Cookies' ?></a> ·
        <a href="<?= url('returns') ?>"><?= $fr ? 'Retours' : 'Returns' ?></a> ·
        <a href="<?= url('accessibility') ?>"><?= $fr ? 'Accessibilité' : 'Accessibility' ?></a>
      </div>
    </div>
  </section>
</main>

<script>
// NEQ input - digits only + counter
function updateNeqCounter(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    const counter = document.getElementById('neqCounter');
    counter.textContent = input.value.length + '/10';
    counter.style.color = input.value.length === 10 ? '#00b207' : '#9ca3af';
}

// File upload handler
function handleFileSelect(input, areaId) {
    const area = document.getElementById(areaId);
    const fileNameEl = area.querySelector('.doc-file-name');
    const iconEl = area.querySelector('.doc-upload-icon');

    if (input.files && input.files[0]) {
        const file = input.files[0];

        if (file.size > 5 * 1024 * 1024) {
            alert('<?= $currentLang === 'fr' ? 'Le fichier doit faire moins de 5 Mo' : 'File size must be less than 5MB' ?>');
            input.value = '';
            return;
        }

        const ext = file.name.split('.').pop().toLowerCase();
        if (!['pdf', 'jpg', 'jpeg', 'png'].includes(ext)) {
            alert('<?= $currentLang === 'fr' ? 'Seuls les fichiers PDF, JPG et PNG sont acceptés' : 'Only PDF, JPG, and PNG files are allowed' ?>');
            input.value = '';
            return;
        }

        area.classList.add('has-file');
        fileNameEl.textContent = file.name;
        if (iconEl) iconEl.innerHTML = '<i class="fas fa-check-circle"></i>';
    } else {
        area.classList.remove('has-file');
        fileNameEl.textContent = '';
        if (iconEl) iconEl.innerHTML = '<i class="fas fa-cloud-upload-alt"></i>';
    }
}

// Password show/hide toggle
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Password strength rules
const pwRules = [
    { id: 'rule-length',  test: pw => pw.length >= 10,          label: '<?= $tr['pw_rule_length'] ?>' },
    { id: 'rule-upper',   test: pw => /[A-Z]/.test(pw),         label: '<?= $tr['pw_rule_upper'] ?>' },
    { id: 'rule-lower',   test: pw => /[a-z]/.test(pw),         label: '<?= $tr['pw_rule_lower'] ?>' },
    { id: 'rule-number',  test: pw => /[0-9]/.test(pw),         label: '<?= $tr['pw_rule_number'] ?>' },
    { id: 'rule-special', test: pw => /[^A-Za-z0-9]/.test(pw), label: '<?= $tr['pw_rule_special'] ?>' },
];

function buildStrengthUI() {
    const container = document.getElementById('pwStrengthBox');
    if (!container) return;
    container.innerHTML = pwRules.map(r =>
        `<div id="${r.id}" class="pw-rule pw-rule-fail"><i class="fas fa-circle-xmark"></i> ${r.label}</div>`
    ).join('');
}

function updateStrength(pw) {
    pwRules.forEach(r => {
        const el = document.getElementById(r.id);
        if (!el) return;
        const pass = r.test(pw);
        el.className = 'pw-rule ' + (pass ? 'pw-rule-ok' : 'pw-rule-fail');
        el.querySelector('i').className = pass ? 'fas fa-circle-check' : 'fas fa-circle-xmark';
    });
}

document.getElementById('distPassword')?.addEventListener('input', function () {
    updateStrength(this.value);
    const confirm = document.getElementById('distPasswordConfirm');
    if (confirm.value.length > 0) {
        document.getElementById('passwordMatchError').style.display =
            (this.value !== confirm.value) ? 'block' : 'none';
    }
});

document.getElementById('distPasswordConfirm')?.addEventListener('input', function () {
    const pw = document.getElementById('distPassword').value;
    document.getElementById('passwordMatchError').style.display =
        (this.value.length > 0 && this.value !== pw) ? 'block' : 'none';
});

// Form submission validation
document.getElementById('registerForm')?.addEventListener('submit', function (e) {
    // Re-enable synced delivery fields so disabled values are included in POST
    if (document.getElementById('sameAsRegistered')?.checked) {
        ['del-street', 'del-city', 'del-province', 'del-postal'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.disabled = false;
        });
    }

    const pw = document.getElementById('distPassword').value;
    const pwConfirm = document.getElementById('distPasswordConfirm').value;
    const btn = document.getElementById('submitBtn');

    if (pw !== pwConfirm) {
        e.preventDefault();
        document.getElementById('passwordMatchError').style.display = 'block';
        document.getElementById('distPasswordConfirm').focus();
        return;
    }

    const failedRules = pwRules.filter(r => !r.test(pw));
    if (failedRules.length > 0) {
        e.preventDefault();
        document.getElementById('distPassword').focus();
        return;
    }

    document.getElementById('passwordMatchError').style.display = 'none';
    btn.classList.add('loading');
    btn.disabled = true;
});

buildStrengthUI();

// Same-as-registered checkbox
const deliveryFieldMap = [
    ['reg-street',   'del-street'],
    ['reg-city',     'del-city'],
    ['reg-province', 'del-province'],
    ['reg-postal',   'del-postal'],
];

function syncDeliveryAddress(checked) {
    deliveryFieldMap.forEach(([srcId, dstId]) => {
        const src = document.getElementById(srcId);
        const dst = document.getElementById(dstId);
        if (!src || !dst) return;
        if (checked) {
            dst.value = src.value;
            dst.disabled = true;
        } else {
            dst.disabled = false;
        }
    });
}

// Keep delivery in sync while checkbox is active and user edits registered address
deliveryFieldMap.forEach(([srcId]) => {
    document.getElementById(srcId)?.addEventListener('input', function () {
        if (document.getElementById('sameAsRegistered')?.checked) {
            syncDeliveryAddress(true);
        }
    });
    // select uses 'change' not 'input'
    document.getElementById(srcId)?.addEventListener('change', function () {
        if (document.getElementById('sameAsRegistered')?.checked) {
            syncDeliveryAddress(true);
        }
    });
});

// Init NEQ counter if value restored from old input
(function () {
    const neqInput = document.getElementById('neqInput');
    if (neqInput && neqInput.value) {
        updateNeqCounter(neqInput);
    }
})();
</script>

</body>
</html>
