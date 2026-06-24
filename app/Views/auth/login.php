<?php
/**
 * OCS Login Page v2
 * File: app/Views/auth/login.php
 * Split-panel layout with real site header/footer.
 */

$currentLang = $_SESSION['language'] ?? 'fr';
$redirect    = isset($_GET['redirect']) ? sanitize($_GET['redirect']) : '';

$lx_strings = [
    'en' => [
        'page_title'          => 'Sign In',
        'heading'             => 'Welcome back',
        'subheading'          => 'Sign in to continue to OCSAPP',
        'email'               => 'Email address',
        'password'            => 'Password',
        'remember_me'         => 'Remember me',
        'forgot_password'     => 'Forgot password?',
        'sign_in'             => 'Sign In',
        'no_account'          => "Don't have an account?",
        'sign_up'             => 'Create one free',
        'email_ph'            => 'you@example.com',
        'password_ph'         => '••••••••',
        'checkout_title'      => 'Complete Your Order',
        'checkout_desc'       => 'Sign in to proceed with your',
        'items_order'         => 'item(s)',
        'sign_in_checkout'    => 'Sign In & Checkout',
        'customer_tab'        => 'Customer / Seller',
        'customer_desc'       => 'Shop or manage your store',
        'supplier_tab'        => 'Supplier',
        'supplier_desc'       => 'Supplier portal',
        'distribution_tab'    => 'Business',
        'distribution_desc'   => 'Distribution & procurement',
        'driver_tab'          => 'Driver',
        'driver_desc'         => 'Driver portal',
        'access_supplier'     => 'Access Supplier Portal',
        'access_distribution' => 'Access Business Portal',
        'access_driver'       => 'Access Driver Portal',
        'supplier_forgot'     => 'Forgot supplier password?',
        'not_supplier_yet'    => 'Not a supplier yet?',
        'learn_more'          => 'Learn more',
        'new_partner'         => 'New business partner?',
        'register'            => 'Register',
        'drive_interest'      => 'Interested in driving?',
        'or'                  => 'or',
        'shop_now'            => 'Shop Now',
        'become_seller'       => 'Become a Seller',
        'become_driver'       => 'Become a Driver',
        'driver_verified'     => 'Email verified! Your driver application has been submitted. Log in below with the credentials sent to your email.',
        'brand_tagline'       => 'Zero-Emission Grocery Delivery',
        'trust_1'             => 'Free to join - no setup fees',
        'trust_2'             => 'Local sellers you can trust',
        'trust_3'             => 'Zero-emission deliveries',
        'trust_4'             => '100% Canadian platform',
    ],
    'fr' => [
        'page_title'          => 'Connexion',
        'heading'             => 'Bienvenue',
        'subheading'          => 'Connectez-vous pour accéder à OCSAPP',
        'email'               => 'Adresse courriel',
        'password'            => 'Mot de passe',
        'remember_me'         => 'Se souvenir',
        'forgot_password'     => 'Mot de passe oublié?',
        'sign_in'             => 'Se connecter',
        'no_account'          => 'Pas de compte?',
        'sign_up'             => 'Créer un compte gratuit',
        'email_ph'            => 'vous@exemple.com',
        'password_ph'         => '••••••••',
        'checkout_title'      => 'Complétez votre commande',
        'checkout_desc'       => 'Connectez-vous pour procéder avec votre commande de',
        'items_order'         => 'article(s)',
        'sign_in_checkout'    => 'Se connecter et commander',
        'customer_tab'        => 'Client / Vendeur',
        'customer_desc'       => 'Achetez ou gérez votre boutique',
        'supplier_tab'        => 'Fournisseur',
        'supplier_desc'       => 'Portail fournisseur',
        'distribution_tab'    => 'Entreprise',
        'distribution_desc'   => 'Distribution et approvisionnement',
        'driver_tab'          => 'Chauffeur',
        'driver_desc'         => 'Portail livreur',
        'access_supplier'     => 'Accéder au portail fournisseur',
        'access_distribution' => 'Accéder au portail entreprise',
        'access_driver'       => 'Accéder au portail chauffeur',
        'supplier_forgot'     => 'Mot de passe fournisseur oublié?',
        'not_supplier_yet'    => 'Pas encore fournisseur?',
        'learn_more'          => 'En savoir plus',
        'new_partner'         => 'Nouveau partenaire?',
        'register'            => 'Créer un compte',
        'drive_interest'      => 'Intéressé à conduire?',
        'or'                  => 'ou',
        'shop_now'            => 'Magasiner',
        'become_seller'       => 'Devenir vendeur',
        'become_driver'       => 'Devenir livreur',
        'driver_verified'     => 'Courriel vérifié ! Votre demande de livreur a été soumise. Connectez-vous ci-dessous avec les identifiants envoyés à votre courriel.',
        'brand_tagline'       => "Livraison d'épicerie zéro émission",
        'trust_1'             => 'Gratuit - aucun frais d\'inscription',
        'trust_2'             => 'Vendeurs locaux de confiance',
        'trust_3'             => 'Livraisons zéro émission',
        'trust_4'             => 'Plateforme 100% canadienne',
    ],
];

$lx = $lx_strings[$currentLang] ?? $lx_strings['en'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $lx['page_title'] ?> - OCSAPP</title>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/login.css') ?>">
</head>
<body>
<?php include __DIR__ . '/../components/header.php'; ?>

<main class="login-page">
  <div class="login-split">

    <!-- ── Left brand panel ── -->
    <div class="brand-panel">
      <div class="brand-logo">
        <img src="<?= asset('images/logo.png') ?>" alt="OCSAPP">
        <span>OCSAPP</span>
      </div>

      <div class="brand-body">
        <div class="brand-tagline">
          <?= $currentLang === 'fr'
            ? 'Le commerce local,<br><span>réinventé.</span>'
            : 'Local commerce,<br><span>reinvented.</span>' ?>
        </div>
        <p class="brand-sub"><?= $lx['brand_tagline'] ?></p>

        <ul class="trust-list">
          <li><i class="fa-solid fa-check"></i> <?= $lx['trust_1'] ?></li>
          <li><i class="fa-solid fa-check"></i> <?= $lx['trust_2'] ?></li>
          <li><i class="fa-solid fa-check"></i> <?= $lx['trust_3'] ?></li>
          <li><i class="fa-solid fa-check"></i> <?= $lx['trust_4'] ?></li>
        </ul>

        <!-- Mobile-only trust pills -->
        <div class="trust-pills">
          <span class="trust-pill"><i class="fa-solid fa-check"></i> <?= $lx['trust_3'] ?></span>
          <span class="trust-pill"><i class="fa-solid fa-check"></i> <?= $lx['trust_4'] ?></span>
          <span class="trust-pill"><i class="fa-solid fa-check"></i> <?= $lx['trust_1'] ?></span>
        </div>
      </div>

      <p class="brand-footer-text">ocsapp.ca &copy; <?= date('Y') ?></p>
    </div>

    <!-- ── Right form panel ── -->
    <div class="form-panel">

      <!-- Role tabs -->
      <div class="role-tabs" role="tablist">
        <button type="button" class="role-tab active" id="tab-btn-customer"
                onclick="switchTab('customer')" role="tab" aria-selected="true">
          <span class="tab-icon"><i class="fa-solid fa-basket-shopping"></i></span>
          <span class="tab-name"><?= $lx['customer_tab'] ?></span>
        </button>
        <button type="button" class="role-tab" id="tab-btn-supplier"
                onclick="switchTab('supplier')" role="tab" aria-selected="false">
          <span class="tab-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
          <span class="tab-name"><?= $lx['supplier_tab'] ?></span>
        </button>
        <button type="button" class="role-tab" id="tab-btn-distribution"
                onclick="switchTab('distribution')" role="tab" aria-selected="false">
          <span class="tab-icon"><i class="fa-solid fa-building"></i></span>
          <span class="tab-name"><?= $lx['distribution_tab'] ?></span>
        </button>
        <button type="button" class="role-tab" id="tab-btn-driver"
                onclick="switchTab('driver')" role="tab" aria-selected="false">
          <span class="tab-icon"><i class="fa-solid fa-truck"></i></span>
          <span class="tab-name"><?= $lx['driver_tab'] ?></span>
        </button>
      </div>

      <!-- Form heading -->
      <div class="form-heading">
        <h2 id="form-title"><?= $lx['heading'] ?></h2>
        <p id="form-subtitle"><?= $lx['subheading'] ?></p>
      </div>

      <!-- Driver verified notice -->
      <?php if (!empty($_GET['driver_verified'])): ?>
        <div class="alert alert-success">
          <i class="fa-solid fa-circle-check"></i> <?= $lx['driver_verified'] ?>
        </div>
      <?php endif; ?>

      <!-- Flash messages -->
      <?php if (hasFlash('error')): ?>
        <div class="alert alert-error"><?= getFlash('error') ?></div>
      <?php endif; ?>
      <?php if (hasFlash('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars(getFlash('success')) ?></div>
      <?php endif; ?>
      <?php if (hasFlash('info')): ?>
        <div class="alert alert-info"><?= htmlspecialchars(getFlash('info')) ?></div>
      <?php endif; ?>

      <!-- ── CUSTOMER / SELLER ── -->
      <div class="tab-panel active" id="tab-panel-customer" role="tabpanel">

        <?php if ($redirect === '/checkout' && isset($_SESSION['pending_checkout_cart'])): ?>
        <div class="checkout-notice">
          <div class="checkout-notice-title">
            <i class="fa-solid fa-cart-shopping"></i> <?= $lx['checkout_title'] ?>
          </div>
          <div class="checkout-notice-desc">
            <?= $lx['checkout_desc'] ?> <strong><?= count($_SESSION['pending_checkout_cart']) ?> <?= $lx['items_order'] ?></strong>
          </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('login') ?>">
          <?= csrfField() ?>
          <?php if ($redirect): ?>
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
          <?php endif; ?>

          <div class="form-group">
            <label for="email" class="form-label"><?= $lx['email'] ?></label>
            <div class="input-wrap">
              <i class="fa-regular fa-envelope input-icon"></i>
              <input type="email" id="email" name="email"
                value="<?= htmlspecialchars(old('email') ?? '') ?>"
                class="form-input" placeholder="<?= $lx['email_ph'] ?>"
                required autofocus>
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label"><?= $lx['password'] ?></label>
            <div class="input-wrap">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" id="password" name="password"
                class="form-input" style="padding-right:42px;"
                placeholder="<?= $lx['password_ph'] ?>" required>
              <button type="button" class="pw-toggle" data-toggle="password" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-options">
            <div class="checkbox-row">
              <input type="checkbox" id="remember" name="remember" class="form-checkbox">
              <label for="remember" class="checkbox-label"><?= $lx['remember_me'] ?></label>
            </div>
            <a href="<?= url('forgot-password') ?>" class="forgot-link"><?= $lx['forgot_password'] ?></a>
          </div>

          <button type="submit" class="btn-submit">
            <?php if ($redirect === '/checkout'): ?>
              <i class="fa-solid fa-cart-shopping"></i> <?= $lx['sign_in_checkout'] ?>
            <?php else: ?>
              <i class="fa-solid fa-arrow-right-to-bracket"></i> <?= $lx['sign_in'] ?>
            <?php endif; ?>
          </button>
        </form>

        <div class="register-row">
          <?= $lx['no_account'] ?> <a href="<?= url('register' . ($redirect ? '?redirect=' . urlencode($redirect) : '')) ?>"><?= $lx['sign_up'] ?></a>
        </div>
      </div>

      <!-- ── SUPPLIER ── -->
      <div class="tab-panel" id="tab-panel-supplier" role="tabpanel">
        <form method="POST" action="<?= url('supplier/login') ?>">
          <?= csrfField() ?>

          <div class="form-group">
            <label for="sup-email" class="form-label"><?= $lx['email'] ?></label>
            <div class="input-wrap">
              <i class="fa-regular fa-envelope input-icon"></i>
              <input type="email" id="sup-email" name="email"
                class="form-input" placeholder="<?= $lx['email_ph'] ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="sup-password" class="form-label"><?= $lx['password'] ?></label>
            <div class="input-wrap">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" id="sup-password" name="password"
                class="form-input" style="padding-right:42px;"
                placeholder="<?= $lx['password_ph'] ?>" required>
              <button type="button" class="pw-toggle" data-toggle="sup-password" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-options">
            <div></div>
            <a href="<?= url('supplier/forgot-password') ?>" class="forgot-link"><?= $lx['forgot_password'] ?></a>
          </div>

          <button type="submit" class="btn-submit">
            <i class="fa-solid fa-boxes-stacked"></i> <?= $lx['access_supplier'] ?>
          </button>
        </form>

        <div class="register-row">
          <?= $lx['not_supplier_yet'] ?> <a href="<?= url('supplier-central') ?>"><?= $lx['learn_more'] ?></a>
        </div>
      </div>

      <!-- ── DISTRIBUTION ── -->
      <div class="tab-panel" id="tab-panel-distribution" role="tabpanel">
        <form method="POST" action="<?= url('distribution/login') ?>">
          <?= csrfField() ?>

          <div class="form-group">
            <label for="dist-email" class="form-label"><?= $lx['email'] ?></label>
            <div class="input-wrap">
              <i class="fa-regular fa-envelope input-icon"></i>
              <input type="email" id="dist-email" name="email"
                class="form-input" placeholder="<?= $lx['email_ph'] ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="dist-password" class="form-label"><?= $lx['password'] ?></label>
            <div class="input-wrap">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" id="dist-password" name="password"
                class="form-input" style="padding-right:42px;"
                placeholder="<?= $lx['password_ph'] ?>" required>
              <button type="button" class="pw-toggle" data-toggle="dist-password" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-options">
            <div></div>
            <a href="<?= url('forgot-password?portal=business') ?>" class="forgot-link"><?= $lx['forgot_password'] ?></a>
          </div>

          <button type="submit" class="btn-submit">
            <i class="fa-solid fa-building"></i> <?= $lx['access_distribution'] ?>
          </button>
        </form>

        <div class="register-row">
          <?= $lx['new_partner'] ?> <a href="<?= url('distribution/register') ?>"><?= $lx['register'] ?></a>
        </div>
      </div>

      <!-- ── DRIVER ── -->
      <div class="tab-panel" id="tab-panel-driver" role="tabpanel">
        <form method="POST" action="<?= url('login') ?>">
          <?= csrfField() ?>

          <div class="form-group">
            <label for="driver-email" class="form-label"><?= $lx['email'] ?></label>
            <div class="input-wrap">
              <i class="fa-regular fa-envelope input-icon"></i>
              <input type="email" id="driver-email" name="email"
                class="form-input" placeholder="<?= $lx['email_ph'] ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="driver-password" class="form-label"><?= $lx['password'] ?></label>
            <div class="input-wrap">
              <i class="fa-solid fa-lock input-icon"></i>
              <input type="password" id="driver-password" name="password"
                class="form-input" style="padding-right:42px;"
                placeholder="<?= $lx['password_ph'] ?>" required>
              <button type="button" class="pw-toggle" data-toggle="driver-password" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-options">
            <div></div>
            <a href="<?= url('forgot-password') ?>" class="forgot-link"><?= $lx['forgot_password'] ?></a>
          </div>

          <button type="submit" class="btn-submit" style="margin-top:8px;">
            <i class="fa-solid fa-truck"></i> <?= $lx['access_driver'] ?>
          </button>
        </form>

        <div class="register-row">
          <?= $lx['drive_interest'] ?> <a href="<?= url('driver-central') ?>"><?= $lx['learn_more'] ?></a>
        </div>
      </div>

      <!-- Quick links -->
      <div class="quick-section">
        <p class="quick-label"><?= $lx['or'] ?></p>
        <div class="quick-links">
          <a href="<?= url('home') ?>" class="quick-link">
            <i class="fa-solid fa-basket-shopping"></i> <?= $lx['shop_now'] ?>
          </a>
          <a href="<?= url('seller-central') ?>" class="quick-link">
            <i class="fa-solid fa-store"></i> <?= $lx['become_seller'] ?>
          </a>
          <a href="<?= url('driver-central') ?>" class="quick-link">
            <i class="fa-solid fa-truck"></i> <?= $lx['become_driver'] ?>
          </a>
        </div>
      </div>

    </div><!-- /form-panel -->
  </div><!-- /login-split -->
</main>

<?php include __DIR__ . '/../components/footer.php'; ?>

<script>
  const TABS = ['customer', 'supplier', 'distribution', 'driver'];

  function switchTab(tab) {
    TABS.forEach(id => {
      const btn   = document.getElementById('tab-btn-' + id);
      const panel = document.getElementById('tab-panel-' + id);
      const active = id === tab;
      btn.classList.toggle('active', active);
      btn.setAttribute('aria-selected', active);
      panel.classList.toggle('active', active);
    });
    try { localStorage.setItem('ocsLoginTab', tab); } catch(e) {}
  }

  (function() {
    try {
      const saved = localStorage.getItem('ocsLoginTab');
      if (saved && TABS.includes(saved) && saved !== 'customer') switchTab(saved);
    } catch(e) {}
  })();

  document.querySelectorAll('.pw-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.toggle);
      if (!input) return;
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.querySelector('i').className = show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  });
</script>
</body>
</html>
<?php clearOldInput(); ?>
