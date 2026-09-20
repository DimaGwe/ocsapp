<?php
/**
 * Seller Application Form
 * Public-facing form for businesses to apply as OCSAPP sellers
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? 'Inscription - Vendeur Central - OCSAPP' : 'Create account - Seller Central - OCSAPP' ?></title>
  <?= csrfMeta() ?>

  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/portal-register.css') ?>">

  <style>
    body { font-family: 'Inter', 'Segoe UI', sans-serif; }

    .form-group label .required { color: #ef4444; }
    .form-group input.error, .form-group select.error { border-color: #ef4444 !important; }
    .error-text { color: #ef4444; font-size: 12px; margin-top: 4px; }

    .neq-input-wrapper input { padding-right: 52px !important; }
    .neq-counter {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      font-size: 12px; color: #9ca3af; pointer-events: none;
    }

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
    .doc-file-name { display: none; margin-top: 6px; font-size: 13px; font-weight: 600; word-break: break-all; }
    .doc-upload-area.has-file .doc-file-name { display: block; }

    .submit-section { padding-top: 8px; }
    .btn-submit .spinner {
      display: none; width: 20px; height: 20px;
      border: 2px solid rgba(255,255,255,0.3); border-top-color: white;
      border-radius: 50%; animation: spin 0.6s linear infinite;
    }
    .btn-submit.loading .spinner { display: inline-block; }
    .btn-submit.loading .btn-text { display: none; }
    @keyframes spin { to { transform: rotate(360deg); } }

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
      border-color: #00b207;
      background: #f0fdf4;
      box-shadow: 0 0 0 1px #00b207;
    }
    .pkg-option-card:hover { border-color: #00b207; background: #f9fffe; }
    .pkg-option-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
    .pkg-option-name { font-size: 14px; font-weight: 700; color: #111827; }
    .pkg-option-check {
      width: 20px; height: 20px;
      border-radius: 50%;
      border: 2px solid #d1d5db;
      display: flex; align-items: center; justify-content: center;
      transition: all 0.2s;
      flex-shrink: 0;
    }
    .pkg-option input[type="radio"]:checked + .pkg-option-card .pkg-option-check {
      border-color: #00b207;
      background: #00b207;
    }
    .pkg-option input[type="radio"]:checked + .pkg-option-card .pkg-option-check::after {
      content: '';
      width: 6px; height: 6px;
      background: white;
      border-radius: 50%;
    }
    .pkg-badge-pill {
      display: inline-block;
      font-size: 10px; font-weight: 700;
      letter-spacing: 0.8px; text-transform: uppercase;
      padding: 2px 10px; border-radius: 10px;
      margin-bottom: 8px;
    }
    .pkg-essential-pill  { background: #f3f4f6; color: #6b7280; }
    .pkg-experience-pill { background: #eff6ff; color: #3b82f6; }
    .pkg-prestige-pill   { background: #f0fdf4; color: #00b207; }
    .pkg-enterprise-pill { background: #fefce8; color: #d97706; }
    .pkg-option-desc { font-size: 12px; color: #6b7280; line-height: 1.5; }
    .pkg-option-popular {
      display: inline-block;
      font-size: 10px; font-weight: 700;
      background: #00b207; color: white;
      padding: 2px 8px; border-radius: 8px;
      margin-left: 6px;
    }
    .pkg-error { color: #ef4444; font-size: 12px; margin-top: 6px; display: none; }

    @media (max-width: 640px) {
      .doc-upload-area { padding: 20px 12px; }
      .pkg-selector-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
<header class="auth-topbar">
  <a class="auth-logo" href="<?= url('/') ?>" aria-label="OCSAPP">OCSAPP</a>
  <nav class="auth-toplinks" aria-label="<?= $fr ? "Navigation d'inscription" : 'Registration navigation' ?>">
    <a href="<?= url('/') ?>"><?= $fr ? 'Écosystème' : 'Ecosystem' ?></a>
    <a href="<?= url('home') ?>"><?= $fr ? 'Marché Central' : 'Market Central' ?></a>
    <a class="central-link" href="<?= url('seller-central') ?>"><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></a>
  </nav>
</header>

<main class="register-shell">
  <section class="brand-panel" aria-label="<?= $fr ? 'Vendeur Central' : 'Seller Central' ?>">
    <div class="brand-content">
      <div class="eyebrow"><?= $fr ? 'INSCRIPTION VENDEUR' : 'SELLER REGISTRATION' ?></div>
      <div class="central-icon"><img src="<?= asset('images/about/central-seller.png') ?>" alt="<?= $fr ? 'Vendeur Central' : 'Seller Central' ?>"></div>
      <h1><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></h1>
      <p><?= $fr ? "Créez votre compte vendeur, vérifiez votre entreprise et choisissez le forfait adapté à vos activités dans l'écosystème OCSAPP." : 'Create your seller account, verify your business and choose the package that fits your operation within the OCSAPP ecosystem.' ?></p>
      <p class="ecosystem-note"><strong><?= $fr ? 'Un seul écosystème OCSAPP.' : 'One OCSAPP ecosystem.' ?></strong><br><?= $fr ? "Votre processus d'intégration est relié au Central correspondant à votre rôle." : 'Your onboarding is connected to the Central associated with your role.' ?></p>
    </div>
  </section>

  <section class="form-panel">
    <div class="register-card">
      <div class="card-head">
        <div class="card-kicker"><?= $fr ? 'INTÉGRATION VENDEUR' : 'SELLER ONBOARDING' ?></div>
        <h2><?= $fr ? 'Joignez Vendeur Central' : 'Join Seller Central' ?></h2>
        <p><?= $fr ? "Complétez votre profil d'entreprise et la création de votre compte dans un seul parcours d'intégration standardisé." : 'Complete your business profile and account setup in one standardized onboarding flow.' ?></p>
      </div>

  <?php if (!empty($flash)):
    $flashType = isset($flash['error']) ? 'error' : 'success';
    $flashMessage = $flash[$flashType] ?? '';
  ?>
    <div class="alert alert-<?= $flashType === 'success' ? 'success' : 'error' ?>">
      <i class="fas fa-<?= $flashType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
      <?= $flashMessage ?>
    </div>
  <?php endif; ?>

  <form action="<?= url('seller/apply') ?>" method="POST" enctype="multipart/form-data" id="sellerApplicationForm">
    <?= csrfField() ?>
    <input type="hidden" name="lang" value="<?= $currentLang ?>">

    <!-- Section 1: Business Owner Info -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-user-tie"></i>
        <?= $fr ? 'Renseignements généraux sur le propriétaire' : 'General Business Owner Information' ?>
      </div>
      <p class="form-section-desc"><?= $fr ? "Parlez-nous de vous et de l'entreprise que vous souhaitez exploiter dans Vendeur Central." : 'Tell us about yourself and the business you want to operate through Seller Central.' ?></p>

      <div class="form-row">
        <div class="form-group">
          <label><?= $fr ? 'Prénom' : 'First Name' ?> <span class="required">*</span></label>
          <input type="text" name="first_name" required maxlength="100" placeholder="<?= $fr ? 'Jean' : 'John' ?>" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label><?= $fr ? 'Nom de famille' : 'Last Name' ?> <span class="required">*</span></label>
          <input type="text" name="last_name" required maxlength="100" placeholder="<?= $fr ? 'Tremblay' : 'Doe' ?>" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label><?= $fr ? 'Adresse courriel' : 'Email Address' ?> <span class="required">*</span></label>
          <input type="email" name="email" required maxlength="255" placeholder="<?= $fr ? 'jean@entreprise.ca' : 'john@business.com' ?>" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label><?= $fr ? 'Numéro de téléphone' : 'Phone Number' ?></label>
          <input type="tel" name="phone" maxlength="50" placeholder="(514) 555-0123" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $fr ? "Nom de l'entreprise" : 'Business Name' ?> <span class="required">*</span></label>
          <input type="text" name="business_name" required maxlength="255" placeholder="<?= $fr ? 'Votre Boutique inc.' : 'Your Store Inc.' ?>" value="<?= htmlspecialchars($old['business_name'] ?? '') ?>">
        </div>
      </div>
    </div>

    <!-- Section 2: Quebec Legal Identity -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-landmark"></i>
        <?= $fr ? 'Vérification de l\'identité légale au Québec' : 'Québec Legal Identity Verification' ?>
      </div>
      <p class="form-section-desc"><?= $fr ? "Fournissez les renseignements d'immatriculation de votre entreprise au Québec afin de vérifier votre profil vendeur." : 'Provide your Québec business registration details so your seller profile can be verified.' ?></p>

      <div class="info-box">
        <p>
          <i class="fas fa-info-circle"></i>
          <?= $fr ? 'Les renseignements peuvent être vérifiés auprès du' : 'Business records can be verified through the' ?>
          <a href="https://www.registreentreprises.gouv.qc.ca/" target="_blank" rel="noopener">Registraire des entreprises du Qu&eacute;bec</a>.
        </p>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $fr ? 'NEQ (numéro d\'entreprise du Québec)' : 'NEQ (Enterprise Number)' ?> <span class="required">*</span></label>
          <div class="neq-input-wrapper">
            <input type="text" name="neq_number" id="neqInput" required maxlength="10" minlength="10" pattern="[0-9]{10}" placeholder="1234567890" value="<?= htmlspecialchars($old['neq_number'] ?? '') ?>" oninput="updateNeqCounter(this)">
            <span class="neq-counter" id="neqCounter">0/10</span>
          </div>
          <span class="hint"><?= $fr ? "Numéro d'identification à 10 chiffres du Registraire des entreprises" : '10-digit Québec enterprise number' ?></span>
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $fr ? 'Dénomination sociale' : 'Legal Name' ?> <span class="required">*</span></label>
          <input type="text" name="legal_name" required maxlength="255" placeholder="<?= $fr ? 'Dénomination sociale complète telle qu\'enregistrée' : 'Full legal name as registered' ?>" value="<?= htmlspecialchars($old['legal_name'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $fr ? 'Nom(s) d\'exploitation' : 'Operating Name(s)' ?></label>
          <input type="text" name="operating_names" maxlength="500" placeholder="<?= $fr ? 'Nom commercial ou nom de boutique, s\'il diffère' : 'Trade or storefront name(s), if different' ?>" value="<?= htmlspecialchars($old['operating_names'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row single" style="margin-bottom: 8px;">
        <div class="form-group">
          <label><?= $fr ? 'Adresse du siège social' : 'Registered Office Address' ?> <span class="required">*</span></label>
          <input type="text" name="registered_address_street" required maxlength="255" placeholder="<?= $fr ? 'Adresse civique' : 'Street address' ?>" value="<?= htmlspecialchars($old['registered_address_street'] ?? '') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label><?= $fr ? 'Ville' : 'City' ?> <span class="required">*</span></label>
          <input type="text" name="registered_address_city" required maxlength="100" placeholder="<?= $fr ? 'Montr&eacute;al' : 'Montreal' ?>" value="<?= htmlspecialchars($old['registered_address_city'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label><?= $fr ? 'Province' : 'Province' ?></label>
          <select name="registered_address_province">
            <option value="Quebec" <?= ($old['registered_address_province'] ?? 'Quebec') === 'Quebec' ? 'selected' : '' ?>>Qu&eacute;bec</option>
            <option value="Ontario" <?= ($old['registered_address_province'] ?? '') === 'Ontario' ? 'selected' : '' ?>>Ontario</option>
            <option value="British Columbia" <?= ($old['registered_address_province'] ?? '') === 'British Columbia' ? 'selected' : '' ?>>British Columbia</option>
            <option value="Alberta" <?= ($old['registered_address_province'] ?? '') === 'Alberta' ? 'selected' : '' ?>>Alberta</option>
            <option value="Manitoba" <?= ($old['registered_address_province'] ?? '') === 'Manitoba' ? 'selected' : '' ?>>Manitoba</option>
            <option value="Saskatchewan" <?= ($old['registered_address_province'] ?? '') === 'Saskatchewan' ? 'selected' : '' ?>>Saskatchewan</option>
            <option value="Nova Scotia" <?= ($old['registered_address_province'] ?? '') === 'Nova Scotia' ? 'selected' : '' ?>>Nova Scotia</option>
            <option value="New Brunswick" <?= ($old['registered_address_province'] ?? '') === 'New Brunswick' ? 'selected' : '' ?>>New Brunswick</option>
            <option value="Newfoundland and Labrador" <?= ($old['registered_address_province'] ?? '') === 'Newfoundland and Labrador' ? 'selected' : '' ?>>Newfoundland and Labrador</option>
            <option value="Prince Edward Island" <?= ($old['registered_address_province'] ?? '') === 'Prince Edward Island' ? 'selected' : '' ?>>Prince Edward Island</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label><?= $fr ? 'Code postal' : 'Postal Code' ?> <span class="required">*</span></label>
          <input type="text" name="registered_address_postal" required maxlength="10" placeholder="H2X 1Y4" pattern="[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d" value="<?= htmlspecialchars($old['registered_address_postal'] ?? '') ?>">
        </div>
        <div class="form-group"></div>
      </div>
    </div>

    <!-- Section 3: Choose Your Package -->
    <?php
    $validPkgs = ['Essential', 'Experience', 'Prestige', 'Enterprise'];
    $preselectedPkg = '';
    $urlPkg = trim($_GET['package'] ?? '');
    if (in_array($urlPkg, $validPkgs)) $preselectedPkg = $urlPkg;
    if (empty($preselectedPkg) && in_array($old['subscription_package'] ?? '', $validPkgs)) {
        $preselectedPkg = $old['subscription_package'];
    }
    ?>
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-layer-group"></i>
        <?= $fr ? 'Choisissez votre forfait' : 'Choose Your Package' ?>
      </div>
      <p class="form-section-desc"><?= $fr ? 'Sélectionnez le forfait Vendeur Central qui convient à votre entreprise. Vous pourrez le modifier plus tard à partir de votre portail vendeur.' : 'Select the Seller Central plan that best fits your business. You can change plans later from your seller portal.' ?></p>

      <div class="pkg-selector-grid">

        <label class="pkg-option">
          <input type="radio" name="subscription_package" value="Essential" <?= $preselectedPkg === 'Essential' || $preselectedPkg === '' ? 'checked' : '' ?>>
          <span class="pkg-option-card">
            <span class="pkg-badge-pill pkg-essential-pill"><?= $fr ? 'Essentiel' : 'Essential' ?></span>
            <div class="pkg-option-header">
              <span class="pkg-option-name"><?= $fr ? 'Démarrer' : 'Get Started' ?></span>
              <span class="pkg-option-check"></span>
            </div>
            <p class="pkg-option-desc"><?= $fr ? 'Les outils essentiels pour commencer à vendre sur OCSAPP.' : 'Core selling tools for businesses getting started on OCSAPP.' ?></p>
          </span>
        </label>

        <label class="pkg-option">
          <input type="radio" name="subscription_package" value="Experience" <?= $preselectedPkg === 'Experience' ? 'checked' : '' ?>>
          <span class="pkg-option-card">
            <span class="pkg-badge-pill pkg-experience-pill"><?= $fr ? 'Expérience' : 'Experience' ?></span>
            <div class="pkg-option-header">
              <span class="pkg-option-name"><?= $fr ? 'Développer' : 'Grow' ?></span>
              <span class="pkg-option-check"></span>
            </div>
            <p class="pkg-option-desc"><?= $fr ? "Des outils élargis, de l'analytique et du soutien opérationnel." : 'Expanded selling tools, analytics and operational support.' ?></p>
          </span>
        </label>

        <label class="pkg-option">
          <input type="radio" name="subscription_package" value="Prestige" <?= $preselectedPkg === 'Prestige' ? 'checked' : '' ?>>
          <span class="pkg-option-card">
            <span class="pkg-badge-pill pkg-prestige-pill"><?= $fr ? 'Prestige' : 'Prestige' ?> <span class="pkg-option-popular"><?= $fr ? '★ Populaire' : '★ Popular' ?></span></span>
            <div class="pkg-option-header">
              <span class="pkg-option-name"><?= $fr ? 'Accélérer' : 'Scale' ?></span>
              <span class="pkg-option-check"></span>
            </div>
            <p class="pkg-option-desc"><?= $fr ? 'Une visibilité accrue et des capacités de croissance avancées.' : 'Advanced visibility, business support and growth capabilities.' ?></p>
          </span>
        </label>

        <label class="pkg-option">
          <input type="radio" name="subscription_package" value="Enterprise" <?= $preselectedPkg === 'Enterprise' ? 'checked' : '' ?>>
          <span class="pkg-option-card">
            <span class="pkg-badge-pill pkg-enterprise-pill"><?= $fr ? 'Entreprise' : 'Enterprise' ?></span>
            <div class="pkg-option-header">
              <span class="pkg-option-name"><?= $fr ? 'Sur mesure' : 'Custom' ?></span>
              <span class="pkg-option-check"></span>
            </div>
            <p class="pkg-option-desc"><?= $fr ? 'Des modalités et intégrations adaptées aux opérations complexes ou à fort volume.' : 'Tailored terms and integrations for complex or high-volume operations.' ?></p>
          </span>
        </label>

      </div>
      <p class="pkg-error" id="pkgError"><?= $fr ? 'Veuillez sélectionner un forfait pour continuer.' : 'Please select a package to continue.' ?></p>
    </div>

    <!-- Section 4: Document Uploads -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-file-alt"></i>
        <?= $fr ? 'Documents de vérification' : 'Verification Documents' ?>
        <span style="margin-left:10px;background:#f0fdf4;color:#059669;border:1px solid #bbf7d0;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;vertical-align:middle;"><?= $fr ? 'Facultatif' : 'Optional' ?></span>
      </div>
      <p class="form-section-desc"><?= $fr ? 'Téléversez vos documents d\'entreprise maintenant ou complétez la vérification plus tard à partir de Vendeur Central.' : 'Upload your Québec business documents now, or complete verification later from Seller Central.' ?></p>

      <div class="doc-upload-group">
        <label><?= $fr ? 'Certificat de constitution' : 'Certificate of Incorporation' ?></label>
        <div class="doc-upload-area" id="dropArea1">
          <input type="file" name="doc_certificate_incorporation" id="doc_certificate" accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this, 'dropArea1')">
          <div class="doc-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <div class="doc-upload-text"><strong><?= $fr ? 'Cliquez pour téléverser' : 'Click to upload' ?></strong> <?= $fr ? 'ou glissez-déposez' : 'or drag and drop' ?></div>
          <div class="doc-file-name" id="fileName1"></div>
        </div>
        <div class="doc-upload-hint"><?= $fr ? 'PDF, JPG ou PNG (max 5 Mo)' : 'PDF, JPG or PNG (max 5MB)' ?></div>
      </div>

      <div class="doc-upload-group">
        <label><?= $fr ? "Déclaration d'immatriculation" : 'Declaration of Registration' ?></label>
        <div class="doc-upload-area" id="dropArea2">
          <input type="file" name="doc_declaration_registration" id="doc_declaration" accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this, 'dropArea2')">
          <div class="doc-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <div class="doc-upload-text"><strong><?= $fr ? 'Cliquez pour téléverser' : 'Click to upload' ?></strong> <?= $fr ? 'ou glissez-déposez' : 'or drag and drop' ?></div>
          <div class="doc-file-name" id="fileName2"></div>
        </div>
        <div class="doc-upload-hint"><?= $fr ? 'Pour les entreprises individuelles ou sociétés de personnes (max 5 Mo)' : 'For sole proprietorships or partnerships (max 5MB)' ?></div>
      </div>

      <div class="doc-upload-group">
        <label><?= $fr ? 'Dossier du registre des entreprises' : 'Enterprise Register File' ?></label>
        <div class="doc-upload-area" id="dropArea3">
          <input type="file" name="doc_enterprise_register" id="doc_register" accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this, 'dropArea3')">
          <div class="doc-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <div class="doc-upload-text"><strong><?= $fr ? 'Cliquez pour téléverser' : 'Click to upload' ?></strong> <?= $fr ? 'ou glissez-déposez' : 'or drag and drop' ?></div>
          <div class="doc-file-name" id="fileName3"></div>
        </div>
        <div class="doc-upload-hint"><?= $fr ? 'Fiche publique du Registraire des entreprises du Québec (max 5 Mo)' : 'Public Québec enterprise register record (max 5MB)' ?></div>
      </div>

      <div class="info-box">
        <p>
          <i class="fas fa-shield-alt"></i>
          <?= $fr ? 'Vos documents sont conservés de façon sécurisée et utilisés uniquement à des fins de vérification. Ils ne seront pas partagés avec des tiers.' : 'Your documents are securely stored and only used for verification purposes. They will not be shared with third parties.' ?>
        </p>
      </div>
    </div>

    <!-- Section 5: Create Your Password -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-lock"></i>
        <?= $fr ? 'Créez votre mot de passe' : 'Create Your Account Password' ?>
      </div>
      <p class="form-section-desc"><?= $fr ? 'Créez un mot de passe sécurisé pour accéder à Vendeur Central.' : 'Create a secure password for access to Seller Central.' ?></p>

      <div class="form-row">
        <div class="form-group">
          <label><?= $fr ? 'Mot de passe' : 'Password' ?> <span class="required">*</span></label>
          <div style="position: relative;">
            <input type="password" name="password" id="applyPassword" required minlength="10" placeholder="<?= $fr ? '10 caractères minimum' : 'Minimum 10 characters' ?>">
            <button type="button" class="password-toggle" onclick="togglePassword('applyPassword', this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;font-size:16px;">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>
        <div class="form-group">
          <label><?= $fr ? 'Confirmer le mot de passe' : 'Confirm Password' ?> <span class="required">*</span></label>
          <div style="position: relative;">
            <input type="password" name="password_confirmation" id="applyPasswordConfirm" required minlength="10" placeholder="<?= $fr ? 'Ressaisissez votre mot de passe' : 'Re-enter your password' ?>">
            <button type="button" class="password-toggle" onclick="togglePassword('applyPasswordConfirm', this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;font-size:16px;">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>
      </div>
      <div id="pwStrengthBox" style="margin-top:10px;"></div>
      <p id="passwordMatchError" style="display:none; color:#ef4444; font-size:13px; margin-top:4px;">
        <i class="fas fa-exclamation-circle"></i> <?= $fr ? 'Les mots de passe ne correspondent pas' : 'Passwords do not match' ?>
      </p>
    </div>

    <!-- Checkboxes -->
    <div class="checkbox-group">
      <input type="checkbox" name="terms" id="terms" required>
      <label for="terms">
        <?= $fr ? "J'accepte les" : 'I agree to the' ?>
        <a href="<?= url('terms') ?>" target="_blank"><?= $fr ? "Conditions d'utilisation" : 'Terms of Service' ?></a>
        <?= $fr ? 'et la' : 'and' ?>
        <a href="<?= url('privacy') ?>" target="_blank"><?= $fr ? 'Politique de confidentialité' : 'Privacy Policy' ?></a>
      </label>
    </div>
    <div class="checkbox-group">
      <input type="checkbox" name="seller_agreement" id="seller_agreement" required>
      <label for="seller_agreement">
        <?= $fr ? "J'ai lu et j'accepte l'" : 'I have read and agree to the' ?>
        <a href="<?= url('seller-agreement') ?>" target="_blank"><?= $fr ? 'Entente vendeur' : 'Seller Agreement' ?></a>.
      </label>
    </div>

    <!-- Submit -->
    <div class="submit-section">
      <button type="submit" class="btn-submit" id="submitBtn">
        <span class="spinner"></span>
        <span class="btn-text"><i class="fas fa-paper-plane"></i> <?= $fr ? 'Créer le compte vendeur' : 'Create Seller Account' ?></span>
      </button>
    </div>
  </form>

  <!-- Bottom links -->
  <div class="apply-links">
    <?= $fr ? 'Vous avez déjà un compte vendeur?' : 'Already have a seller account?' ?> <a href="<?= url('seller/login') ?>"><?= $fr ? 'Se connecter' : 'Sign In' ?></a>
  </div>

    </div>
  </section>
</main>

<script>
function updateNeqCounter(input) {
  input.value = input.value.replace(/[^0-9]/g, '');
  const counter = document.getElementById('neqCounter');
  counter.textContent = input.value.length + '/10';
  counter.style.color = input.value.length === 10 ? '#00b207' : '#9ca3af';
}

function handleFileSelect(input, areaId) {
  const area = document.getElementById(areaId);
  const fileNameEl = area.querySelector('.doc-file-name');

  if (input.files && input.files[0]) {
    const file = input.files[0];

    if (file.size > 5 * 1024 * 1024) {
      alert('<?= $fr ? 'Le fichier doit faire moins de 5 Mo' : 'File size must be less than 5MB' ?>');
      input.value = '';
      return;
    }

    const ext = file.name.split('.').pop().toLowerCase();
    if (!['pdf', 'jpg', 'jpeg', 'png'].includes(ext)) {
      alert('<?= $fr ? 'Seuls les fichiers PDF, JPG et PNG sont acceptés' : 'Only PDF, JPG, and PNG files are allowed' ?>');
      input.value = '';
      return;
    }

    area.classList.add('has-file');
    fileNameEl.textContent = file.name;
  } else {
    area.classList.remove('has-file');
    fileNameEl.textContent = '';
  }
}

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

const pwRules = [
  { id: 'rule-length',  test: pw => pw.length >= 10,          label: '<?= $fr ? '10 caractères minimum' : 'At least 10 characters' ?>' },
  { id: 'rule-upper',   test: pw => /[A-Z]/.test(pw),         label: '<?= $fr ? 'Une lettre majuscule (A-Z)' : 'One uppercase letter (A-Z)' ?>' },
  { id: 'rule-lower',   test: pw => /[a-z]/.test(pw),         label: '<?= $fr ? 'Une lettre minuscule (a-z)' : 'One lowercase letter (a-z)' ?>' },
  { id: 'rule-number',  test: pw => /[0-9]/.test(pw),         label: '<?= $fr ? 'Un chiffre (0-9)' : 'One number (0-9)' ?>' },
  { id: 'rule-special', test: pw => /[^A-Za-z0-9]/.test(pw), label: '<?= $fr ? 'Un caractère spécial (!@#$%^&*)' : 'One special character (!@#$%^&*)' ?>' },
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

document.getElementById('applyPassword')?.addEventListener('input', function() {
  updateStrength(this.value);
  const confirm = document.getElementById('applyPasswordConfirm');
  if (confirm.value.length > 0) {
    document.getElementById('passwordMatchError').style.display =
      (this.value !== confirm.value) ? 'block' : 'none';
  }
});

document.getElementById('applyPasswordConfirm')?.addEventListener('input', function() {
  const pw = document.getElementById('applyPassword').value;
  document.getElementById('passwordMatchError').style.display =
    (this.value.length > 0 && this.value !== pw) ? 'block' : 'none';
});

document.getElementById('sellerApplicationForm')?.addEventListener('submit', function(e) {
  const btn = document.getElementById('submitBtn');
  const neq = document.getElementById('neqInput');

  const pkgSelected = document.querySelector('input[name="subscription_package"]:checked');
  if (!pkgSelected) {
    e.preventDefault();
    document.getElementById('pkgError').style.display = 'block';
    document.querySelector('.pkg-selector-grid').scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }
  document.getElementById('pkgError').style.display = 'none';

  if (neq.value.length !== 10) {
    e.preventDefault();
    neq.classList.add('error');
    neq.focus();
    alert('<?= $fr ? 'Le NEQ doit contenir exactement 10 chiffres' : 'NEQ must be exactly 10 digits' ?>');
    return;
  }

  const pw = document.getElementById('applyPassword').value;
  const pwConfirm = document.getElementById('applyPasswordConfirm').value;
  if (pw !== pwConfirm) {
    e.preventDefault();
    document.getElementById('passwordMatchError').style.display = 'block';
    document.getElementById('applyPasswordConfirm').focus();
    return;
  }
  if (pw.length < 10) {
    e.preventDefault();
    document.getElementById('applyPassword').focus();
    return;
  }
  const failedRules = pwRules.filter(r => !r.test(pw));
  if (failedRules.length > 0) {
    e.preventDefault();
    document.getElementById('applyPassword').focus();
    return;
  }
  document.getElementById('passwordMatchError').style.display = 'none';

  btn.classList.add('loading');
  btn.disabled = true;
});

buildStrengthUI();

document.addEventListener('DOMContentLoaded', function() {
  const neqInput = document.getElementById('neqInput');
  if (neqInput && neqInput.value) {
    updateNeqCounter(neqInput);
  }
});
</script>

</body>
</html>
