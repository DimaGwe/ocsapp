<?php
/**
 * Supplier Application Form
 * Public-facing form for businesses to apply as OCSAPP suppliers
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$t = getTranslations($currentLang);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? 'Devenir fournisseur' : 'Become a Supplier' ?> - OCSAPP</title>
  <?= csrfMeta() ?>

  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">

  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Inter', 'Segoe UI', sans-serif; }
    main.page { background: #f3f4f6; }
    .top-banner { margin-bottom: 0; }
    .header { margin-bottom: 0; }
    footer.footer { margin-top: 0; }

    /* ── Dark hero banner ── */
    .apply-hero {
      background: linear-gradient(135deg, #0a1628 0%, #0d2137 50%, #071220 100%);
      color: white;
      text-align: center;
      padding: 56px 24px 48px;
      position: relative;
      overflow: hidden;
    }
    .apply-hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      pointer-events: none;
    }
    .apply-hero-badge {
      display: inline-block;
      background: rgba(0,178,7,0.18);
      color: #4ade80;
      border: 1px solid rgba(0,178,7,0.35);
      padding: 6px 18px;
      border-radius: 50px;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.5px;
      margin-bottom: 20px;
    }
    .apply-hero h1 {
      font-size: clamp(24px, 4vw, 36px);
      font-weight: 800;
      color: white;
      margin-bottom: 10px;
      line-height: 1.2;
    }
    .apply-hero h1 span { color: #4ade80; }
    .apply-hero p {
      font-size: 15px;
      color: rgba(255,255,255,0.72);
      max-width: 480px;
      margin: 0 auto;
      line-height: 1.6;
    }
    .apply-hero-back {
      position: absolute;
      top: 20px;
      left: 24px;
      color: rgba(255,255,255,0.55);
      font-size: 13px;
      font-weight: 500;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: color 0.2s;
    }
    .apply-hero-back:hover { color: #4ade80; }

    /* ── Page wrapper ── */
    .apply-page {
      max-width: 700px;
      margin: 0 auto;
      padding: 32px 20px 64px;
    }

    /* ── Form sections ── */
    .form-section {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-top: 3px solid #00b207;
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
    .form-section-desc {
      font-size: 13px;
      color: #9ca3af;
      margin-bottom: 20px;
    }

    /* ── Form layout ── */
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 0;
    }
    .form-row.single { grid-template-columns: 1fr; }
    .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 16px;
    }
    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 6px;
    }
    .form-group label .required { color: #ef4444; }

    /* ── Inputs ── */
    .form-group input,
    .form-group textarea,
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
    .form-group textarea:focus,
    .form-group select:focus {
      outline: none;
      border-color: #00b207;
      box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
    }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .form-group input.error,
    .form-group select.error { border-color: #ef4444; }
    .form-group .hint,
    .hint {
      display: block;
      font-size: 12px;
      color: #9ca3af;
      margin-top: 4px;
    }
    .error-text { color: #ef4444; font-size: 12px; margin-top: 4px; }

    /* ── NEQ counter ── */
    .neq-input-wrapper { position: relative; }
    .neq-input-wrapper input { padding-right: 52px; }
    .neq-counter {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 12px;
      color: #9ca3af;
      pointer-events: none;
    }

    /* ── Password ── */
    .pw-input-wrap { position: relative; }
    .pw-input-wrap input { padding-right: 44px; }
    .pw-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #9ca3af;
      cursor: pointer;
      font-size: 16px;
      padding: 0;
      line-height: 1;
    }
    .pw-toggle:hover { color: #6b7280; }
    .pw-rule {
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 2px 0;
      transition: color 0.2s;
    }
    .pw-rule-fail { color: #9ca3af; }
    .pw-rule-ok   { color: #00b207; }
    .pw-rule i    { font-size: 12px; }

    /* ── Document upload ── */
    .doc-upload-group { margin-bottom: 20px; }
    .doc-upload-group > label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 8px;
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
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }
    .doc-upload-icon { font-size: 28px; color: #9ca3af; margin-bottom: 8px; }
    .doc-upload-area.has-file .doc-upload-icon { color: #00b207; }
    .doc-upload-text { font-size: 14px; color: #6b7280; }
    .doc-upload-text strong { color: #374151; }
    .doc-file-name {
      display: none;
      margin-top: 6px;
      font-size: 13px;
      color: #00b207;
      font-weight: 600;
      word-break: break-all;
    }
    .doc-upload-area.has-file .doc-file-name { display: block; }
    .doc-upload-hint { font-size: 12px; color: #9ca3af; margin-top: 6px; }

    /* ── Info box (green, brand-consistent) ── */
    .info-box {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: 8px;
      padding: 12px 16px;
      margin-top: 8px;
      margin-bottom: 0;
      font-size: 13px;
      color: #166534;
      line-height: 1.6;
    }
    .info-box p { margin: 0; }
    .info-box p + p { margin-top: 8px; }
    .info-box i { margin-right: 6px; }
    .info-box a { color: #15803d; font-weight: 600; }

    /* ── Checkbox ── */
    .checkbox-group {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin: 8px 0 20px;
    }
    .checkbox-group input[type="checkbox"] {
      width: 18px;
      height: 18px;
      margin-top: 2px;
      accent-color: #00b207;
      flex-shrink: 0;
    }
    .checkbox-group label { font-size: 13px; color: #6b7280; line-height: 1.6; }
    .checkbox-group label a { color: #00b207; font-weight: 600; text-decoration: none; }
    .checkbox-group label a:hover { text-decoration: underline; }

    /* ── Submit ── */
    .submit-section { padding-top: 8px; }
    .btn-submit {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 15px 24px;
      background: linear-gradient(135deg, #00b207 0%, #008505 100%);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s;
      font-family: inherit;
    }
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0,178,7,0.3);
    }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .btn-submit .spinner {
      display: none;
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255,255,255,0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.6s linear infinite;
    }
    .btn-submit.loading .spinner { display: inline-block; }
    .btn-submit.loading .btn-text { display: none; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Flash messages ── */
    .flash-message {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .flash-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

    /* ── Bottom links ── */
    .apply-links {
      text-align: center;
      margin-top: 24px;
      font-size: 14px;
      color: #6b7280;
      line-height: 2;
    }
    .apply-links a { color: #00b207; font-weight: 600; text-decoration: none; }
    .apply-links a:hover { text-decoration: underline; }

    /* ── Success page ── */
    .success-container { text-align: center; padding: 60px 20px; }
    .success-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #00b207 0%, #008505 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      color: white;
      font-size: 36px;
    }
    .success-container h2 { font-size: 28px; font-weight: 800; color: #1a1a1a; margin-bottom: 12px; }
    .success-container p { color: #6b7280; font-size: 16px; line-height: 1.6; max-width: 500px; margin: 0 auto 24px; }

    /* ── Package selector ── */
    .pkg-selector-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
      margin-top: 4px;
    }
    .pkg-option {
      position: relative;
      cursor: pointer;
    }
    .pkg-option input[type="radio"] {
      position: absolute;
      opacity: 0;
      width: 0; height: 0;
    }
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
    .pkg-option-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 6px;
    }
    .pkg-option-name {
      font-size: 14px;
      font-weight: 700;
      color: #111827;
    }
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
    .pkg-option-desc {
      font-size: 12px;
      color: #6b7280;
      line-height: 1.5;
    }
    .pkg-option-popular {
      display: inline-block;
      font-size: 10px; font-weight: 700;
      background: #00b207; color: white;
      padding: 2px 8px; border-radius: 8px;
      margin-left: 6px;
    }
    .pkg-error { color: #ef4444; font-size: 12px; margin-top: 6px; display: none; }

    /* ── Responsive ── */
    @media (max-width: 640px) {
      .apply-hero { padding: 48px 20px 36px; }
      .apply-hero-back { font-size: 12px; }
      .apply-page { padding: 20px 16px 48px; }
      .form-section { padding: 20px 16px; }
      .form-row { grid-template-columns: 1fr; }
      .doc-upload-area { padding: 20px 12px; }
      .pkg-selector-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/../components/header.php'; ?>

<!-- Dark hero banner -->
<div class="apply-hero">
  <a href="<?= url('supplier-central') ?>" class="apply-hero-back">
    <i class="fas fa-arrow-left"></i>
    <?= $fr ? 'Retour au Central Fournisseur' : 'Back to Supplier Central' ?>
  </a>
  <div class="apply-hero-badge">
    <i class="fas fa-store"></i>
    <?= $fr ? 'Portail Fournisseur' : 'Supplier Portal' ?>
  </div>
  <h1><?= $fr ? 'Devenez <span>Fournisseur</span>' : 'Become a <span>Supplier</span>' ?></h1>
  <p><?= $fr ? "Remplissez le formulaire ci-dessous avec les informations de votre entreprise. Notre équipe examinera votre demande et vous répondra dans les plus brefs délais." : 'Fill out the form below with your business information. Our team will review your application and get back to you.' ?></p>
</div>

<main class="page">
<div class="apply-page">

  <?php if (!empty($flash)):
    $flashType = isset($flash['error']) ? 'error' : 'success';
    $flashMessage = $flash[$flashType] ?? '';
  ?>
    <div class="flash-message flash-<?= $flashType ?>">
      <i class="fas fa-<?= $flashType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
      <?= htmlspecialchars($flashMessage) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="form-section">
      <div class="success-container">
        <div class="success-icon">
          <i class="fas fa-check"></i>
        </div>
        <h2><?= $fr ? 'Candidature soumise !' : 'Application Submitted!' ?></h2>
        <p><?= $fr ? "Merci de l'intérêt que vous portez à OCSAPP en tant que fournisseur. Notre équipe examinera votre demande et communiquera avec vous dans un délai de 2 à 3 jours ouvrables." : 'Thank you for your interest in becoming an OCSAPP supplier. Our team will review your application and contact you within 2-3 business days.' ?></p>
        <a href="<?= url('supplier-central') ?>" class="btn-submit" style="text-decoration: none;">
          <i class="fas fa-arrow-left"></i>
          <span><?= $fr ? 'Retour au portail fournisseur' : 'Back to Supplier Portal' ?></span>
        </a>
      </div>
    </div>
  <?php else: ?>

  <form action="<?= url('supplier/apply') ?>" method="POST" enctype="multipart/form-data" id="supplierApplicationForm">
    <?= csrfField() ?>
    <input type="hidden" name="lang" value="<?= $currentLang ?>">

    <!-- Section 1: Business Owner Info -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-user-tie"></i>
        <?= $fr ? 'Informations générales sur le propriétaire' : 'General Business Owner Information' ?>
      </div>
      <p class="form-section-desc"><?= $fr ? 'Parlez-nous de vous et de votre entreprise.' : 'Tell us about yourself and your business.' ?></p>

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
          <input type="email" name="email" required maxlength="255" placeholder="jean@entreprise.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label><?= $fr ? 'Numéro de téléphone' : 'Phone Number' ?> <span class="required">*</span></label>
          <input type="tel" name="phone" required maxlength="50" placeholder="(514) 555-0123" pattern="[\d\s\-\(\)\+]{10,}" title="<?= $fr ? 'Entrez un numéro de téléphone valide (au moins 10 chiffres)' : 'Enter a valid phone number (at least 10 digits)' ?>" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $fr ? "Nom de l'entreprise" : 'Business Name' ?> <span class="required">*</span></label>
          <input type="text" name="business_name" required maxlength="255" placeholder="<?= $fr ? 'Votre entreprise inc.' : 'Your Company Inc.' ?>" value="<?= htmlspecialchars($old['business_name'] ?? '') ?>">
        </div>
      </div>
    </div>

    <!-- Section 2: Quebec Legal Identity -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-landmark"></i>
        <?= $fr ? 'Vérification de l\'identité juridique au Québec' : 'Quebec Legal Identity Verification' ?>
      </div>
      <p class="form-section-desc"><?= $fr ? 'Fournissez les détails de l\'immatriculation de votre entreprise au Québec pour vérification.' : 'Provide your Quebec business registration details for verification.' ?></p>

      <div class="info-box">
        <p>
          <i class="fas fa-info-circle"></i>
          <?= $fr ? 'Les dossiers d\'entreprise sont vérifiés auprès du' : 'Business records are verified through the' ?>
          <a href="https://www.registreentreprises.gouv.qc.ca" target="_blank" rel="noopener">Registraire des entreprises du Qu&eacute;bec</a>.
          <?= $fr ? 'Votre NEQ (numéro d\'entreprise) se trouve sur vos documents d\'immatriculation.' : 'Your NEQ (Enterprise Number) can be found on your business registration documents.' ?>
        </p>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $fr ? 'NEQ (Numéro d\'entreprise)' : 'NEQ (Enterprise Number)' ?> <span class="required">*</span></label>
          <div class="neq-input-wrapper">
            <input type="text" name="neq_number" id="neqInput" required maxlength="10" minlength="10" pattern="[0-9]{10}" placeholder="1234567890" value="<?= htmlspecialchars($old['neq_number'] ?? '') ?>" oninput="updateNeqCounter(this)">
            <span class="neq-counter" id="neqCounter">0/10</span>
          </div>
          <span class="hint"><?= $fr ? 'Numéro d\'identification à 10 chiffres du Registraire des entreprises' : '10-digit identification number from the Registraire des entreprises' ?></span>
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $fr ? 'Dénomination sociale' : 'Legal Name' ?> <span class="required">*</span></label>
          <input type="text" name="legal_name" required maxlength="255" placeholder="<?= $fr ? 'Dénomination sociale complète telle qu\'enregistrée' : 'Full legal name as registered' ?>" value="<?= htmlspecialchars($old['legal_name'] ?? '') ?>">
          <span class="hint"><?= $fr ? 'Doit être conforme à la Charte de la langue française' : 'Must comply with the Charter of the French Language' ?></span>
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label><?= $fr ? 'Nom(s) d\'exploitation' : 'Operating Name(s)' ?></label>
          <input type="text" name="operating_names" maxlength="500" placeholder="<?= $fr ? 'Noms commerciaux ou DBA (si différents de la dénomination sociale)' : 'Trade names or DBA names (if different from legal name)' ?>" value="<?= htmlspecialchars($old['operating_names'] ?? '') ?>">
          <span class="hint"><?= $fr ? 'Séparez plusieurs noms par des virgules' : 'Separate multiple names with commas' ?></span>
        </div>
      </div>

      <div class="form-row single" style="margin-bottom: 8px;">
        <div class="form-group">
          <label><?= $fr ? 'Adresse du siège social' : 'Registered Office Address' ?> <span class="required">*</span></label>
          <input type="text" name="registered_address_street" required maxlength="255" placeholder="<?= $fr ? 'Adresse de rue' : 'Street address' ?>" value="<?= htmlspecialchars($old['registered_address_street'] ?? '') ?>">
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
          <input type="text" name="registered_address_postal" required maxlength="10" placeholder="H2X 1Y4" pattern="[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d" title="<?= $fr ? 'Code postal canadien (ex. H2X 1Y4)' : 'Canadian postal code (e.g. H2X 1Y4)' ?>" value="<?= htmlspecialchars($old['registered_address_postal'] ?? '') ?>">
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
      <p class="form-section-desc"><?= $fr ? 'Sélectionnez le forfait adapté à votre entreprise. Vous pouvez passer à un niveau supérieur en tout temps depuis votre portail fournisseur.' : 'Select the plan that fits your business. You can upgrade anytime from your supplier portal.' ?></p>

      <div class="pkg-selector-grid">

        <label class="pkg-option">
          <input type="radio" name="subscription_package" value="Essential" <?= $preselectedPkg === 'Essential' || $preselectedPkg === '' ? 'checked' : '' ?>>
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

    <!-- Section 4: Document Uploads -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-file-alt"></i>
        <?= $fr ? 'Documents de vérification' : 'Verification Documents' ?>
        <span style="margin-left:10px;background:#f0fdf4;color:#059669;border:1px solid #bbf7d0;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;vertical-align:middle;"><?= $fr ? 'Optionnel' : 'Optional' ?></span>
      </div>
      <p class="form-section-desc"><?= $fr ? "Vous pouvez télécharger vos documents d'enregistrement d'entreprise maintenant ou ultérieurement depuis votre portail fournisseur. Les documents sont requis avant que votre compte puisse être pleinement approuvé." : 'You can upload your business registration documents now or later from your supplier portal. Documents are required before your account can be fully approved.' ?></p>

      <div class="doc-upload-group">
        <label><?= $fr ? 'Certificat de constitution en société' : 'Certificate of Incorporation' ?></label>
        <div class="doc-upload-area" id="dropArea1">
          <input type="file" name="doc_certificate_incorporation" id="doc_certificate" accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this, 'dropArea1')">
          <div class="doc-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <div class="doc-upload-text"><strong><?= $fr ? 'Cliquez pour télécharger' : 'Click to upload' ?></strong> <?= $fr ? 'ou glisser-déposer' : 'or drag and drop' ?></div>
          <div class="doc-file-name" id="fileName1"></div>
        </div>
        <div class="doc-upload-hint"><?= $fr ? 'Pour les corporations. PDF, JPG ou PNG (max 5 Mo)' : 'For corporations. PDF, JPG, or PNG (max 5MB)' ?></div>
      </div>

      <div class="doc-upload-group">
        <label><?= $fr ? "Déclaration d'immatriculation" : 'Declaration of Registration' ?></label>
        <div class="doc-upload-area" id="dropArea2">
          <input type="file" name="doc_declaration_registration" id="doc_declaration" accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this, 'dropArea2')">
          <div class="doc-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <div class="doc-upload-text"><strong><?= $fr ? 'Cliquez pour télécharger' : 'Click to upload' ?></strong> <?= $fr ? 'ou glisser-déposer' : 'or drag and drop' ?></div>
          <div class="doc-file-name" id="fileName2"></div>
        </div>
        <div class="doc-upload-hint"><?= $fr ? 'Pour les entreprises individuelles / sociétés de personnes. PDF, JPG ou PNG (max 5 Mo)' : 'For sole proprietorships / partnerships. PDF, JPG, or PNG (max 5MB)' ?></div>
      </div>

      <div class="doc-upload-group">
        <label><?= $fr ? 'Recherche au registre des entreprises' : 'Enterprise Register File Search' ?></label>
        <div class="doc-upload-area" id="dropArea3">
          <input type="file" name="doc_enterprise_register" id="doc_register" accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this, 'dropArea3')">
          <div class="doc-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <div class="doc-upload-text"><strong><?= $fr ? 'Cliquez pour télécharger' : 'Click to upload' ?></strong> <?= $fr ? 'ou glisser-déposer' : 'or drag and drop' ?></div>
          <div class="doc-file-name" id="fileName3"></div>
        </div>
        <div class="doc-upload-hint"><?= $fr ? 'Disponible publiquement au REQ. PDF, JPG ou PNG (max 5 Mo)' : 'Publicly available from the REQ. PDF, JPG, or PNG (max 5MB)' ?></div>
      </div>

      <div class="info-box">
        <p>
          <i class="fas fa-shield-alt"></i>
          <?= $fr ? 'Vos documents sont conservés de façon sécurisée et utilisés uniquement à des fins de vérification. Ils ne seront pas partagés avec des tiers.' : 'Your documents are securely stored and only used for verification purposes. They will not be shared with third parties.' ?>
        </p>
        <p style="margin-top:8px;">
          <i class="fas fa-folder-open"></i>
          <?= $fr ? 'Vous passez les documents ? Vous pouvez les télécharger en tout temps depuis <strong>Mes documents</strong> dans votre portail fournisseur.' : 'Skipping documents? You can upload them anytime from <strong>My Documents</strong> in your supplier portal.' ?>
        </p>
      </div>
    </div>

    <!-- Section 5: Create Your Password -->
    <div class="form-section">
      <div class="form-section-title">
        <i class="fas fa-lock"></i>
        <?= $fr ? 'Créez votre mot de passe' : 'Create Your Account Password' ?>
      </div>
      <p class="form-section-desc"><?= $fr ? "Définissez un mot de passe pour accéder à votre portail fournisseur immédiatement après votre candidature. Vous aurez un accès limité jusqu'à ce que votre compte soit vérifié par notre équipe." : "Set a password so you can access your supplier portal immediately after applying. You'll have limited access until your account is verified by our team." ?></p>

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

    <!-- Terms checkbox -->
    <div class="checkbox-group">
      <input type="checkbox" name="terms" id="terms" required>
      <label for="terms">
        <?= $fr ? "J'accepte les" : 'I agree to the' ?>
        <a href="<?= url('terms') ?>" target="_blank"><?= $fr ? "Conditions d'utilisation" : 'Terms of Service' ?></a>
        <?= $fr ? 'et la' : 'and' ?>
        <a href="<?= url('privacy') ?>" target="_blank"><?= $fr ? 'Politique de confidentialité' : 'Privacy Policy' ?></a>
      </label>
    </div>

    <!-- Submit -->
    <div class="submit-section">
      <button type="submit" class="btn-submit" id="submitBtn">
        <span class="spinner"></span>
        <span class="btn-text"><i class="fas fa-paper-plane"></i> <?= $fr ? 'Soumettre ma candidature' : 'Submit Application' ?></span>
      </button>
    </div>
  </form>

  <!-- Bottom links -->
  <div class="apply-links">
    <?= $fr ? 'Vous avez déjà un compte ?' : 'Already have an account?' ?> <a href="<?= url('supplier/login') ?>"><?= $fr ? 'Se connecter' : 'Sign In' ?></a>
  </div>

  <?php endif; ?>
</div>
</main>

<?php include __DIR__ . '/../components/footer.php'; ?>

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

document.getElementById('supplierApplicationForm')?.addEventListener('submit', function(e) {
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
