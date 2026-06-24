<?php
/**
 * Supplier Accept Invite - Registration Form
 * Redesigned to match apply.php — uses site header/footer, section-card layout.
 */

$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');

$s = [
    'en' => [
        'page_title'      => 'Accept Supplier Invitation',
        'h1'              => 'Complete Your Application',
        'subtitle'        => 'You\'ve been invited to join OCSAPP as a supplier. Fill out the form below and our team will review your application.',
        'invited_to'      => 'Invitation sent to:',
        'invited_note'    => 'You can use a different email address for your account below.',

        'sec_biz'         => 'Business Information',
        'sec_biz_desc'    => 'Tell us about your company.',
        'sec_contact'     => 'Contact Person',
        'sec_contact_desc'=> 'Primary contact for this supplier account.',
        'sec_address'     => 'Registered Business Address',
        'sec_address_desc'=> 'The official registered address of your business.',
        'sec_docs'        => 'Verification Documents',
        'sec_docs_desc'   => 'Upload at least one document confirming your business registration. You can also add documents later from your supplier portal.',
        'sec_pw'          => 'Create Your Account Password',
        'sec_pw_desc'     => 'Set a password so you can access your supplier portal right after submitting.',

        'biz_name'        => 'Business / Operating Name',
        'biz_name_ph'     => 'ABC Wholesale Inc.',
        'legal_name'      => 'Legal Name',
        'legal_name_ph'   => 'ABC Wholesale Incorporated',
        'legal_name_hint' => 'Official registered legal name of your business',
        'op_names'        => 'Operating / DBA Names',
        'op_names_ph'     => 'e.g. ABC Foods, ABC Direct',
        'op_names_hint'   => 'Other names you operate under, comma-separated',
        'neq'             => 'NEQ Number',
        'neq_ph'          => '1234567890',
        'neq_hint'        => '10-digit Quebec Enterprise Number',
        'email_lbl'       => 'Account Email',
        'email_ph'        => 'you@example.com',
        'email_hint'      => 'Pre-filled from your invitation — change if you prefer a different login email',
        'first_name'      => 'First Name',
        'first_name_ph'   => 'Jane',
        'last_name'       => 'Last Name',
        'last_name_ph'    => 'Smith',
        'phone'           => 'Phone Number',
        'phone_ph'        => '(514) 123-4567',
        'street'          => 'Street Address',
        'street_ph'       => '123 Commerce Street',
        'city'            => 'City',
        'city_ph'         => 'Montreal',
        'province'        => 'Province',
        'province_ph'     => 'Select Province',
        'postal'          => 'Postal Code',
        'postal_ph'       => 'H1A 2B3',
        'country'         => 'Country',

        'doc_cert'        => 'Certificate of Incorporation',
        'doc_cert_hint'   => 'For corporations. PDF, JPG or PNG (max 5MB)',
        'doc_decl'        => 'Declaration of Registration',
        'doc_decl_hint'   => 'For sole proprietorships / partnerships. PDF, JPG or PNG (max 5MB)',
        'doc_reg'         => 'Enterprise Register Extract',
        'doc_reg_hint'    => 'Publicly available from the REQ. PDF, JPG or PNG (max 5MB)',
        'doc_upload'      => 'Click to upload',
        'doc_drag'        => 'or drag and drop',
        'doc_secure'      => 'Your documents are securely stored and only used for verification. They will not be shared with third parties.',
        'doc_later'       => 'Skipping documents? You can upload them anytime from <strong>My Documents</strong> in your supplier portal.',

        'pw'              => 'Password',
        'pw_ph'           => 'Minimum 10 characters',
        'pw_confirm'      => 'Confirm Password',
        'pw_confirm_ph'   => 'Re-enter your password',
        'pw_nomatch'      => 'Passwords do not match',
        'pw_rule_len'     => 'At least 10 characters',
        'pw_rule_upper'   => 'One uppercase letter (A-Z)',
        'pw_rule_lower'   => 'One lowercase letter (a-z)',
        'pw_rule_num'     => 'One number (0-9)',
        'pw_rule_special' => 'One special character (!@#$%^&*)',
        'pw_fail_alert'   => 'Password must meet all requirements listed.',
        'pw_neq_alert'    => 'NEQ must be exactly 10 digits.',

        'terms_agree'     => 'I agree to the',
        'terms_link'      => 'Terms of Service',
        'terms_and'       => 'and',
        'privacy_link'    => 'Privacy Policy',

        'submit'          => 'Submit Application',
        'have_account'    => 'Already have an account?',
        'sign_in'         => 'Sign In',
        'opt'             => 'optional',
        'req'             => '*',
    ],
    'fr' => [
        'page_title'      => 'Accepter l\'invitation fournisseur',
        'h1'              => 'Complétez votre candidature',
        'subtitle'        => 'Vous avez été invité à rejoindre OCSAPP en tant que fournisseur. Remplissez le formulaire ci-dessous et notre équipe examinera votre candidature.',
        'invited_to'      => 'Invitation envoyée à :',
        'invited_note'    => 'Vous pouvez utiliser une autre adresse courriel pour votre compte ci-dessous.',

        'sec_biz'         => 'Informations sur l\'entreprise',
        'sec_biz_desc'    => 'Parlez-nous de votre entreprise.',
        'sec_contact'     => 'Personne-contact',
        'sec_contact_desc'=> 'Contact principal pour ce compte fournisseur.',
        'sec_address'     => 'Adresse officielle de l\'entreprise',
        'sec_address_desc'=> 'L\'adresse officielle enregistrée de votre entreprise.',
        'sec_docs'        => 'Documents de vérification',
        'sec_docs_desc'   => 'Téléversez au moins un document confirmant l\'enregistrement de votre entreprise. Vous pouvez également les ajouter plus tard depuis votre portail fournisseur.',
        'sec_pw'          => 'Créer votre mot de passe',
        'sec_pw_desc'     => 'Définissez un mot de passe pour accéder à votre portail fournisseur dès la soumission.',

        'biz_name'        => 'Nom commercial / d\'exploitation',
        'biz_name_ph'     => 'ABC Grossiste Inc.',
        'legal_name'      => 'Dénomination sociale',
        'legal_name_ph'   => 'ABC Grossiste Incorporée',
        'legal_name_hint' => 'Nom légal officiel enregistré de votre entreprise',
        'op_names'        => 'Noms d\'exploitation / DBA',
        'op_names_ph'     => 'ex. ABC Aliments, ABC Direct',
        'op_names_hint'   => 'Autres noms sous lesquels vous opérez, séparés par des virgules',
        'neq'             => 'Numéro NEQ',
        'neq_ph'          => '1234567890',
        'neq_hint'        => 'Numéro d\'entreprise du Québec à 10 chiffres',
        'email_lbl'       => 'Courriel du compte',
        'email_ph'        => 'vous@exemple.com',
        'email_hint'      => 'Pré-rempli depuis votre invitation — modifiez-le si vous préférez une autre adresse de connexion',
        'first_name'      => 'Prénom',
        'first_name_ph'   => 'Marie',
        'last_name'       => 'Nom',
        'last_name_ph'    => 'Tremblay',
        'phone'           => 'Numéro de téléphone',
        'phone_ph'        => '(514) 123-4567',
        'street'          => 'Adresse',
        'street_ph'       => '123 rue du Commerce',
        'city'            => 'Ville',
        'city_ph'         => 'Montréal',
        'province'        => 'Province',
        'province_ph'     => 'Sélectionner la province',
        'postal'          => 'Code postal',
        'postal_ph'       => 'H1A 2B3',
        'country'         => 'Pays',

        'doc_cert'        => 'Certificat de constitution',
        'doc_cert_hint'   => 'Pour les sociétés. PDF, JPG ou PNG (max 5 Mo)',
        'doc_decl'        => 'Déclaration d\'immatriculation',
        'doc_decl_hint'   => 'Pour les entreprises individuelles / sociétés. PDF, JPG ou PNG (max 5 Mo)',
        'doc_reg'         => 'Extrait du registre des entreprises',
        'doc_reg_hint'    => 'Disponible au REQ. PDF, JPG ou PNG (max 5 Mo)',
        'doc_upload'      => 'Cliquez pour télécharger',
        'doc_drag'        => 'ou glisser-déposer',
        'doc_secure'      => 'Vos documents sont sécurisés et utilisés uniquement à des fins de vérification. Ils ne seront pas partagés avec des tiers.',
        'doc_later'       => 'Vous ignorez les documents ? Vous pouvez les télécharger à tout moment depuis <strong>Mes documents</strong> dans votre portail fournisseur.',

        'pw'              => 'Mot de passe',
        'pw_ph'           => 'Minimum 10 caractères',
        'pw_confirm'      => 'Confirmer le mot de passe',
        'pw_confirm_ph'   => 'Ressaisir le mot de passe',
        'pw_nomatch'      => 'Les mots de passe ne correspondent pas',
        'pw_rule_len'     => '10 caractères minimum',
        'pw_rule_upper'   => 'Une lettre majuscule (A-Z)',
        'pw_rule_lower'   => 'Une lettre minuscule (a-z)',
        'pw_rule_num'     => 'Un chiffre (0-9)',
        'pw_rule_special' => 'Un caractère spécial (!@#$%^&*)',
        'pw_fail_alert'   => 'Le mot de passe doit satisfaire toutes les exigences listées.',
        'pw_neq_alert'    => 'Le NEQ doit comporter exactement 10 chiffres.',

        'terms_agree'     => 'J\'accepte les',
        'terms_link'      => 'Conditions d\'utilisation',
        'terms_and'       => 'et la',
        'privacy_link'    => 'Politique de confidentialité',

        'submit'          => 'Soumettre ma candidature',
        'have_account'    => 'Vous avez déjà un compte ?',
        'sign_in'         => 'Se connecter',
        'opt'             => 'optionnel',
        'req'             => '*',
    ],
];

$inv = $s[$currentLang] ?? $s['fr'];

$provinces = [
    'AB' => 'Alberta', 'BC' => 'British Columbia', 'MB' => 'Manitoba',
    'NB' => 'New Brunswick', 'NL' => 'Newfoundland and Labrador', 'NS' => 'Nova Scotia',
    'ON' => 'Ontario', 'PE' => 'Prince Edward Island', 'QC' => 'Québec',
    'SK' => 'Saskatchewan', 'NT' => 'Northwest Territories', 'NU' => 'Nunavut', 'YT' => 'Yukon',
];
$oldProv = $_SESSION['_old_input']['registered_address_province'] ?? 'QC';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $inv['page_title'] ?> – OCSAPP</title>
  <?= csrfMeta() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    .invite-page {
      max-width: 700px;
      margin: 0 auto;
      padding: 40px 20px 64px;
    }

    .invite-header {
      text-align: center;
      margin-bottom: 28px;
    }
    .invite-header h1 {
      font-size: 32px;
      font-weight: 800;
      color: #1a1a1a;
      margin-bottom: 10px;
    }
    .invite-header p {
      font-size: 15px;
      color: #6b7280;
      line-height: 1.6;
    }

    /* Invite banner */
    .invite-banner {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-left: 4px solid #00b207;
      border-radius: 10px;
      padding: 14px 18px;
      margin-bottom: 24px;
    }
    .invite-banner i { color: #00b207; font-size: 18px; margin-top: 2px; flex-shrink: 0; }
    .invite-banner-body { font-size: 13px; color: #4b5563; line-height: 1.6; }
    .invite-banner-body strong { display: block; font-size: 15px; font-weight: 700; color: #1a1a1a; margin-top: 2px; }
    .invite-banner-body span { font-size: 12px; color: #6b7280; margin-top: 4px; display: block; }

    /* Section cards — identical to apply.php */
    .form-section {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 28px 32px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .form-section-title {
      font-size: 16px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .form-section-title i { color: #00b207; font-size: 17px; }
    .form-section-desc { font-size: 13px; color: #9ca3af; margin-bottom: 20px; }

    /* Grid */
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    .form-row.single { grid-template-columns: 1fr; }
    .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 16px;
    }
    .form-group label {
      font-size: 13px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 6px;
    }
    .form-group label .req { color: #ef4444; }
    .form-group label .opt { font-weight: 400; color: #9ca3af; }

    /* Inputs */
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      font-family: inherit;
      color: #1a1a1a;
      background: #fff;
      transition: border-color 0.2s, box-shadow 0.2s;
      box-sizing: border-box;
    }
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #00b207;
      box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
    }
    .form-group input[readonly] { background: #f9fafb; color: #6b7280; cursor: default; }
    .hint { display: block; font-size: 12px; color: #9ca3af; margin-top: 4px; }

    /* NEQ counter */
    .neq-wrap { position: relative; }
    .neq-wrap input { padding-right: 52px; }
    .neq-counter {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      font-size: 12px; color: #9ca3af; pointer-events: none;
    }

    /* Password */
    .pw-wrap { position: relative; }
    .pw-wrap input { padding-right: 44px; }
    .pw-toggle {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      background: none; border: none; color: #9ca3af;
      cursor: pointer; font-size: 16px; padding: 0; line-height: 1;
    }
    .pw-toggle:hover { color: #6b7280; }
    .pw-rule {
      font-size: 13px; display: flex; align-items: center; gap: 6px;
      padding: 2px 0; transition: color 0.2s;
    }
    .pw-rule-fail { color: #9ca3af; }
    .pw-rule-ok   { color: #00b207; }
    .pw-rule i    { font-size: 12px; }

    /* Document upload */
    .doc-upload-group { margin-bottom: 20px; }
    .doc-upload-group > label {
      display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;
    }
    .doc-upload-area {
      position: relative;
      border: 2px dashed #d1d5db;
      border-radius: 10px;
      padding: 24px 20px;
      text-align: center;
      cursor: pointer;
      transition: border-color 0.2s, background 0.2s;
      background: #fafafa;
    }
    .doc-upload-area:hover,
    .doc-upload-area.has-file { border-color: #00b207; background: #f0fdf4; }
    .doc-upload-area input[type="file"] {
      position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .doc-upload-icon { font-size: 28px; color: #9ca3af; margin-bottom: 8px; }
    .doc-upload-area.has-file .doc-upload-icon { color: #00b207; }
    .doc-upload-text { font-size: 14px; color: #6b7280; }
    .doc-upload-text strong { color: #374151; }
    .doc-file-name {
      display: none; margin-top: 6px; font-size: 13px;
      color: #00b207; font-weight: 600; word-break: break-all;
    }
    .doc-upload-area.has-file .doc-file-name { display: block; }
    .doc-upload-hint { font-size: 12px; color: #9ca3af; margin-top: 6px; }

    /* Info box */
    .info-box {
      background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;
      padding: 12px 16px; margin-top: 8px;
      font-size: 13px; color: #166534; line-height: 1.6;
    }
    .info-box p { margin: 0; }
    .info-box p + p { margin-top: 8px; }
    .info-box i { margin-right: 6px; }

    /* Alerts */
    .flash-message {
      padding: 14px 18px; border-radius: 8px; margin-bottom: 20px;
      font-size: 14px; display: flex; align-items: center; gap: 10px;
    }
    .flash-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

    /* Checkbox */
    .checkbox-group {
      display: flex; align-items: flex-start; gap: 10px; margin: 8px 0 20px;
    }
    .checkbox-group input[type="checkbox"] {
      width: 18px; height: 18px; margin-top: 2px;
      accent-color: #00b207; flex-shrink: 0;
    }
    .checkbox-group label { font-size: 13px; color: #6b7280; line-height: 1.6; }
    .checkbox-group label a { color: #00b207; font-weight: 600; text-decoration: none; }
    .checkbox-group label a:hover { text-decoration: underline; }

    /* Submit */
    .btn-submit {
      display: flex; align-items: center; justify-content: center; gap: 10px;
      width: 100%; padding: 15px 24px;
      background: linear-gradient(135deg, #00b207 0%, #008505 100%);
      color: white; border: none; border-radius: 10px;
      font-size: 16px; font-weight: 700; cursor: pointer;
      transition: all 0.3s; font-family: inherit;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,178,7,0.3); }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .spinner {
      display: none; width: 20px; height: 20px;
      border: 2px solid rgba(255,255,255,0.3); border-top-color: white;
      border-radius: 50%; animation: spin 0.6s linear infinite;
    }
    .btn-submit.loading .spinner  { display: inline-block; }
    .btn-submit.loading .btn-text { display: none; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Bottom links */
    .invite-links {
      text-align: center; margin-top: 24px;
      font-size: 14px; color: #6b7280; line-height: 2;
    }
    .invite-links a { color: #00b207; font-weight: 600; text-decoration: none; }
    .invite-links a:hover { text-decoration: underline; }

    /* ── Package selector ── */
    .pkg-selector-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
      margin-top: 4px;
    }
    .pkg-option { position: relative; cursor: pointer; }
    .pkg-option input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
    .pkg-option-card {
      display: block;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      padding: 18px 16px;
      transition: all 0.2s ease;
      background: #fff;
      user-select: none;
    }
    .pkg-option input[type="radio"]:checked + .pkg-option-card {
      border-color: #00b207; background: #f0fdf4;
      box-shadow: 0 0 0 1px #00b207;
    }
    .pkg-option-card:hover { border-color: #00b207; background: #f9fffe; }
    .pkg-option-header {
      display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;
    }
    .pkg-option-name { font-size: 14px; font-weight: 700; color: #111827; }
    .pkg-option-check {
      width: 20px; height: 20px; border-radius: 50%;
      border: 2px solid #d1d5db;
      display: flex; align-items: center; justify-content: center;
      transition: all 0.2s; flex-shrink: 0;
    }
    .pkg-option input[type="radio"]:checked + .pkg-option-card .pkg-option-check {
      border-color: #00b207; background: #00b207;
    }
    .pkg-option input[type="radio"]:checked + .pkg-option-card .pkg-option-check::after {
      content: ''; width: 6px; height: 6px; background: white; border-radius: 50%;
    }
    .pkg-badge-pill {
      display: inline-block;
      font-size: 10px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;
      padding: 2px 10px; border-radius: 10px; margin-bottom: 8px;
    }
    .pkg-essential-pill  { background: #f3f4f6; color: #6b7280; }
    .pkg-experience-pill { background: #eff6ff; color: #3b82f6; }
    .pkg-prestige-pill   { background: #f0fdf4; color: #00b207; }
    .pkg-enterprise-pill { background: #fefce8; color: #d97706; }
    .pkg-option-desc { font-size: 12px; color: #6b7280; line-height: 1.5; }
    .pkg-option-popular {
      display: inline-block; font-size: 10px; font-weight: 700;
      background: #00b207; color: white; padding: 2px 8px; border-radius: 8px; margin-left: 6px;
    }
    .pkg-error { color: #ef4444; font-size: 12px; margin-top: 6px; display: none; }

    @media (max-width: 640px) {
      .invite-page { padding: 24px 16px 48px; }
      .invite-header h1 { font-size: 26px; }
      .form-section { padding: 20px 16px; }
      .form-row { grid-template-columns: 1fr; }
      .pkg-selector-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/../components/header.php'; ?>

<main class="page" style="background: #fff; min-height: calc(100vh - 200px);">
<div class="invite-page">

  <div class="invite-header">
    <h1><?= $inv['h1'] ?></h1>
    <p><?= $inv['subtitle'] ?></p>
  </div>

  <!-- Invite Banner -->
  <div class="invite-banner">
    <i class="fas fa-envelope-open-text"></i>
    <div class="invite-banner-body">
      <?= $inv['invited_to'] ?>
      <strong><?= htmlspecialchars($invite['email']) ?></strong>
      <span><?= $inv['invited_note'] ?></span>
    </div>
  </div>

  <?php if (hasFlash('error')): ?>
    <div class="flash-message flash-error">
      <i class="fas fa-exclamation-circle"></i>
      <?= getFlash('error') ?>
    </div>
  <?php endif; ?>
  <?php if (hasFlash('success')): ?>
    <div class="flash-message flash-success">
      <i class="fas fa-check-circle"></i>
      <?= getFlash('success') ?>
    </div>
  <?php endif; ?>

  <form id="inviteForm" method="POST" action="<?= url('supplier/complete-registration') ?>" enctype="multipart/form-data">
    <?= csrfField() ?>
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

    <!-- ── Section 1: Business Information ── -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-building"></i>
        <?= $inv['sec_biz'] ?>
      </div>
      <p class="form-section-desc"><?= $inv['sec_biz_desc'] ?></p>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $inv['biz_name'] ?> <span class="req">*</span></label>
          <input type="text" name="business_name" required maxlength="255"
            placeholder="<?= $inv['biz_name_ph'] ?>"
            value="<?= htmlspecialchars($_SESSION['_old_input']['business_name'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $inv['legal_name'] ?> <span class="req">*</span></label>
          <input type="text" name="legal_name" required maxlength="255"
            placeholder="<?= $inv['legal_name_ph'] ?>"
            value="<?= htmlspecialchars($_SESSION['_old_input']['legal_name'] ?? '') ?>">
          <span class="hint"><?= $inv['legal_name_hint'] ?></span>
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $inv['op_names'] ?> <span class="opt">(<?= $inv['opt'] ?>)</span></label>
          <input type="text" name="operating_names" maxlength="500"
            placeholder="<?= $inv['op_names_ph'] ?>"
            value="<?= htmlspecialchars($_SESSION['_old_input']['operating_names'] ?? '') ?>">
          <span class="hint"><?= $inv['op_names_hint'] ?></span>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label><?= $inv['neq'] ?> <span class="req">*</span></label>
          <div class="neq-wrap">
            <input type="text" name="neq_number" id="neqInput" required
              maxlength="10" minlength="10" pattern="[0-9]{10}"
              placeholder="<?= $inv['neq_ph'] ?>"
              value="<?= htmlspecialchars($_SESSION['_old_input']['neq_number'] ?? '') ?>"
              oninput="updateNeqCounter(this)">
            <span class="neq-counter" id="neqCounter">0/10</span>
          </div>
          <span class="hint"><?= $inv['neq_hint'] ?></span>
        </div>

        <div class="form-group">
          <label><?= $inv['email_lbl'] ?> <span class="req">*</span></label>
          <input type="email" name="email" required maxlength="255"
            placeholder="<?= $inv['email_ph'] ?>"
            value="<?= htmlspecialchars($_SESSION['_old_input']['email'] ?? $invite['email']) ?>">
          <span class="hint"><?= $inv['email_hint'] ?></span>
        </div>
      </div>
    </div>

    <!-- ── Section 2: Contact Person ── -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-user-tie"></i>
        <?= $inv['sec_contact'] ?>
      </div>
      <p class="form-section-desc"><?= $inv['sec_contact_desc'] ?></p>

      <div class="form-row">
        <div class="form-group">
          <label><?= $inv['first_name'] ?> <span class="req">*</span></label>
          <input type="text" name="first_name" required maxlength="100"
            placeholder="<?= $inv['first_name_ph'] ?>"
            value="<?= htmlspecialchars($_SESSION['_old_input']['first_name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label><?= $inv['last_name'] ?> <span class="req">*</span></label>
          <input type="text" name="last_name" required maxlength="100"
            placeholder="<?= $inv['last_name_ph'] ?>"
            value="<?= htmlspecialchars($_SESSION['_old_input']['last_name'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $inv['phone'] ?> <span class="req">*</span></label>
          <input type="tel" name="phone" required maxlength="50"
            placeholder="<?= $inv['phone_ph'] ?>"
            value="<?= htmlspecialchars($_SESSION['_old_input']['phone'] ?? '') ?>">
        </div>
      </div>
    </div>

    <!-- ── Section 3: Registered Address ── -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-map-marker-alt"></i>
        <?= $inv['sec_address'] ?>
      </div>
      <p class="form-section-desc"><?= $inv['sec_address_desc'] ?></p>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $inv['street'] ?> <span class="req">*</span></label>
          <input type="text" name="registered_address_street" required maxlength="255"
            placeholder="<?= $inv['street_ph'] ?>"
            value="<?= htmlspecialchars($_SESSION['_old_input']['registered_address_street'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label><?= $inv['city'] ?> <span class="req">*</span></label>
          <input type="text" name="registered_address_city" required maxlength="100"
            placeholder="<?= $inv['city_ph'] ?>"
            value="<?= htmlspecialchars($_SESSION['_old_input']['registered_address_city'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label><?= $inv['province'] ?> <span class="req">*</span></label>
          <select name="registered_address_province" required>
            <option value=""><?= $inv['province_ph'] ?></option>
            <?php foreach ($provinces as $code => $label): ?>
              <option value="<?= $code ?>" <?= $oldProv === $code ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label><?= $inv['postal'] ?> <span class="req">*</span></label>
          <input type="text" name="registered_address_postal" required maxlength="10"
            placeholder="<?= $inv['postal_ph'] ?>"
            value="<?= htmlspecialchars($_SESSION['_old_input']['registered_address_postal'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label><?= $inv['country'] ?></label>
          <input type="text" value="Canada" readonly>
        </div>
      </div>
    </div>

    <!-- ── Section 4: Choose Your Package ── -->
    <?php
    $validPkgs = ['Essential', 'Experience', 'Prestige', 'Enterprise'];
    $preselectedPkg = $_SESSION['_old_input']['subscription_package'] ?? 'Essential';
    if (!in_array($preselectedPkg, $validPkgs)) $preselectedPkg = 'Essential';
    ?>
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-layer-group"></i>
        <?= $currentLang === 'fr' ? 'Choisissez votre forfait' : 'Choose Your Package' ?>
      </div>
      <p class="form-section-desc"><?= $currentLang === 'fr' ? 'Sélectionnez le plan qui correspond à votre entreprise. Vous pouvez passer à un plan supérieur à tout moment.' : 'Select the plan that fits your business. You can upgrade anytime from your supplier portal.' ?></p>

      <div class="pkg-selector-grid">
        <label class="pkg-option">
          <input type="radio" name="subscription_package" value="Essential" <?= $preselectedPkg === 'Essential' ? 'checked' : '' ?>>
          <span class="pkg-option-card">
            <span class="pkg-badge-pill pkg-essential-pill">Essential</span>
            <div class="pkg-option-header">
              <span class="pkg-option-name"><?= $fr ? 'Démarrage' : 'Get Started' ?></span>
              <span class="pkg-option-check"></span>
            </div>
            <p class="pkg-option-desc"><?= $fr ? "Jusqu'à 25 produits. Analytique de base. Idéal pour les vendeurs en ligne débutants." : 'Up to 25 products. Basic analytics. Perfect for first-time online sellers.' ?></p>
          </span>
        </label>

        <label class="pkg-option">
          <input type="radio" name="subscription_package" value="Experience" <?= $preselectedPkg === 'Experience' ? 'checked' : '' ?>>
          <span class="pkg-option-card">
            <span class="pkg-badge-pill pkg-experience-pill">Experience</span>
            <div class="pkg-option-header">
              <span class="pkg-option-name"><?= $fr ? 'Croissance' : 'Scale Up' ?></span>
              <span class="pkg-option-check"></span>
            </div>
            <p class="pkg-option-desc"><?= $fr ? "Jusqu'à 200 produits. Analytique avancée, factures et file d'attente prioritaire." : 'Up to 200 products. Advanced analytics, invoices & priority order queue.' ?></p>
          </span>
        </label>

        <label class="pkg-option">
          <input type="radio" name="subscription_package" value="Prestige" <?= $preselectedPkg === 'Prestige' ? 'checked' : '' ?>>
          <span class="pkg-option-card">
            <span class="pkg-badge-pill pkg-prestige-pill">Prestige <span class="pkg-option-popular"><?= $fr ? '★ Populaire' : '★ Popular' ?></span></span>
            <div class="pkg-option-header">
              <span class="pkg-option-name"><?= $fr ? 'Plein Potentiel' : 'Full Power' ?></span>
              <span class="pkg-option-check"></span>
            </div>
            <p class="pkg-option-desc"><?= $fr ? 'Annonces illimitées, mise en avant, gestionnaire de compte dédié et co-marketing.' : 'Unlimited listings, featured placement, dedicated account manager & co-marketing.' ?></p>
          </span>
        </label>

        <label class="pkg-option">
          <input type="radio" name="subscription_package" value="Enterprise" <?= $preselectedPkg === 'Enterprise' ? 'checked' : '' ?>>
          <span class="pkg-option-card">
            <span class="pkg-badge-pill pkg-enterprise-pill">Enterprise</span>
            <div class="pkg-option-header">
              <span class="pkg-option-name"><?= $fr ? 'Sur Mesure' : 'Custom Deal' ?></span>
              <span class="pkg-option-check"></span>
            </div>
            <p class="pkg-option-desc"><?= $fr ? 'Conditions personnalisées, taux de commission sur mesure, intégrations API/ERP pour les partenaires à fort volume.' : 'Tailored terms, custom commission rates, API/ERP integrations for high-volume partners.' ?></p>
          </span>
        </label>
      </div>
      <p class="pkg-error" id="pkgError"><?= $fr ? 'Veuillez sélectionner un forfait pour continuer.' : 'Please select a package to continue.' ?></p>
    </div>

    <!-- ── Section 5: Documents ── -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-file-alt"></i>
        <?= $inv['sec_docs'] ?>
        <span style="margin-left:8px;background:#f0fdf4;color:#059669;border:1px solid #bbf7d0;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;"><?= $inv['opt'] ?></span>
      </div>
      <p class="form-section-desc"><?= $inv['sec_docs_desc'] ?></p>

      <div class="doc-upload-group">
        <label><?= $inv['doc_cert'] ?></label>
        <div class="doc-upload-area" id="dropArea1">
          <input type="file" name="doc_certificate_incorporation" accept=".pdf,.jpg,.jpeg,.png"
                 onchange="handleFileSelect(this,'dropArea1')">
          <div class="doc-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <div class="doc-upload-text"><strong><?= $inv['doc_upload'] ?></strong> <?= $inv['doc_drag'] ?></div>
          <div class="doc-file-name"></div>
        </div>
        <div class="doc-upload-hint"><?= $inv['doc_cert_hint'] ?></div>
      </div>

      <div class="doc-upload-group">
        <label><?= $inv['doc_decl'] ?></label>
        <div class="doc-upload-area" id="dropArea2">
          <input type="file" name="doc_declaration_registration" accept=".pdf,.jpg,.jpeg,.png"
                 onchange="handleFileSelect(this,'dropArea2')">
          <div class="doc-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <div class="doc-upload-text"><strong><?= $inv['doc_upload'] ?></strong> <?= $inv['doc_drag'] ?></div>
          <div class="doc-file-name"></div>
        </div>
        <div class="doc-upload-hint"><?= $inv['doc_decl_hint'] ?></div>
      </div>

      <div class="doc-upload-group">
        <label><?= $inv['doc_reg'] ?></label>
        <div class="doc-upload-area" id="dropArea3">
          <input type="file" name="doc_enterprise_register" accept=".pdf,.jpg,.jpeg,.png"
                 onchange="handleFileSelect(this,'dropArea3')">
          <div class="doc-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <div class="doc-upload-text"><strong><?= $inv['doc_upload'] ?></strong> <?= $inv['doc_drag'] ?></div>
          <div class="doc-file-name"></div>
        </div>
        <div class="doc-upload-hint"><?= $inv['doc_reg_hint'] ?></div>
      </div>

      <div class="info-box">
        <p><i class="fas fa-shield-alt"></i><?= $inv['doc_secure'] ?></p>
        <p style="margin-top:8px;"><i class="fas fa-folder-open"></i><?= $inv['doc_later'] ?></p>
      </div>
    </div>

    <!-- ── Section 5: Password ── -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-lock"></i>
        <?= $inv['sec_pw'] ?>
      </div>
      <p class="form-section-desc"><?= $inv['sec_pw_desc'] ?></p>

      <div class="form-row">
        <div class="form-group">
          <label><?= $inv['pw'] ?> <span class="req">*</span></label>
          <div class="pw-wrap">
            <input type="password" name="password" id="invitePw" required minlength="10"
              placeholder="<?= $inv['pw_ph'] ?>">
            <button type="button" class="pw-toggle" onclick="togglePw('invitePw',this)">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>
        <div class="form-group">
          <label><?= $inv['pw_confirm'] ?> <span class="req">*</span></label>
          <div class="pw-wrap">
            <input type="password" name="password_confirmation" id="invitePwConfirm" required minlength="10"
              placeholder="<?= $inv['pw_confirm_ph'] ?>">
            <button type="button" class="pw-toggle" onclick="togglePw('invitePwConfirm',this)">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>
      </div>

      <div id="pwStrengthBox" style="margin-top:10px;"></div>
      <p id="pwMatchErr" style="display:none;color:#ef4444;font-size:13px;margin-top:6px;">
        <i class="fas fa-exclamation-circle"></i> <?= $inv['pw_nomatch'] ?>
      </p>
    </div>

    <!-- Terms -->
    <div class="checkbox-group">
      <input type="checkbox" name="terms" id="terms" required>
      <label for="terms">
        <?= $inv['terms_agree'] ?>
        <a href="<?= url('terms') ?>" target="_blank"><?= $inv['terms_link'] ?></a>
        <?= $inv['terms_and'] ?>
        <a href="<?= url('privacy') ?>" target="_blank"><?= $inv['privacy_link'] ?></a>
      </label>
    </div>

    <!-- Submit -->
    <button type="submit" class="btn-submit" id="submitBtn">
      <span class="spinner"></span>
      <span class="btn-text"><i class="fas fa-paper-plane"></i> <?= $inv['submit'] ?></span>
    </button>

  </form>

  <div class="invite-links">
    <?= $inv['have_account'] ?> <a href="<?= url('supplier/login') ?>"><?= $inv['sign_in'] ?></a>
  </div>

</div>
</main>

<?php include __DIR__ . '/../components/footer.php'; ?>

<script>
/* ── NEQ counter ── */
function updateNeqCounter(input) {
  input.value = input.value.replace(/[^0-9]/g, '');
  var counter = document.getElementById('neqCounter');
  counter.textContent = input.value.length + '/10';
  counter.style.color = input.value.length === 10 ? '#00b207' : '#9ca3af';
}

/* ── File upload ── */
function handleFileSelect(input, areaId) {
  var area = document.getElementById(areaId);
  var fnEl = area.querySelector('.doc-file-name');
  if (input.files && input.files[0]) {
    var file = input.files[0];
    if (file.size > 5 * 1024 * 1024) {
      alert('<?= $currentLang === 'fr' ? 'Le fichier doit faire moins de 5 Mo' : 'File size must be less than 5MB' ?>');
      input.value = ''; return;
    }
    var ext = file.name.split('.').pop().toLowerCase();
    if (!['pdf','jpg','jpeg','png'].includes(ext)) {
      alert('<?= $currentLang === 'fr' ? 'Seuls les fichiers PDF, JPG et PNG sont acceptés' : 'Only PDF, JPG, and PNG files are allowed' ?>');
      input.value = ''; return;
    }
    area.classList.add('has-file');
    fnEl.textContent = file.name;
  } else {
    area.classList.remove('has-file');
    fnEl.textContent = '';
  }
}

/* ── Password toggle ── */
function togglePw(id, btn) {
  var input = document.getElementById(id);
  var icon  = btn.querySelector('i');
  input.type = input.type === 'password' ? 'text' : 'password';
  icon.className = input.type === 'text' ? 'fas fa-eye-slash' : 'fas fa-eye';
}

/* ── Password strength ── */
var pwRules = [
  { id: 'rule-len',     test: function(p){ return p.length >= 10; },          label: <?= json_encode($inv['pw_rule_len']) ?> },
  { id: 'rule-upper',   test: function(p){ return /[A-Z]/.test(p); },         label: <?= json_encode($inv['pw_rule_upper']) ?> },
  { id: 'rule-lower',   test: function(p){ return /[a-z]/.test(p); },         label: <?= json_encode($inv['pw_rule_lower']) ?> },
  { id: 'rule-num',     test: function(p){ return /[0-9]/.test(p); },         label: <?= json_encode($inv['pw_rule_num']) ?> },
  { id: 'rule-special', test: function(p){ return /[^A-Za-z0-9]/.test(p); }, label: <?= json_encode($inv['pw_rule_special']) ?> },
];

(function buildStrengthUI() {
  var box = document.getElementById('pwStrengthBox');
  if (!box) return;
  box.innerHTML = pwRules.map(function(r) {
    return '<div id="' + r.id + '" class="pw-rule pw-rule-fail"><i class="fas fa-circle-xmark"></i> ' + r.label + '</div>';
  }).join('');
})();

function updateStrength(pw) {
  pwRules.forEach(function(r) {
    var el = document.getElementById(r.id);
    if (!el) return;
    var ok = r.test(pw);
    el.className = 'pw-rule ' + (ok ? 'pw-rule-ok' : 'pw-rule-fail');
    el.querySelector('i').className = ok ? 'fas fa-circle-check' : 'fas fa-circle-xmark';
  });
}

document.getElementById('invitePw').addEventListener('input', function() {
  updateStrength(this.value);
  var c = document.getElementById('invitePwConfirm');
  if (c.value.length > 0)
    document.getElementById('pwMatchErr').style.display = this.value !== c.value ? 'block' : 'none';
});

document.getElementById('invitePwConfirm').addEventListener('input', function() {
  var pw = document.getElementById('invitePw').value;
  document.getElementById('pwMatchErr').style.display =
    (this.value.length > 0 && this.value !== pw) ? 'block' : 'none';
});

/* ── Form submit ── */
document.getElementById('inviteForm').addEventListener('submit', function(e) {
  var pkgSelected = document.querySelector('input[name="subscription_package"]:checked');
  if (!pkgSelected) {
    e.preventDefault();
    document.getElementById('pkgError').style.display = 'block';
    document.querySelector('.pkg-selector-grid').scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }
  document.getElementById('pkgError').style.display = 'none';

  var neq = document.getElementById('neqInput');
  if (!/^[0-9]{10}$/.test(neq.value)) {
    e.preventDefault();
    alert(<?= json_encode($inv['pw_neq_alert']) ?>);
    neq.focus(); return;
  }
  var pw  = document.getElementById('invitePw').value;
  var pw2 = document.getElementById('invitePwConfirm').value;
  var allPass = pwRules.every(function(r){ return r.test(pw); });
  if (!allPass) {
    e.preventDefault();
    alert(<?= json_encode($inv['pw_fail_alert']) ?>);
    document.getElementById('invitePw').focus(); return;
  }
  if (pw !== pw2) {
    e.preventDefault();
    document.getElementById('pwMatchErr').style.display = 'block';
    document.getElementById('invitePwConfirm').focus(); return;
  }
  document.getElementById('submitBtn').classList.add('loading');
  document.getElementById('submitBtn').disabled = true;
});

/* ── Init NEQ counter ── */
(function() {
  var neq = document.getElementById('neqInput');
  if (neq && neq.value) updateNeqCounter(neq);
})();
</script>

</body>
</html>
