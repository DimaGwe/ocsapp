<?php
/**
 * OCSAPP Login Page - WITH CART PRESERVATION
 * File: app/Views/auth/login.php
 */

// Get current language from session or default to French
$currentLang = $_SESSION['language'] ?? 'fr';

// ADDED: Get redirect parameter if present
$redirect = isset($_GET['redirect']) ? sanitize($_GET['redirect']) : '';

// Language translations
$translations = [
    'en' => [
        'welcome_back' => 'Welcome Back',
        'sign_in_to' => 'Sign in to your account',
        'email' => 'Email Address',
        'password' => 'Password',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot password?',
        'sign_in' => 'Sign In',
        'no_account' => 'Don\'t have an account?',
        'sign_up' => 'Sign up',
        'all_rights' => 'All Rights Reserved',
        'tagline' => 'Zero-Emission Grocery Delivery',
        'email_placeholder' => 'you@example.com',
        'password_placeholder' => '••••••••',
        // ADDED: Cart checkout messages
        'checkout_notice_title' => 'Complete Your Order',
        'checkout_notice_desc' => 'Sign in to proceed with your',
        'items_order' => 'item(s) order',
        'sign_in_and_checkout' => 'Sign In & Checkout',
    ],
    'fr' => [
        'welcome_back' => 'Bienvenue',
        'sign_in_to' => 'Connectez-vous à votre compte',
        'email' => 'Adresse e-mail',
        'password' => 'Mot de passe',
        'remember_me' => 'Se souvenir',
        'forgot_password' => 'Mot de passe oublié?',
        'sign_in' => 'Se connecter',
        'no_account' => 'Pas de compte?',
        'sign_up' => 'S\'inscrire',
        'all_rights' => 'Tous droits réservés',
        'tagline' => 'Livraison d\'épicerie zéro émission',
        'email_placeholder' => 'vous@exemple.com',
        'password_placeholder' => '••••••••',
        // ADDED: Cart checkout messages
        'checkout_notice_title' => 'Complétez votre commande',
        'checkout_notice_desc' => 'Connectez-vous pour procéder avec votre commande de',
        'items_order' => 'article(s)',
        'sign_in_and_checkout' => 'Se connecter et commander',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $t['sign_in'] ?> – OCSAPP</title>
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
      padding: 20px;
      color: var(--dark);
    }

    .login-container {
      width: 100%;
      max-width: 480px;
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

    .login-card {
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

    .alert-success {
      background: #f0fdf4;
      border-left: 4px solid #22c55e;
      color: #166534;
    }

    .alert-info {
      background: #eff6ff;
      border-left: 4px solid #3b82f6;
      color: #1e40af;
    }

    /* Form Styles */
    .form-group {
      margin-bottom: 24px;
    }

    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 8px;
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

    .form-input {
      width: 100%;
      padding: 14px 16px 14px 48px;
      border: 2px solid var(--border);
      border-radius: var(--radius-md);
      font-size: 15px;
      font-family: inherit;
      transition: all var(--transition-base);
    }

    .form-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
    }

    .form-input::placeholder {
      color: var(--gray-500);
    }

    /* Remember & Forgot */
    .form-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 25px;
    }

    .checkbox-wrapper {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .form-checkbox {
      width: 18px;
      height: 18px;
      border-radius: 4px;
      cursor: pointer;
      accent-color: var(--primary);
    }

    .checkbox-label {
      font-size: 14px;
      color: var(--dark);
      cursor: pointer;
      user-select: none;
    }

    .forgot-link {
      font-size: 14px;
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
      transition: color var(--transition-base);
    }

    .forgot-link:hover {
      color: var(--primary-600);
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

    /* Register Link */
    .register-section {
      margin-top: 25px;
      text-align: center;
      font-size: 14px;
      color: var(--gray-600);
    }

    .register-link {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
      transition: color var(--transition-base);
    }

    .register-link:hover {
      color: var(--primary-600);
      text-decoration: underline;
    }

    /* Footer */
    .login-footer {
      text-align: center;
      margin-top: 30px;
      font-size: 13px;
      color: var(--gray-600);
    }

    /* Language Selector Dropdown */
    .language-selector-login {
      position: absolute;
      top: 20px;
      right: 20px;
      z-index: 100;
    }

    .language-btn-login {
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

    .language-btn-login:hover {
      border-color: var(--primary);
      box-shadow: var(--shadow-md);
    }

    .language-dropdown-login {
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

    .language-dropdown-login.show {
      display: block;
    }

    .language-option-login {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      cursor: pointer;
      transition: background var(--transition-base);
      font-size: 14px;
      font-weight: 500;
    }

    .language-option-login:hover {
      background: var(--gray-100);
    }

    .language-option-login.selected {
      background: var(--gray-50);
      color: var(--primary);
      font-weight: 600;
    }

    .language-option-login.selected::after {
      content: '✓';
      margin-left: auto;
      color: var(--primary);
      font-weight: 700;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body {
        padding: 15px;
      }

      .login-card {
        padding: 30px 25px;
      }

      .brand-name {
        font-size: 36px;
      }

      .card-title {
        font-size: 24px;
      }

      .language-selector-login {
        top: 10px;
        right: 10px;
      }

      .language-btn-login {
        padding: 6px 12px;
        font-size: 12px;
      }

      .language-dropdown-login {
        min-width: 140px;
      }

      .language-option-login {
        padding: 10px 14px;
        font-size: 13px;
      }
    }

    @media (max-width: 480px) {
      .login-card {
        padding: 25px 20px;
      }
    }
  </style>
</head>
<body>
  <!-- Language Selector Dropdown -->
  <div class="language-selector-login">
    <button class="language-btn-login" id="languageBtnLogin" type="button" aria-label="Select Language">
      <span><?= strtoupper($currentLang) ?></span>
      <span>▼</span>
    </button>
    <div class="language-dropdown-login" id="languageDropdownLogin" role="menu">
      <div class="language-option-login <?= $currentLang === 'en' ? 'selected' : '' ?>" data-lang="en" role="menuitem">
        <span>🇺🇸</span>
        <span>English</span>
      </div>
      <div class="language-option-login <?= $currentLang === 'fr' ? 'selected' : '' ?>" data-lang="fr" role="menuitem">
        <span>🇫🇷</span>
        <span>Français</span>
      </div>
    </div>
  </div>

  <div class="login-container">
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

    <!-- Login Card -->
    <div class="login-card">
      <div class="card-header">
        <h2 class="card-title"><?= $t['welcome_back'] ?></h2>
        <p class="card-subtitle"><?= $t['sign_in_to'] ?></p>
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

      <?php if (hasFlash('success')): ?>
        <div class="alert alert-success" role="alert">
          <?= htmlspecialchars(getFlash('success')) ?>
        </div>
      <?php endif; ?>
      
      <?php if (hasFlash('info')): ?>
        <div class="alert alert-info" role="alert">
          <?= htmlspecialchars(getFlash('info')) ?>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form method="POST" action="<?= url('login') ?>">
        <?= csrfField() ?>

        <!-- ADDED: Hidden redirect field -->
        <?php if ($redirect): ?>
          <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
        <?php endif; ?>

        <!-- Email -->
        <div class="form-group">
          <label for="email" class="form-label"><?= $t['email'] ?></label>
          <div class="input-wrapper">
            <span class="input-icon">📧</span>
            <input 
              type="email" 
              id="email" 
              name="email" 
              value="<?= htmlspecialchars(old('email') ?? '') ?>"
              class="form-input"
              placeholder="<?= $t['email_placeholder'] ?>"
              required
              autofocus
            >
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password" class="form-label"><?= $t['password'] ?></label>
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-input"
              placeholder="<?= $t['password_placeholder'] ?>"
              required
            >
          </div>
        </div>

        <!-- Remember & Forgot -->
        <div class="form-options">
          <div class="checkbox-wrapper">
            <input 
              type="checkbox" 
              id="remember" 
              name="remember" 
              class="form-checkbox"
            >
            <label for="remember" class="checkbox-label"><?= $t['remember_me'] ?></label>
          </div>
          <a href="<?= url('forgot-password') ?>" class="forgot-link">
            <?= $t['forgot_password'] ?>
          </a>
        </div>

        <!-- Submit Button - UPDATED: Dynamic text based on checkout flow -->
        <button type="submit" class="btn-submit">
          <?= $redirect === '/checkout' ? '🛒 ' . $t['sign_in_and_checkout'] : $t['sign_in'] ?>
        </button>
      </form>

      <!-- Register Link - ADDED: Preserve redirect parameter -->
      <div class="register-section">
        <?= $t['no_account'] ?> 
        <a href="<?= url('register' . ($redirect ? '?redirect=' . urlencode($redirect) : '')) ?>" class="register-link"><?= $t['sign_up'] ?></a>
      </div>
    </div>

    <!-- Footer -->
    <div class="login-footer">
      OCSAPP © <?= date('Y') ?>. <?= $t['all_rights'] ?>
    </div>
  </div>

  <script>
    // Language dropdown toggle
    const languageBtnLogin = document.getElementById('languageBtnLogin');
    const languageDropdownLogin = document.getElementById('languageDropdownLogin');
    
    if (languageBtnLogin && languageDropdownLogin) {
      // Toggle dropdown
      languageBtnLogin.addEventListener('click', (e) => {
        e.stopPropagation();
        languageDropdownLogin.classList.toggle('show');
      });

      // Handle language selection
      document.querySelectorAll('.language-option-login').forEach(option => {
        option.addEventListener('click', async function() {
          const lang = this.dataset.lang;
          
          // Get CSRF token
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
          const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';
          
          try {
            // Create form data
            const formData = new URLSearchParams();
            formData.append(csrfName, csrfToken);
            formData.append('language', lang);
            
            // Send AJAX request
            const response = await fetch('<?= url('set-language') ?>', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: formData.toString()
            });
            
            const data = await response.json();
            
            if (data.success) {
              // Reload page to apply new language
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
        if (!languageBtnLogin.contains(e.target) && !languageDropdownLogin.contains(e.target)) {
          languageDropdownLogin.classList.remove('show');
        }
      });
    }
  </script>
</body>
</html>
<?php clearOldInput(); ?>