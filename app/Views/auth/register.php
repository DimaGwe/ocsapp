<?php
/**
 * OCSAPP Register Page - WITH CART PRESERVATION
 * File: app/Views/auth/register.php
 */

$currentLang = $_SESSION['language'] ?? 'fr';
$redirect    = isset($_GET['redirect']) ? sanitize($_GET['redirect']) : '';
$urlRole     = sanitize($_GET['role'] ?? '');
$fr = ($currentLang === 'fr');

$translations = [
    'en' => [
        'create_account'      => 'Create Account',
        'sign_up'             => 'Sign Up',
        'first_name'          => 'First Name',
        'last_name'           => 'Last Name',
        'email'               => 'Email Address',
        'phone'               => 'Phone Number',
        'password'            => 'Password',
        'confirm_password'    => 'Confirm Password',
        'register_as'         => 'Register as',
        'buyer'               => 'Buyer',
        'seller'              => 'Seller',
        'delivery'            => 'Delivery',
        'affiliate'           => 'Affiliate',
        'agree_to'            => 'I agree to the',
        'terms'               => 'Terms of Service',
        'and'                 => 'and',
        'privacy'             => 'Privacy Policy',
        'create_btn'          => 'Create Account',
        'have_account'        => "Don't have access yet?",
        'sign_in'             => 'Sign in',
        'email_placeholder'   => 'you@example.com',
        'phone_placeholder'   => '+1-514-555-0000',
        'password_placeholder'=> 'Create a password',
        'first_name_placeholder' => 'Enter first name',
        'last_name_placeholder'  => 'Enter last name',
        'checkout_notice_title'  => 'Complete Your Order',
        'checkout_notice_desc'   => 'Create an account to proceed with your',
        'items_order'            => 'item(s) order',
        'create_and_checkout'    => 'Create Account & Checkout',
    ],
    'fr' => [
        'create_account'      => 'Créer un compte',
        'sign_up'             => "S'inscrire",
        'first_name'          => 'Prénom',
        'last_name'           => 'Nom',
        'email'               => 'Adresse courriel',
        'phone'               => 'Téléphone',
        'password'            => 'Mot de passe',
        'confirm_password'    => 'Confirmer le mot de passe',
        'register_as'         => "S'inscrire comme",
        'buyer'               => 'Acheteur',
        'seller'              => 'Vendeur',
        'delivery'            => 'Livreur',
        'affiliate'           => 'Affilié',
        'agree_to'            => "J'accepte les",
        'terms'               => "Conditions d'utilisation",
        'and'                 => 'et',
        'privacy'             => 'Politique de confidentialité',
        'create_btn'          => 'Créer un compte',
        'have_account'        => "Vous n'avez pas encore accès?",
        'sign_in'             => 'Se connecter',
        'email_placeholder'   => 'vous@exemple.com',
        'phone_placeholder'   => '+1-514-555-0000',
        'password_placeholder'=> 'Entrez votre mot de passe',
        'first_name_placeholder' => 'Entrez votre prénom',
        'last_name_placeholder'  => 'Entrez votre nom',
        'checkout_notice_title'  => 'Complétez votre commande',
        'checkout_notice_desc'   => 'Créez un compte pour procéder avec votre commande de',
        'items_order'            => 'article(s)',
        'create_and_checkout'    => 'Créer un compte et commander',
    ],
];

$t = $translations[$currentLang] ?? $translations['fr'];

// Brand-panel + nav config per role (buyer default, seller when ?role=seller)
$heroConfig = [
    'seller' => [
        'title_tag'   => $fr ? 'Créer un compte - Vendeur Central - OCSAPP' : 'Create account - Seller Central - OCSAPP',
        'eyebrow'     => $fr ? 'VENDEUR CENTRAL' : 'SELLER CENTRAL',
        'icon'        => 'images/about/central-seller.png',
        'central_url' => url('seller-central'),
        'central_lbl' => $fr ? 'Vendeur Central' : 'Seller Central',
        'h1'          => $fr ? 'Vendeur Central' : 'Seller Central',
        'p'           => $fr
            ? 'Créez votre compte vendeur pour gérer votre boutique, vos produits et vos commandes au sein de l\'écosystème OCSAPP.'
            : 'Create your seller account to manage your store, products and orders within the OCSAPP ecosystem.',
        'card_kicker' => $fr ? 'CRÉER VOTRE COMPTE' : 'CREATE YOUR ACCOUNT',
        'card_h2'     => $fr ? 'Rejoindre Vendeur Central' : 'Join Seller Central',
        'card_p'      => $fr
            ? 'Créez votre compte OCSAPP vendeur et complétez le contrat de vendeur requis.'
            : 'Create your OCSAPP seller account and complete the required seller agreement.',
        'login_url'   => url('seller/login'),
        'login_lbl'   => $fr ? 'Vous avez déjà un compte vendeur? Se connecter' : 'Already have a seller account? Sign in',
    ],
    'default' => [
        'title_tag'   => $fr ? 'Créer un compte - Acheteur Central - OCSAPP' : 'Create account - Buyer Central - OCSAPP',
        'eyebrow'     => $fr ? 'ACHETEUR CENTRAL' : 'BUYER CENTRAL',
        'icon'        => 'images/about/central-buyer.png',
        'central_url' => url('buyer-central'),
        'central_lbl' => $fr ? 'Acheteur Central' : 'Buyer Central',
        'h1'          => $fr ? 'Acheteur Central' : 'Buyer Central',
        'p'           => $fr
            ? 'Créez votre compte acheteur pour magasiner chez les commerces locaux et gérer vos activités dans l\'écosystème OCSAPP.'
            : 'Create your buyer account to shop local businesses and manage your activity across the OCSAPP ecosystem.',
        'card_kicker' => $fr ? 'CRÉER VOTRE COMPTE' : 'CREATE YOUR ACCOUNT',
        'card_h2'     => $fr ? 'Rejoindre Acheteur Central' : 'Join Buyer Central',
        'card_p'      => $fr
            ? 'Entrez vos informations ci-dessous pour créer votre compte acheteur OCSAPP.'
            : 'Enter your information below to create your OCSAPP buyer account.',
        'login_url'   => url('login' . ($redirect ? '?redirect=' . urlencode($redirect) : '')),
        'login_lbl'   => $fr ? 'Vous avez déjà un compte? Se connecter' : 'Already have an account? Sign in',
    ],
];
$hero = $heroConfig[$urlRole] ?? $heroConfig['default'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#00B207">
  <title><?= $hero['title_tag'] ?></title>
  <?= csrfMeta() ?>

  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/portal-register.css') ?>">
</head>
<body>
<header class="auth-topbar">
  <a class="auth-logo" href="<?= url('/') ?>" aria-label="OCSAPP">OCSAPP</a>
  <nav class="auth-toplinks" aria-label="<?= $fr ? "Navigation d'inscription" : 'Registration navigation' ?>">
    <a href="<?= url('/') ?>"><?= $fr ? 'Écosystème' : 'Ecosystem' ?></a>
    <a href="<?= url('home') ?>"><?= $fr ? 'Marché Central' : 'Market Central' ?></a>
    <a class="central-link" id="centralLink" href="<?= $hero['central_url'] ?>"><?= $hero['central_lbl'] ?></a>
  </nav>
</header>

<main class="register-shell">
  <section class="brand-panel" aria-label="<?= $hero['h1'] ?>">
    <div class="brand-content">
      <div class="eyebrow" id="heroEyebrow"><?= $hero['eyebrow'] ?></div>
      <div class="central-icon"><img id="heroIcon" src="<?= asset($hero['icon']) ?>" alt="<?= $hero['h1'] ?>"></div>
      <h1 id="heroTitle"><?= $hero['h1'] ?></h1>
      <p id="heroSubtitle"><?= $hero['p'] ?></p>
      <p class="ecosystem-note"><strong><?= $fr ? 'Un seul écosystème OCSAPP.' : 'One OCSAPP ecosystem.' ?></strong><br><?= $fr ? 'Votre compte est lié au Central correspondant à votre rôle.' : 'Your account is connected to the Central associated with your role.' ?></p>
    </div>
  </section>

  <section class="form-panel">
    <div class="register-card">
      <div class="card-head">
        <div class="card-kicker" id="cardKicker"><?= $hero['card_kicker'] ?></div>
        <h2 id="cardH2"><?= $hero['card_h2'] ?></h2>
        <p id="cardP"><?= $hero['card_p'] ?></p>
      </div>

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

      <?php if (hasFlash('error')): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars(getFlash('error')) ?></div>
      <?php endif; ?>
      <?php if (hasFlash('info')): ?>
        <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> <?= htmlspecialchars(getFlash('info')) ?></div>
      <?php endif; ?>

      <div class="form-section">
        <form method="POST" action="<?= url('register') ?>">
          <?= csrfField() ?>
          <?php if (!empty($_GET['ref'])): ?>
            <input type="hidden" name="ref" value="<?= htmlspecialchars($_GET['ref']) ?>">
          <?php endif; ?>
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
              <i class="fa-solid fa-envelope input-icon"></i>
              <input type="email" id="email" name="email"
                value="<?= htmlspecialchars(old('email') ?? '') ?>"
                class="form-input with-icon" placeholder="<?= $t['email_placeholder'] ?>" required>
            </div>
          </div>

          <!-- Phone -->
          <div class="form-group">
            <label for="phone" class="form-label"><?= $t['phone'] ?></label>
            <div class="input-wrapper">
              <i class="fa-solid fa-mobile-screen input-icon"></i>
              <input type="tel" id="phone" name="phone"
                value="<?= htmlspecialchars(old('phone') ?? '') ?>"
                class="form-input with-icon" placeholder="<?= $t['phone_placeholder'] ?>">
            </div>
          </div>

          <!-- Role -->
          <div class="form-group">
            <label for="role" class="form-label"><?= $t['register_as'] ?> <span class="required">*</span></label>
            <select id="role" name="role" class="form-select" required>
              <option value="buyer"    <?= old('role') === 'buyer'    ? 'selected' : '' ?>><?= $t['buyer'] ?></option>
              <option value="seller"   <?= old('role') === 'seller'   ? 'selected' : '' ?>><?= $t['seller'] ?></option>
              <option value="delivery" <?= old('role') === 'delivery' ? 'selected' : '' ?>><?= $t['delivery'] ?></option>
              <option value="affiliate"<?= old('role') === 'affiliate'? 'selected' : '' ?>><?= $t['affiliate'] ?></option>
            </select>
            <div class="driver-notice" id="driverNotice" style="display:none;">
              <div class="driver-notice-title">
                <i class="fa-solid fa-truck"></i>
                <?= $fr ? "Rejoindre notre équipe de livraison" : 'Join Our Delivery Team' ?>
              </div>
              <div class="driver-notice-desc">
                <?= $fr
                  ? "Les livreurs ne s'inscrivent pas ici. Vous devez d'abord soumettre une demande via notre portail Livreur Central, où notre équipe examinera votre candidature."
                  : "Delivery drivers don't sign up here. You need to submit an application through our Driver Central first, where our team will review your candidacy." ?>
              </div>
              <a href="<?= url('delivery/apply') ?>" class="btn-apply-driver">
                <i class="fa-solid fa-paper-plane"></i>
                <?= $fr ? 'Soumettre ma candidature' : 'Submit My Application' ?>
              </a>
            </div>
          </div>

          <!-- Password -->
          <div class="form-group">
            <label for="password" class="form-label"><?= $t['password'] ?> <span class="required">*</span></label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" id="password" name="password"
                class="form-input with-icon with-toggle"
                placeholder="<?= $t['password_placeholder'] ?>" minlength="10" maxlength="72" autocomplete="new-password" required>
              <button type="button" class="toggle-pass" data-target="password" aria-label="<?= $fr ? 'Afficher le mot de passe' : 'Show password' ?>">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>

          <!-- Confirm password -->
          <div class="form-group">
            <label for="password_confirmation" class="form-label"><?= $t['confirm_password'] ?> <span class="required">*</span></label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" id="password_confirmation" name="password_confirmation"
                class="form-input with-icon with-toggle"
                placeholder="<?= $t['password_placeholder'] ?>" autocomplete="new-password" required>
              <button type="button" class="toggle-pass" data-target="password_confirmation" aria-label="<?= $fr ? 'Afficher le mot de passe' : 'Show password' ?>">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
            <div class="pw-rules" id="pwRules" aria-live="polite">
              <div class="pw-rule" data-rule="length"><i class="fa-solid fa-circle-xmark"></i> <?= $fr ? '10 caractères minimum' : 'At least 10 characters' ?></div>
              <div class="pw-rule" data-rule="upper"><i class="fa-solid fa-circle-xmark"></i> <?= $fr ? 'Une lettre majuscule (A-Z)' : 'One uppercase letter (A-Z)' ?></div>
              <div class="pw-rule" data-rule="lower"><i class="fa-solid fa-circle-xmark"></i> <?= $fr ? 'Une lettre minuscule (a-z)' : 'One lowercase letter (a-z)' ?></div>
              <div class="pw-rule" data-rule="number"><i class="fa-solid fa-circle-xmark"></i> <?= $fr ? 'Un chiffre (0-9)' : 'One number (0-9)' ?></div>
              <div class="pw-rule" data-rule="special"><i class="fa-solid fa-circle-xmark"></i> <?= $fr ? 'Un caractère spécial (!@#$%^&*)' : 'One special character (!@#$%^&*)' ?></div>
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

      <div class="apply-links" id="loginLink"><a href="<?= $hero['login_url'] ?>"><?= $hero['login_lbl'] ?></a></div>
      <div class="security-note"><?= $fr
        ? 'Pour votre sécurité, OCSAPP ne vous demandera jamais votre mot de passe par courriel ou par téléphone.'
        : 'For your security, OCSAPP will never ask for your password by email or phone.' ?></div>
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
  // Show/hide password (same behaviour as the login page)
  document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', function() {
      const input = document.getElementById(this.dataset.target);
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      this.innerHTML = show ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
      this.setAttribute('aria-label', show
        ? <?= json_encode($fr ? 'Masquer le mot de passe' : 'Hide password') ?>
        : <?= json_encode($fr ? 'Afficher le mot de passe' : 'Show password') ?>);
    });
  });

  // Password rules: same checks as validatePasswordStrength() on the server
  const passwordInput = document.getElementById('password');
  const confirmInput  = document.getElementById('password_confirmation');
  const pwTests = {
    length:  pw => pw.length >= 10,
    upper:   pw => /[A-Z]/.test(pw),
    lower:   pw => /[a-z]/.test(pw),
    number:  pw => /[0-9]/.test(pw),
    special: pw => /[^A-Za-z0-9]/.test(pw),
  };
  passwordInput.addEventListener('input', function() {
    let allOk = true;
    document.querySelectorAll('#pwRules .pw-rule').forEach(el => {
      const ok = pwTests[el.dataset.rule](this.value);
      allOk = allOk && ok;
      el.classList.toggle('pw-rule-ok', ok);
      el.querySelector('i').className = ok ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-xmark';
    });
    this.setCustomValidity(allOk ? '' : <?= json_encode($fr ? 'Le mot de passe ne respecte pas toutes les règles ci-dessous.' : 'Password does not meet all the rules below.') ?>);
  });

  // Password match validation
  confirmInput.addEventListener('input', function() {
    this.setCustomValidity(
      (passwordInput.value !== this.value && this.value.length > 0)
        ? <?= json_encode($fr ? 'Les mots de passe ne correspondent pas' : 'Passwords do not match') ?>
        : ''
    );
  });

  // Role change handler
  const roleSelect              = document.getElementById('role');
  const sellerAgreementSection  = document.getElementById('sellerAgreementSection');
  const sellerAgreementCheckbox = document.getElementById('seller_agreement');
  const driverNotice            = document.getElementById('driverNotice');
  const submitBtn               = document.querySelector('.btn-submit');

  // Brand-panel + login-link content per role, so the page reflects the
  // selected role even without a page reload (mirrors the server-rendered state).
  const heroByRole = <?= json_encode([
    'buyer'  => $heroConfig['default'],
    'seller' => $heroConfig['seller'],
  ], JSON_UNESCAPED_UNICODE) ?>;

  function applyHero(role) {
    const hero = heroByRole[role];
    if (!hero) return;
    document.title = hero.title_tag;
    document.getElementById('heroEyebrow').textContent = hero.eyebrow;
    document.getElementById('heroIcon').src = <?= json_encode(asset('')) ?> + hero.icon;
    document.getElementById('heroIcon').alt = hero.h1;
    document.getElementById('heroTitle').textContent = hero.h1;
    document.getElementById('heroSubtitle').textContent = hero.p;
    document.getElementById('cardKicker').textContent = hero.card_kicker;
    document.getElementById('cardH2').textContent = hero.card_h2;
    document.getElementById('cardP').textContent = hero.card_p;
    document.getElementById('centralLink').href = hero.central_url;
    document.getElementById('centralLink').textContent = hero.central_lbl;
    const loginA = document.querySelector('#loginLink a');
    loginA.href = hero.login_url;
    loginA.textContent = hero.login_lbl;
  }

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
        ? <?= json_encode($fr ? 'Veuillez soumettre une candidature via Livreur Central' : 'Please apply via Driver Central instead') ?>
        : '';
    }

    if (val === 'buyer' || val === 'seller') applyHero(val);
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
