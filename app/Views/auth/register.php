<?php
/**
 * OCSAPP Register Page - WITH CART PRESERVATION
 * File: app/Views/auth/register.php
 */

$currentLang = $_SESSION['language'] ?? 'fr';
$redirect    = isset($_GET['redirect']) ? sanitize($_GET['redirect']) : '';
$urlRole     = sanitize($_GET['role'] ?? '');

$translations = [
    'en' => [
        'create_account'      => 'Create Account',
        'join_ocs'            => 'Join OCSAPP and start ordering',
        'sign_up'             => 'Sign Up',
        'first_name'          => 'First Name',
        'last_name'           => 'Last Name',
        'email'               => 'Email Address',
        'phone'               => 'Phone Number',
        'password'            => 'Password',
        'confirm_password'    => 'Confirm Password',
        'register_as'         => 'I want to register as',
        'buyer'               => 'Buyer (Shop & Order)',
        'seller'              => 'Seller (Sell Products)',
        'delivery'            => 'Delivery Staff',
        'affiliate'           => 'Affiliate Marketer',
        'agree_to'            => 'I agree to the',
        'terms'               => 'Terms of Service',
        'and'                 => 'and',
        'privacy'             => 'Privacy Policy',
        'create_btn'          => 'Create Account',
        'have_account'        => 'Already have an account?',
        'sign_in'             => 'Sign in',
        'all_rights'          => 'All Rights Reserved',
        'tagline'             => 'Zero-Emission Grocery Delivery',
        'required'            => 'Required',
        'optional'            => 'Optional',
        'min_8_chars'         => 'Minimum 8 characters',
        'email_placeholder'   => 'you@example.com',
        'phone_placeholder'   => '+1-514-555-0000',
        'password_placeholder'=> '••••••••',
        'first_name_placeholder' => 'Juan',
        'last_name_placeholder'  => 'Perez',
        'checkout_notice_title'  => 'Complete Your Order',
        'checkout_notice_desc'   => 'Create an account to proceed with your',
        'items_order'            => 'item(s) order',
        'create_and_checkout'    => 'Create Account & Checkout',
    ],
    'fr' => [
        'create_account'      => 'Créer un compte',
        'join_ocs'            => 'Rejoignez OCSAPP et commencez à commander',
        'sign_up'             => 'S\'inscrire',
        'first_name'          => 'Prénom',
        'last_name'           => 'Nom',
        'email'               => 'Adresse e-mail',
        'phone'               => 'Téléphone',
        'password'            => 'Mot de passe',
        'confirm_password'    => 'Confirmer le mot de passe',
        'register_as'         => 'Je veux m\'inscrire comme',
        'buyer'               => 'Acheteur (Magasiner)',
        'seller'              => 'Vendeur (Vendre)',
        'delivery'            => 'Livreur',
        'affiliate'           => 'Affilié',
        'agree_to'            => 'J\'accepte les',
        'terms'               => 'Conditions d\'utilisation',
        'and'                 => 'et',
        'privacy'             => 'Politique de confidentialité',
        'create_btn'          => 'Créer un compte',
        'have_account'        => 'Vous avez déjà un compte?',
        'sign_in'             => 'Se connecter',
        'all_rights'          => 'Tous droits réservés',
        'tagline'             => 'Livraison d\'épicerie zéro émission',
        'required'            => 'Requis',
        'optional'            => 'Optionnel',
        'min_8_chars'         => 'Minimum 8 caractères',
        'email_placeholder'   => 'vous@exemple.com',
        'phone_placeholder'   => '+1-514-555-0000',
        'password_placeholder'=> '••••••••',
        'first_name_placeholder' => 'Juan',
        'last_name_placeholder'  => 'Perez',
        'checkout_notice_title'  => 'Complétez votre commande',
        'checkout_notice_desc'   => 'Créez un compte pour procéder avec votre commande de',
        'items_order'            => 'article(s)',
        'create_and_checkout'    => 'Créer un compte et commander',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

// Hero config per role
$fr = ($currentLang === 'fr');
$heroConfig = [
    'seller' => [
        'badge'    => $fr ? 'Portail Vendeur'   : 'Seller Portal',
        'icon'     => 'fa-shop',
        'title'    => $fr ? 'Vendez sur <span>OCSAPP</span>'    : 'Sell on <span>OCSAPP</span>',
        'subtitle' => $fr ? 'Créez votre compte vendeur et commencez à lister vos produits dès aujourd\'hui.'
                          : 'Create your seller account and start listing your products today.',
        'back_url' => url('seller-central'),
        'back_lbl' => $fr ? 'Retour à Seller Central' : 'Back to Seller Central',
    ],
    'default' => [
        'badge'    => $fr ? 'Portail Acheteur'  : 'Buyer Account',
        'icon'     => 'fa-user',
        'title'    => $fr ? 'Rejoindre <span>OCSAPP</span>'     : 'Join <span>OCSAPP</span>',
        'subtitle' => $fr ? 'Créez votre compte et commencez à magasiner l\'épicerie zéro émission.'
                          : 'Create your account and start shopping zero-emission groceries.',
        'back_url' => url('/'),
        'back_lbl' => $fr ? 'Retour à l\'accueil' : 'Back to Home',
    ],
];
$hero = $heroConfig[$urlRole] ?? $heroConfig['default'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $t['create_account'] ?> – OCSAPP</title>
  <?= csrfMeta() ?>

  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">

  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Inter', 'Segoe UI', sans-serif; }
    main.page { background: #f3f4f6; }

    /* Dark hero banner */
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

    /* Page wrapper */
    .apply-page {
      max-width: 660px;
      margin: 0 auto;
      padding: 32px 20px 64px;
    }

    /* Form section card */
    .form-section {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-top: 3px solid #00b207;
      border-radius: 14px;
      padding: 32px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    /* Checkout notice */
    .checkout-notice {
      background: #f0fdf4;
      border-left: 4px solid #00b207;
      padding: 14px 18px;
      border-radius: 10px;
      margin-bottom: 24px;
    }
    .checkout-notice-title {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 15px;
      font-weight: 700;
      color: #166534;
      margin-bottom: 4px;
    }
    .checkout-notice-desc { font-size: 13px; color: #4b5563; }

    /* Flash messages */
    .alert {
      padding: 14px 18px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-size: 14px;
      font-weight: 500;
    }
    .alert-error { background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; }
    .alert-info  { background: #eff6ff; border-left: 4px solid #3b82f6; color: #1e40af; }

    /* Form layout */
    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
      margin-bottom: 0;
    }
    .form-group { margin-bottom: 16px; }
    .form-group.full-width { grid-column: 1 / -1; }

    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 6px;
    }
    .required { color: #ef4444; }

    .input-wrapper { position: relative; }
    .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 16px;
      pointer-events: none;
    }

    .form-input,
    .form-select {
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
    .form-input.with-icon { padding-left: 40px; }
    .form-input:focus,
    .form-select:focus {
      outline: none;
      border-color: #00b207;
      box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
    }
    .form-input::placeholder { color: #9ca3af; }
    .form-select { cursor: pointer; }
    .form-hint { margin-top: 4px; font-size: 12px; color: #9ca3af; }

    /* Driver notice */
    .driver-notice {
      background: #eff6ff;
      border-left: 4px solid #3b82f6;
      border-radius: 8px;
      padding: 14px 16px;
      margin-top: 10px;
      display: none;
    }
    .driver-notice-title {
      font-size: 13px;
      font-weight: 700;
      color: #1e40af;
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .driver-notice-desc { font-size: 13px; color: #1e40af; line-height: 1.6; margin-bottom: 10px; }
    .driver-notice-desc a { color: #1e40af; font-weight: 700; text-decoration: underline; }
    .btn-apply-driver {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #3b82f6;
      color: #fff;
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 50px;
      font-size: 13px;
      font-weight: 700;
      transition: background 0.2s;
    }
    .btn-apply-driver:hover { background: #2563eb; }

    /* Checkboxes */
    .checkbox-wrapper {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-bottom: 16px;
    }
    .form-checkbox {
      width: 16px;
      height: 16px;
      margin-top: 3px;
      cursor: pointer;
      accent-color: #00b207;
      flex-shrink: 0;
    }
    .checkbox-label { font-size: 13px; color: #374151; line-height: 1.6; }
    .checkbox-label a { color: #00b207; font-weight: 600; text-decoration: none; }
    .checkbox-label a:hover { text-decoration: underline; }

    /* Submit button */
    .btn-submit {
      width: 100%;
      padding: 15px;
      background: #00b207;
      color: #fff;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      font-family: inherit;
      margin-top: 8px;
    }
    .btn-submit:hover {
      background: #009206;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0,178,7,0.25);
    }
    .btn-submit:active { transform: translateY(0); }
    .btn-submit:disabled {
      opacity: 0.45;
      cursor: not-allowed;
      transform: none !important;
      box-shadow: none !important;
    }

    /* Sign in link */
    .apply-links {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #6b7280;
    }
    .apply-links a { color: #00b207; font-weight: 600; text-decoration: none; }
    .apply-links a:hover { text-decoration: underline; }

    /* Responsive */
    @media (max-width: 640px) {
      .apply-hero { padding: 48px 20px 36px; }
      .apply-hero-back { font-size: 12px; }
      .apply-page { padding: 20px 16px 48px; }
      .form-section { padding: 20px 16px; }
      .form-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/../components/header.php'; ?>

<!-- Dark hero banner -->
<div class="apply-hero">
  <a href="<?= $hero['back_url'] ?>" class="apply-hero-back">
    <i class="fas fa-arrow-left"></i>
    <?= $hero['back_lbl'] ?>
  </a>
  <div class="apply-hero-badge">
    <i class="fas <?= $hero['icon'] ?>"></i> <?= $hero['badge'] ?>
  </div>
  <h1><?= $hero['title'] ?></h1>
  <p><?= $hero['subtitle'] ?></p>
</div>

<main class="page">
<div class="apply-page">

  <!-- Checkout notice (only when redirecting from checkout) -->
  <?php if ($redirect === '/checkout' && isset($_SESSION['pending_checkout_cart'])): ?>
  <div class="checkout-notice">
    <div class="checkout-notice-title">
      <span>🛒</span>
      <span><?= $t['checkout_notice_title'] ?></span>
    </div>
    <div class="checkout-notice-desc">
      <?= $t['checkout_notice_desc'] ?> <strong><?= count($_SESSION['pending_checkout_cart']) ?> <?= $t['items_order'] ?></strong>
    </div>
  </div>
  <?php endif; ?>

  <!-- Flash messages -->
  <?php if (hasFlash('error')): ?>
    <div class="alert alert-error"><?= htmlspecialchars(getFlash('error')) ?></div>
  <?php endif; ?>
  <?php if (hasFlash('info')): ?>
    <div class="alert alert-info"><?= htmlspecialchars(getFlash('info')) ?></div>
  <?php endif; ?>

  <div class="form-section">
    <form method="POST" action="<?= url('register') ?>">
      <?= csrfField() ?>
      <?php if ($redirect): ?>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
      <?php endif; ?>

      <!-- Name -->
      <div class="form-grid">
        <div class="form-group">
          <label for="first_name" class="form-label"><?= $t['first_name'] ?> <span class="required">*</span></label>
          <input type="text" id="first_name" name="first_name"
            value="<?= htmlspecialchars(old('first_name') ?? '') ?>"
            class="form-input" placeholder="<?= $t['first_name_placeholder'] ?>" required>
        </div>
        <div class="form-group">
          <label for="last_name" class="form-label"><?= $t['last_name'] ?> <span class="required">*</span></label>
          <input type="text" id="last_name" name="last_name"
            value="<?= htmlspecialchars(old('last_name') ?? '') ?>"
            class="form-input" placeholder="<?= $t['last_name_placeholder'] ?>" required>
        </div>
      </div>

      <!-- Email -->
      <div class="form-group">
        <label for="email" class="form-label"><?= $t['email'] ?> <span class="required">*</span></label>
        <div class="input-wrapper">
          <span class="input-icon">📧</span>
          <input type="email" id="email" name="email"
            value="<?= htmlspecialchars(old('email') ?? '') ?>"
            class="form-input with-icon" placeholder="<?= $t['email_placeholder'] ?>" required>
        </div>
      </div>

      <!-- Phone -->
      <div class="form-group">
        <label for="phone" class="form-label"><?= $t['phone'] ?></label>
        <div class="input-wrapper">
          <span class="input-icon">📱</span>
          <input type="tel" id="phone" name="phone"
            value="<?= htmlspecialchars(old('phone') ?? '') ?>"
            class="form-input with-icon" placeholder="<?= $t['phone_placeholder'] ?>">
        </div>
      </div>

      <!-- Role + Password -->
      <div class="form-grid">
        <div class="form-group">
          <label for="role" class="form-label"><?= $t['register_as'] ?> <span class="required">*</span></label>
          <select id="role" name="role" class="form-select" required>
            <option value="buyer"    <?= old('role') === 'buyer'    ? 'selected' : '' ?>><?= $t['buyer'] ?></option>
            <option value="seller"   <?= old('role') === 'seller'   ? 'selected' : '' ?>><?= $t['seller'] ?></option>
            <option value="delivery" <?= old('role') === 'delivery' ? 'selected' : '' ?>><?= $t['delivery'] ?></option>
            <option value="affiliate"<?= old('role') === 'affiliate'? 'selected' : '' ?>><?= $t['affiliate'] ?></option>
          </select>
          <div class="driver-notice" id="driverNotice">
            <div class="driver-notice-title">
              <i class="fa-solid fa-truck"></i>
              <?= $fr ? 'Rejoindre notre équipe de livraison' : 'Join Our Delivery Team' ?>
            </div>
            <div class="driver-notice-desc">
              <?= $fr
                ? 'Les chauffeurs-livreurs ne s\'inscrivent pas ici. Vous devez d\'abord soumettre une demande via notre portail Driver Central, où notre équipe examinera votre candidature.'
                : 'Delivery drivers don\'t sign up here. You need to submit an application through our Driver Central first, where our team will review your candidacy.' ?>
            </div>
            <a href="<?= url('delivery/apply') ?>" class="btn-apply-driver">
              <i class="fa-solid fa-paper-plane"></i>
              <?= $fr ? 'Soumettre ma candidature' : 'Submit My Application' ?>
            </a>
          </div>
        </div>

        <div class="form-group">
          <label for="password" class="form-label"><?= $t['password'] ?> <span class="required">*</span></label>
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input type="password" id="password" name="password"
              class="form-input with-icon"
              placeholder="<?= $t['password_placeholder'] ?>" minlength="8" required>
          </div>
          <p class="form-hint"><?= $t['min_8_chars'] ?></p>
        </div>
      </div>

      <!-- Confirm password -->
      <div class="form-group">
        <label for="password_confirmation" class="form-label"><?= $t['confirm_password'] ?> <span class="required">*</span></label>
        <div class="input-wrapper">
          <span class="input-icon">🔒</span>
          <input type="password" id="password_confirmation" name="password_confirmation"
            class="form-input with-icon"
            placeholder="<?= $t['password_placeholder'] ?>" required>
        </div>
      </div>

      <!-- Terms -->
      <div class="checkbox-wrapper">
        <input type="checkbox" id="terms" name="terms" class="form-checkbox" required>
        <label for="terms" class="checkbox-label">
          <?= $t['agree_to'] ?> <a href="<?= url('terms') ?>"><?= $t['terms'] ?></a>
          <?= $t['and'] ?> <a href="<?= url('privacy') ?>"><?= $t['privacy'] ?></a>
        </label>
      </div>

      <!-- Seller agreement (conditional) -->
      <div class="checkbox-wrapper" id="sellerAgreementSection" style="display:none;">
        <input type="checkbox" id="seller_agreement" name="seller_agreement" class="form-checkbox">
        <label for="seller_agreement" class="checkbox-label">
          <?= $fr ? "J'ai lu et j'accepte le" : 'I have read and agree to the' ?>
          <a href="<?= url('seller-agreement') ?>" target="_blank">
            <?= $fr ? 'Contrat de vendeur' : 'Seller Agreement' ?>
          </a>
        </label>
      </div>

      <button type="submit" class="btn-submit">
        <?= $redirect === '/checkout' ? '🛒 ' . $t['create_and_checkout'] : $t['create_btn'] ?>
      </button>
    </form>
  </div>

  <div class="apply-links">
    <?= $t['have_account'] ?>
    <a href="<?= url('login' . ($redirect ? '?redirect=' . urlencode($redirect) : '')) ?>"><?= $t['sign_in'] ?></a>
  </div>

</div>
</main>

<?php include __DIR__ . '/../components/footer.php'; ?>

<script>
  // Password match validation
  const passwordInput = document.getElementById('password');
  const confirmInput  = document.getElementById('password_confirmation');
  confirmInput.addEventListener('input', function() {
    this.setCustomValidity(
      (passwordInput.value !== this.value && this.value.length > 0)
        ? '<?= $fr ? "Les mots de passe ne correspondent pas" : "Passwords do not match" ?>'
        : ''
    );
  });

  // Role change handler
  const roleSelect              = document.getElementById('role');
  const sellerAgreementSection  = document.getElementById('sellerAgreementSection');
  const sellerAgreementCheckbox = document.getElementById('seller_agreement');
  const driverNotice            = document.getElementById('driverNotice');
  const submitBtn               = document.querySelector('.btn-submit');

  function onRoleChange() {
    const val      = roleSelect.value;
    const isSeller = val === 'seller';
    const isDriver = val === 'delivery';

    sellerAgreementSection.style.display = isSeller ? 'flex' : 'none';
    sellerAgreementCheckbox.required     = isSeller;
    if (!isSeller) sellerAgreementCheckbox.checked = false;

    if (driverNotice) driverNotice.style.display = isDriver ? 'block' : 'none';
    if (submitBtn) {
      submitBtn.disabled = isDriver;
      submitBtn.title = isDriver
        ? '<?= $fr ? "Veuillez soumettre une candidature via Driver Central" : "Please apply via Driver Central instead" ?>'
        : '';
    }
  }

  roleSelect.addEventListener('change', onRoleChange);

  // Pre-select role from URL param on first load
  const urlRole = new URLSearchParams(window.location.search).get('role');
  if (urlRole) {
    const opt = roleSelect.querySelector(`option[value="${urlRole}"]`);
    if (opt) opt.selected = true;
  }
  onRoleChange();
</script>
</body>
</html>
<?php clearOldInput(); ?>
