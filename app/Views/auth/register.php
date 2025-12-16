<?php
/**
 * OCSAPP Register Page - WITH CART PRESERVATION
 * File: app/Views/auth/register.php
 */

// Get current language from session or default to English
$currentLang = $_SESSION['language'] ?? 'fr';

// ADDED: Get redirect parameter if present
$redirect = isset($_GET['redirect']) ? sanitize($_GET['redirect']) : '';

// Language translations
$translations = [
    'en' => [
        'create_account' => 'Create Account',
        'join_ocs' => 'Join OCSAPP and start ordering',
        'sign_up' => 'Sign Up',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'register_as' => 'I want to register as',
        'buyer' => 'Buyer (Shop & Order)',
        'seller' => 'Seller (Sell Products)',
        'delivery' => 'Delivery Staff',
        'affiliate' => 'Affiliate Marketer',
        'agree_to' => 'I agree to the',
        'terms' => 'Terms of Service',
        'and' => 'and',
        'privacy' => 'Privacy Policy',
        'create_btn' => 'Create Account',
        'have_account' => 'Already have an account?',
        'sign_in' => 'Sign in',
        'all_rights' => 'All Rights Reserved',
        'tagline' => 'Zero-Emission Grocery Delivery',
        'required' => 'Required',
        'optional' => 'Optional',
        'min_8_chars' => 'Minimum 8 characters',
        'email_placeholder' => 'you@example.com',
        'phone_placeholder' => '+1-809-555-0000',
        'password_placeholder' => '••••••••',
        'first_name_placeholder' => 'Juan',
        'last_name_placeholder' => 'Perez',
        // ADDED: Cart checkout messages
        'checkout_notice_title' => 'Complete Your Order',
        'checkout_notice_desc' => 'Create an account to proceed with your',
        'items_order' => 'item(s) order',
        'create_and_checkout' => 'Create Account & Checkout',
    ],
    'fr' => [
        'create_account' => 'Créer un compte',
        'join_ocs' => 'Rejoignez OCSAPP et commencez à commander',
        'sign_up' => 'S\'inscrire',
        'first_name' => 'Prénom',
        'last_name' => 'Nom',
        'email' => 'Adresse e-mail',
        'phone' => 'Téléphone',
        'password' => 'Mot de passe',
        'confirm_password' => 'Confirmer le mot de passe',
        'register_as' => 'Je veux m\'inscrire comme',
        'buyer' => 'Acheteur (Magasiner)',
        'seller' => 'Vendeur (Vendre)',
        'delivery' => 'Livreur',
        'affiliate' => 'Affilié',
        'agree_to' => 'J\'accepte les',
        'terms' => 'Conditions d\'utilisation',
        'and' => 'et',
        'privacy' => 'Politique de confidentialité',
        'create_btn' => 'Créer un compte',
        'have_account' => 'Vous avez déjà un compte?',
        'sign_in' => 'Se connecter',
        'all_rights' => 'Tous droits réservés',
        'tagline' => 'Livraison d\'épicerie zéro émission',
        'required' => 'Requis',
        'optional' => 'Optionnel',
        'min_8_chars' => 'Minimum 8 caractères',
        'email_placeholder' => 'vous@exemple.com',
        'phone_placeholder' => '+1-809-555-0000',
        'password_placeholder' => '••••••••',
        'first_name_placeholder' => 'Juan',
        'last_name_placeholder' => 'Perez',
        // ADDED: Cart checkout messages
        'checkout_notice_title' => 'Complétez votre commande',
        'checkout_notice_desc' => 'Créez un compte pour procéder avec votre commande de',
        'items_order' => 'article(s)',
        'create_and_checkout' => 'Créer un compte et commander',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $t['create_account'] ?> – OCSAPP</title>
  <?= csrfMeta() ?>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #00b207;
      --primary-600: #009206;
      --primary-700: #007a05;
      --dark: #1a1a1a;
      --gray-50: #fafafa;
      --gray-100: #f5f5f5;
      --gray-200: #e5e5e5;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --border: #e5e5e5;
      --radius-sm: 8px;
      --radius-md: 10px;
      --radius-lg: 12px;
      --radius-xl: 16px;
      --radius-full: 999px;
      --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 28px rgba(0, 0, 0, 0.12);
      --transition-base: 200ms ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      color: var(--dark);
    }

    .register-container {
      width: 100%;
      max-width: 700px;
    }

    .brand-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .logo-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 12px;
    }

    .logo-icon {
      width: 60px;
      height: 60px;
      background: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: var(--shadow-md);
      padding: 8px;
      border: 3px solid var(--primary);
    }

    .logo-icon img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .brand-name {
      font-size: 42px;
      font-weight: 800;
      color: var(--primary);
      letter-spacing: -0.5px;
    }

    .brand-tagline {
      font-size: 15px;
      color: var(--gray-600);
      font-weight: 500;
    }

    .register-card {
      background: #fff;
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-lg);
      padding: 40px;
    }

    .card-header {
      text-align: center;
      margin-bottom: 35px;
    }

    .card-title {
      font-size: 28px;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 8px;
    }

    .card-subtitle {
      font-size: 14px;
      color: var(--gray-500);
    }

    /* ADDED: Checkout Notice */
    .checkout-notice {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      border-left: 4px solid var(--primary);
      padding: 16px 20px;
      border-radius: var(--radius-md);
      margin-bottom: 25px;
      box-shadow: var(--shadow-sm);
    }

    .checkout-notice-title {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 16px;
      font-weight: 700;
      color: var(--primary-700);
      margin-bottom: 6px;
    }

    .checkout-notice-desc {
      font-size: 14px;
      color: var(--gray-600);
      line-height: 1.5;
    }

    /* Flash Messages */
    .alert {
      padding: 16px 20px;
      border-radius: var(--radius-md);
      margin-bottom: 25px;
      font-size: 14px;
      font-weight: 500;
    }

    .alert-error {
      background: #fef2f2;
      border-left: 4px solid #ef4444;
      color: #991b1b;
    }

    .alert-info {
      background: #eff6ff;
      border-left: 4px solid #3b82f6;
      color: #1e40af;
    }

    /* Form Styles */
    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 8px;
    }

    .required {
      color: #ef4444;
    }

    .input-wrapper {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray-500);
      font-size: 18px;
      pointer-events: none;
    }

    .form-input,
    .form-select {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid var(--border);
      border-radius: var(--radius-md);
      font-size: 15px;
      font-family: inherit;
      transition: all var(--transition-base);
    }

    .form-input.with-icon {
      padding-left: 48px;
    }

    .form-input:focus,
    .form-select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
    }

    .form-input::placeholder {
      color: var(--gray-500);
    }

    .form-select {
      cursor: pointer;
      background: #fff;
    }

    .form-hint {
      margin-top: 6px;
      font-size: 12px;
      color: var(--gray-500);
    }

    /* Checkbox */
    .checkbox-wrapper {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin-bottom: 25px;
    }

    .form-checkbox {
      width: 18px;
      height: 18px;
      margin-top: 2px;
      border-radius: 4px;
      cursor: pointer;
      accent-color: var(--primary);
      flex-shrink: 0;
    }

    .checkbox-label {
      font-size: 14px;
      color: var(--dark);
      line-height: 1.5;
    }

    .checkbox-label a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
      transition: color var(--transition-base);
    }

    .checkbox-label a:hover {
      color: var(--primary-600);
      text-decoration: underline;
    }

    /* Submit Button */
    .btn-submit {
      width: 100%;
      padding: 16px;
      background: var(--primary);
      color: #fff;
      border: none;
      border-radius: var(--radius-md);
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all var(--transition-base);
      font-family: inherit;
    }

    .btn-submit:hover {
      background: var(--primary-600);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    /* Login Link */
    .login-section {
      margin-top: 25px;
      text-align: center;
      font-size: 14px;
      color: var(--gray-600);
    }

    .login-link {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
      transition: color var(--transition-base);
    }

    .login-link:hover {
      color: var(--primary-600);
      text-decoration: underline;
    }

    /* Footer */
    .register-footer {
      text-align: center;
      margin-top: 30px;
      font-size: 13px;
      color: var(--gray-600);
    }

    /* Language Selector */
    .language-selector-register {
      position: absolute;
      top: 20px;
      right: 20px;
      z-index: 100;
    }

    .language-btn-register {
      background: #fff;
      border: 2px solid var(--border);
      border-radius: var(--radius-full);
      padding: 8px 16px;
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 600;
      transition: all var(--transition-base);
      box-shadow: var(--shadow-sm);
      font-family: inherit;
    }

    .language-btn-register:hover {
      border-color: var(--primary);
      box-shadow: var(--shadow-md);
    }

    .language-dropdown-register {
      display: none;
      position: absolute;
      top: 100%;
      right: 0;
      margin-top: 8px;
      background: #fff;
      border: 2px solid var(--border);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-lg);
      min-width: 160px;
      overflow: hidden;
      z-index: 1000;
    }

    .language-dropdown-register.show {
      display: block;
    }

    .language-option-register {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      cursor: pointer;
      transition: background var(--transition-base);
      font-size: 14px;
      font-weight: 500;
    }

    .language-option-register:hover {
      background: var(--gray-100);
    }

    .language-option-register.selected {
      background: var(--gray-100);
      font-weight: 600;
      color: var(--primary);
    }

    /* Responsive */
    @media (max-width: 768px) {
      body {
        padding: 20px 15px;
      }

      .register-card {
        padding: 30px 25px;
      }

      .brand-name {
        font-size: 36px;
      }

      .card-title {
        font-size: 24px;
      }

      .form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }

      .language-selector-register {
        top: 10px;
        right: 10px;
      }

      .language-btn-register {
        padding: 6px 12px;
        font-size: 12px;
      }
    }

    @media (max-width: 480px) {
      .register-card {
        padding: 25px 20px;
      }
    }
  </style>
</head>
<body>
  <!-- Language Selector Dropdown -->
  <div class="language-selector-register">
    <button class="language-btn-register" id="languageBtnRegister" type="button" aria-label="Select Language">
      <span><?= strtoupper($currentLang) ?></span>
      <span>▼</span>
    </button>
    <div class="language-dropdown-register" id="languageDropdownRegister" role="menu">
      <div class="language-option-register <?= $currentLang === 'en' ? 'selected' : '' ?>" data-lang="en" role="menuitem">
        <span>🇺🇸</span>
        <span>English</span>
      </div>
      <div class="language-option-register <?= $currentLang === 'fr' ? 'selected' : '' ?>" data-lang="fr" role="menuitem">
        <span>🇫🇷</span>
        <span>Français</span>
      </div>
    </div>
  </div>

  <div class="register-container">
    <!-- Brand Header -->
    <div class="brand-header">
      <div class="logo-container">
        <div class="logo-icon">
          <img src="<?= asset('images/logo.png') ?>" alt="OCSAPP Logo" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <h1 class="brand-name">OCSAPP</h1>
      </div>
      <p class="brand-tagline"><?= $t['tagline'] ?></p>
    </div>

    <!-- Register Card -->
    <div class="register-card">
      <div class="card-header">
        <h2 class="card-title"><?= $t['create_account'] ?></h2>
        <p class="card-subtitle"><?= $t['join_ocs'] ?></p>
      </div>

      <!-- ADDED: Checkout Notice (only when coming from checkout) -->
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

      <!-- Flash Messages -->
      <?php if (hasFlash('error')): ?>
        <div class="alert alert-error" role="alert">
          <?= htmlspecialchars(getFlash('error')) ?>
        </div>
      <?php endif; ?>
      
      <?php if (hasFlash('info')): ?>
        <div class="alert alert-info" role="alert">
          <?= htmlspecialchars(getFlash('info')) ?>
        </div>
      <?php endif; ?>

      <!-- Register Form -->
      <form method="POST" action="<?= url('register') ?>">
        <?= csrfField() ?>

        <!-- ADDED: Hidden redirect field -->
        <?php if ($redirect): ?>
          <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
        <?php endif; ?>

        <div class="form-grid">
          <!-- First Name -->
          <div class="form-group">
            <label for="first_name" class="form-label">
              <?= $t['first_name'] ?> <span class="required">*</span>
            </label>
            <input 
              type="text" 
              id="first_name" 
              name="first_name" 
              value="<?= htmlspecialchars(old('first_name') ?? '') ?>"
              class="form-input"
              placeholder="<?= $t['first_name_placeholder'] ?>"
              required
            >
          </div>

          <!-- Last Name -->
          <div class="form-group">
            <label for="last_name" class="form-label">
              <?= $t['last_name'] ?> <span class="required">*</span>
            </label>
            <input 
              type="text" 
              id="last_name" 
              name="last_name" 
              value="<?= htmlspecialchars(old('last_name') ?? '') ?>"
              class="form-input"
              placeholder="<?= $t['last_name_placeholder'] ?>"
              required
            >
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email" class="form-label">
            <?= $t['email'] ?> <span class="required">*</span>
          </label>
          <div class="input-wrapper">
            <span class="input-icon">📧</span>
            <input 
              type="email" 
              id="email" 
              name="email" 
              value="<?= htmlspecialchars(old('email') ?? '') ?>"
              class="form-input with-icon"
              placeholder="<?= $t['email_placeholder'] ?>"
              required
            >
          </div>
        </div>

        <!-- Phone -->
        <div class="form-group">
          <label for="phone" class="form-label">
            <?= $t['phone'] ?>
          </label>
          <div class="input-wrapper">
            <span class="input-icon">📱</span>
            <input 
              type="tel" 
              id="phone" 
              name="phone" 
              value="<?= htmlspecialchars(old('phone') ?? '') ?>"
              class="form-input with-icon"
              placeholder="<?= $t['phone_placeholder'] ?>"
            >
          </div>
        </div>

        <div class="form-grid">
          <!-- Role Selection -->
          <div class="form-group">
            <label for="role" class="form-label">
              <?= $t['register_as'] ?> <span class="required">*</span>
            </label>
            <select 
              id="role" 
              name="role" 
              class="form-select"
              required
            >
              <option value="buyer" <?= old('role') === 'buyer' ? 'selected' : '' ?>><?= $t['buyer'] ?></option>
              <option value="seller" <?= old('role') === 'seller' ? 'selected' : '' ?>><?= $t['seller'] ?></option>
              <option value="delivery" <?= old('role') === 'delivery' ? 'selected' : '' ?>><?= $t['delivery'] ?></option>
              <option value="affiliate" <?= old('role') === 'affiliate' ? 'selected' : '' ?>><?= $t['affiliate'] ?></option>
            </select>
          </div>

          <!-- Password -->
          <div class="form-group">
            <label for="password" class="form-label">
              <?= $t['password'] ?> <span class="required">*</span>
            </label>
            <div class="input-wrapper">
              <span class="input-icon">🔒</span>
              <input 
                type="password" 
                id="password" 
                name="password" 
                class="form-input with-icon"
                placeholder="<?= $t['password_placeholder'] ?>"
                minlength="8"
                required
              >
            </div>
            <p class="form-hint"><?= $t['min_8_chars'] ?></p>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <label for="password_confirmation" class="form-label">
            <?= $t['confirm_password'] ?> <span class="required">*</span>
          </label>
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input 
              type="password" 
              id="password_confirmation" 
              name="password_confirmation" 
              class="form-input with-icon"
              placeholder="<?= $t['password_placeholder'] ?>"
              required
            >
          </div>
        </div>

        <!-- Terms -->
        <div class="checkbox-wrapper">
          <input 
            type="checkbox" 
            id="terms" 
            name="terms" 
            class="form-checkbox"
            required
          >
          <label for="terms" class="checkbox-label">
            <?= $t['agree_to'] ?> <a href="<?= url('terms') ?>"><?= $t['terms'] ?></a> <?= $t['and'] ?> <a href="<?= url('privacy') ?>"><?= $t['privacy'] ?></a>
          </label>
        </div>

        <!-- Submit Button - UPDATED: Dynamic text based on checkout flow -->
        <button type="submit" class="btn-submit">
          <?= $redirect === '/checkout' ? '🛒 ' . $t['create_and_checkout'] : $t['create_btn'] ?>
        </button>
      </form>

      <!-- Login Link - ADDED: Preserve redirect parameter -->
      <div class="login-section">
        <?= $t['have_account'] ?> 
        <a href="<?= url('login' . ($redirect ? '?redirect=' . urlencode($redirect) : '')) ?>" class="login-link"><?= $t['sign_in'] ?></a>
      </div>
    </div>

    <!-- Footer -->
    <div class="register-footer">
      OCSAPP © <?= date('Y') ?>. <?= $t['all_rights'] ?>
    </div>
  </div>

  <script>
    // Password match validation
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');

    confirmInput.addEventListener('input', function() {
      if (passwordInput.value !== this.value && this.value.length > 0) {
        this.setCustomValidity('<?= $currentLang === "fr" ? "Les mots de passe ne correspondent pas" : "Passwords do not match" ?>');
      } else {
        this.setCustomValidity('');
      }
    });

    // Language dropdown
    const languageBtnRegister = document.getElementById('languageBtnRegister');
    const languageDropdownRegister = document.getElementById('languageDropdownRegister');
    
    if (languageBtnRegister && languageDropdownRegister) {
      // Toggle dropdown
      languageBtnRegister.addEventListener('click', (e) => {
        e.stopPropagation();
        languageDropdownRegister.classList.toggle('show');
      });

      // Handle language selection
      document.querySelectorAll('.language-option-register').forEach(option => {
        option.addEventListener('click', async function() {
          const lang = this.dataset.lang;
          
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
          const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';
          
          try {
            const formData = new URLSearchParams();
            formData.append(csrfName, csrfToken);
            formData.append('language', lang);
            
            const response = await fetch('<?= url('set-language') ?>', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: formData.toString()
            });
            
            const data = await response.json();
            
            if (data.success) {
              window.location.reload();
            }
          } catch (error) {
            console.error('Language change error:', error);
            window.location.reload();
          }
        });
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', (e) => {
        if (!languageBtnRegister.contains(e.target) && !languageDropdownRegister.contains(e.target)) {
          languageDropdownRegister.classList.remove('show');
        }
      });
    }
  </script>
</body>
</html>
<?php clearOldInput(); ?>