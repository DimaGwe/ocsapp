<?php
/**
 * OCS Login Page v3 - Ecosystem role-based redesign
 * File: app/Views/auth/login.php
 * One shared form driven by a 5-role selector (Buyer/Seller/Supplier/Business/Driver).
 * Header/footer match the ecosystem landing page (public/landing.php) rather than
 * the portal header.php/footer.php components.
 */

use App\Helpers\VisitorTracker;
VisitorTracker::track();

$currentLang    = $_SESSION['language'] ?? 'fr';
$fr             = ($currentLang === 'fr');
$redirect       = isset($_GET['redirect']) ? sanitize($_GET['redirect']) : '';
$driverVerified = !empty($_GET['driver_verified']);
$isCheckout     = ($redirect === '/checkout');

$roles = [
    'buyer' => [
        'icon'         => 'buyer.png',
        'fa'           => 'fa-bag-shopping',
        'labelLine1'   => $fr ? 'Acheteur' : 'Buyer',
        'labelLine2'   => 'Central',
        'contextLabel' => $fr ? 'Acheteur Central' : 'Buyer Central',
        'action'       => url('login'),
        'forgot'       => url('forgot-password'),
        'cta'          => $fr ? 'Accéder à Acheteur Central' : 'Access Buyer Central',
        'ctaCheckout'  => $fr ? 'Se connecter et commander' : 'Sign In & Checkout',
        'learnUrl'     => url('register' . ($redirect ? '?redirect=' . urlencode($redirect) : '')),
        'learnLabel'   => $fr ? 'Créer un compte gratuit' : 'Create one free',
        'noAccount'    => $fr ? 'Pas de compte?' : "Don't have an account?",
    ],
    'seller' => [
        'icon'         => 'seller.png',
        'fa'           => 'fa-tag',
        'labelLine1'   => $fr ? 'Vendeur' : 'Seller',
        'labelLine2'   => 'Central',
        'contextLabel' => $fr ? 'Vendeur Central' : 'Seller Central',
        'action'       => url('login'),
        'forgot'       => url('forgot-password'),
        'cta'          => $fr ? 'Accéder à Vendeur Central' : 'Access Seller Central',
        'ctaCheckout'  => null,
        'learnUrl'     => url('register' . ($redirect ? '?redirect=' . urlencode($redirect) : '')),
        'learnLabel'   => $fr ? 'Créer un compte gratuit' : 'Create one free',
        'noAccount'    => $fr ? 'Pas de compte?' : "Don't have an account?",
    ],
    'supplier' => [
        'icon'         => 'supplier.png',
        'fa'           => 'fa-boxes-stacked',
        'labelLine1'   => $fr ? 'Fournisseur' : 'Supplier',
        'labelLine2'   => 'Central',
        'contextLabel' => $fr ? 'Fournisseur Central' : 'Supplier Central',
        'action'       => url('supplier/login'),
        'forgot'       => url('supplier/forgot-password'),
        'cta'          => $fr ? 'Accéder à Fournisseur Central' : 'Access Supplier Central',
        'ctaCheckout'  => null,
        'learnUrl'     => url('supplier-central'),
        'learnLabel'   => $fr ? 'En savoir plus' : 'Learn more',
        'noAccount'    => $fr ? 'Pas encore fournisseur?' : 'Not a supplier yet?',
    ],
    'business' => [
        'icon'         => 'business.png',
        'fa'           => 'fa-briefcase',
        'labelLine1'   => $fr ? 'Entreprise' : 'Business',
        'labelLine2'   => 'Central',
        'contextLabel' => $fr ? 'Entreprise Centrale' : 'Business Central',
        'action'       => url('distribution/login'),
        'forgot'       => url('forgot-password?portal=business'),
        'cta'          => $fr ? 'Accéder à Entreprise Centrale' : 'Access Business Central',
        'ctaCheckout'  => null,
        'learnUrl'     => url('distribution/register'),
        'learnLabel'   => $fr ? 'Créer un compte' : 'Register',
        'noAccount'    => $fr ? 'Nouveau partenaire?' : 'New business partner?',
    ],
    'driver' => [
        'icon'         => 'driver.png',
        'fa'           => 'fa-car-side',
        'labelLine1'   => $fr ? 'Livreur' : 'Driver',
        'labelLine2'   => 'Central · ODA',
        'contextLabel' => $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA',
        'action'       => url('login'),
        'forgot'       => url('forgot-password'),
        'cta'          => $fr ? 'Accéder à Livreur Central' : 'Access Driver Central',
        'ctaCheckout'  => null,
        'learnUrl'     => url('driver-central'),
        'learnLabel'   => $fr ? 'En savoir plus' : 'Learn more',
        'noAccount'    => $fr ? 'Intéressé à conduire?' : 'Interested in driving?',
    ],
];

// Checkout only ever applies to the buyer flow, so it always wins as the default.
// Otherwise a verified driver application lands straight on the driver role.
$defaultRole = $isCheckout ? 'buyer' : ($driverVerified ? 'driver' : 'buyer');
$dr = $roles[$defaultRole];

$driverVerifiedMsg = $fr
    ? 'Courriel vérifié ! Votre demande de livreur a été soumise. Connectez-vous ci-dessous avec les identifiants envoyés à votre courriel.'
    : 'Email verified! Your driver application has been submitted. Log in below with the credentials sent to your email.';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? 'Connexion' : 'Sign In' ?> - OCSAPP</title>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/eco-header.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/pages/login.css') ?>">
</head>
<body>

<div class="eco-beta">
  <span class="eco-beta-badge"><?= $fr ? 'Bêta' : 'Beta' ?></span>
  <span class="eco-beta-full"><?= $fr
      ? 'Plateforme en cours de développement. Certaines fonctionnalités ne sont pas encore disponibles.'
      : 'Platform under development. Some features are not yet available.'
  ?></span>
  <a href="<?= url('waitlist') ?>"><?= $fr ? "Rejoindre la liste d'attente" : 'Join the waitlist' ?></a>
</div>

<header class="eco-header">
  <div class="eco-wrap eco-header-inner">
    <a class="eco-brand" href="<?= url('') ?>" aria-label="<?= $fr ? 'OCSAPP - Accueil' : 'OCSAPP - Home' ?>">
      <img src="<?= asset('images/logo.png') ?>" alt="Logo OCSAPP">
      <span class="eco-brand-text">OCSAPP</span>
    </a>

    <nav aria-label="<?= $fr ? 'Navigation principale' : 'Main navigation' ?>">
      <a class="eco-nav-link" href="<?= url('') ?>#ecosysteme"><?= $fr ? 'Écosystème' : 'Ecosystem' ?></a>
      <a class="eco-nav-link" href="<?= url('') ?>#centrales"><?= $fr ? 'Nos Centrales' : 'Our Centrals' ?></a>
      <a class="eco-nav-link" href="<?= url('') ?>#fonctionnement"><?= $fr ? 'Comment ça fonctionne' : 'How it works' ?></a>
      <a class="eco-nav-link" href="<?= url('about') ?>"><?= $fr ? 'À propos' : 'About' ?></a>
      <div class="eco-lang" aria-label="<?= $fr ? 'Langue' : 'Language' ?>">
        <a href="?lang=fr" class="<?= $fr ? 'active' : '' ?>" aria-current="<?= $fr ? 'page' : 'false' ?>">FR</a>
        <a href="?lang=en" class="<?= !$fr ? 'active' : '' ?>" aria-current="<?= !$fr ? 'page' : 'false' ?>">EN</a>
      </div>
      <a class="eco-btn eco-btn-primary eco-header-join" href="<?= url('waitlist') ?>"><?= $fr ? 'Rejoindre OCSAPP' : 'Join OCSAPP' ?></a>
      <button type="button" class="eco-mobile-toggle" id="navToggle" aria-label="Menu" aria-expanded="false" aria-controls="mobileMenu">
        <i class="fa-solid fa-bars"></i>
      </button>
    </nav>
  </div>
  <div class="eco-wrap">
    <div class="eco-mobile-menu" id="mobileMenu">
      <a class="eco-mobile-menu-link" href="<?= url('') ?>#ecosysteme"><?= $fr ? 'Écosystème' : 'Ecosystem' ?></a>
      <a class="eco-mobile-menu-link" href="<?= url('') ?>#centrales"><?= $fr ? 'Nos Centrales' : 'Our Centrals' ?></a>
      <a class="eco-mobile-menu-link" href="<?= url('') ?>#fonctionnement"><?= $fr ? 'Comment ça fonctionne' : 'How it works' ?></a>
      <a class="eco-mobile-menu-link" href="<?= url('about') ?>"><?= $fr ? 'À propos' : 'About' ?></a>
      <div class="eco-mobile-menu-lang" aria-label="<?= $fr ? 'Langue' : 'Language' ?>">
        <a href="?lang=fr" class="<?= $fr ? 'active' : '' ?>" aria-current="<?= $fr ? 'page' : 'false' ?>">FR</a>
        <a href="?lang=en" class="<?= !$fr ? 'active' : '' ?>" aria-current="<?= !$fr ? 'page' : 'false' ?>">EN</a>
      </div>
      <a class="eco-btn eco-btn-primary" style="width:100%" href="<?= url('waitlist') ?>"><?= $fr ? 'Rejoindre OCSAPP' : 'Join OCSAPP' ?></a>
    </div>
  </div>
</header>

<main class="login-page">
  <div class="stage">
    <section class="auth-shell">

      <!-- ── Left brand panel ── -->
      <aside class="auth-side">
        <div class="side-brand">
          <div class="side-logo-wrap"><img src="<?= asset('images/logo.png') ?>" alt="OCSAPP"></div>
          <strong>OCSAPP</strong>
        </div>

        <div class="side-copy">
          <div class="eyebrow"><?= $fr ? 'Une infrastructure. Plusieurs accès.' : 'One infrastructure. Multiple access points.' ?></div>
          <h1><?= $fr
            ? 'Votre rôle.<br><span>Le même écosystème.</span>'
            : 'Your role.<br><span>The same ecosystem.</span>' ?></h1>
          <p><?= $fr
            ? "Connectez-vous au Central qui correspond à votre activité. Chaque accès demeure relié à la même infrastructure OCSAPP."
            : 'Sign in to the Central that matches your activity. Every access point stays connected to the same OCSAPP infrastructure.' ?></p>
        </div>

        <div class="side-list">
          <div class="side-item"><span class="check"><i class="fa-solid fa-check"></i></span><span><?= $fr ? 'Accès centralisé selon votre rôle' : 'Centralized access based on your role' ?></span></div>
          <div class="side-item"><span class="check"><i class="fa-solid fa-check"></i></span><span><?= $fr ? 'Commerce local connecté' : 'Connected local commerce' ?></span></div>
          <div class="side-item"><span class="check"><i class="fa-solid fa-check"></i></span><span><?= $fr ? 'Expérience bilingue' : 'Bilingual experience' ?></span></div>
          <div class="side-item"><span class="check"><i class="fa-solid fa-leaf"></i></span><span><?= $fr ? 'Objectif de livraison zéro émission' : 'Zero-emission delivery goal' ?></span></div>
        </div>

        <div class="side-foot"><?= $fr
          ? "OCSAPP · L'infrastructure numérique tout-en-un du commerce local."
          : 'OCSAPP · The all-in-one digital infrastructure for local commerce.' ?></div>
      </aside>

      <!-- ── Right form panel ── -->
      <div class="auth-main">
        <div class="main-kicker">
          <span><?= $fr ? 'Choisissez votre accès' : 'Choose your access' ?></span>
          <a class="ecosystem-link" href="<?= url('') ?>"><?= $fr ? "Voir l'écosystème" : 'View the ecosystem' ?> <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        </div>

        <div class="role-strip" role="tablist" aria-label="<?= $fr ? 'Sélection du portail de connexion' : 'Login portal selection' ?>">
          <?php foreach ($roles as $key => $r): ?>
            <button type="button"
                    class="role-btn<?= $key === $defaultRole ? ' active' : '' ?>"
                    role="tab"
                    aria-selected="<?= $key === $defaultRole ? 'true' : 'false' ?>"
                    data-role="<?= $key ?>"
                    data-action="<?= htmlspecialchars($r['action']) ?>"
                    data-forgot="<?= htmlspecialchars($r['forgot']) ?>"
                    data-cta="<?= htmlspecialchars($r['cta']) ?>"
                    data-cta-checkout="<?= htmlspecialchars($r['ctaCheckout'] ?? '') ?>"
                    data-fa="<?= htmlspecialchars($r['fa']) ?>"
                    data-context-label="<?= htmlspecialchars($r['contextLabel']) ?>"
                    data-learn-url="<?= htmlspecialchars($r['learnUrl']) ?>"
                    data-learn-label="<?= htmlspecialchars($r['learnLabel']) ?>"
                    data-no-account="<?= htmlspecialchars($r['noAccount']) ?>">
              <span class="role-art"><img src="<?= asset('images/login-roles/' . $r['icon']) ?>" alt="<?= htmlspecialchars($fr ? 'Icône officielle ' . $r['contextLabel'] : 'Official ' . $r['contextLabel'] . ' icon') ?>"></span>
              <span class="role-label"><?= htmlspecialchars($r['labelLine1']) ?><br><?= htmlspecialchars($r['labelLine2']) ?></span>
            </button>
          <?php endforeach; ?>
        </div>

        <div class="auth-head">
          <h2 id="formHeading"><?= $fr ? 'Bienvenue' : 'Welcome back' ?></h2>
          <p><?= $fr ? 'Connectez-vous pour accéder à votre espace OCSAPP.' : 'Sign in to access your OCSAPP space.' ?></p>
        </div>
        <div class="role-context" id="roleContext"><i class="fa-solid <?= htmlspecialchars($dr['fa']) ?>"></i> <span id="roleContextLabel"><?= htmlspecialchars($dr['contextLabel']) ?></span></div>

        <?php if ($driverVerified): ?>
          <div class="alert alert-success" id="driverVerifiedNotice"<?= $defaultRole !== 'driver' ? ' hidden' : '' ?>>
            <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($driverVerifiedMsg) ?>
          </div>
        <?php endif; ?>

        <?php if (hasFlash('error')): ?>
          <div class="alert alert-error"><?= getFlash('error') ?></div>
        <?php endif; ?>
        <?php if (hasFlash('success')): ?>
          <div class="alert alert-success"><?= htmlspecialchars(getFlash('success')) ?></div>
        <?php endif; ?>
        <?php if (hasFlash('info')): ?>
          <div class="alert alert-info"><?= htmlspecialchars(getFlash('info')) ?></div>
        <?php endif; ?>

        <?php if ($isCheckout && isset($_SESSION['pending_checkout_cart'])): ?>
          <div class="checkout-notice" id="checkoutNotice"<?= $defaultRole !== 'buyer' ? ' hidden' : '' ?>>
            <div class="checkout-notice-title">
              <i class="fa-solid fa-cart-shopping"></i> <?= $fr ? 'Complétez votre commande' : 'Complete Your Order' ?>
            </div>
            <div class="checkout-notice-desc">
              <?= $fr ? 'Connectez-vous pour procéder avec votre commande de' : 'Sign in to proceed with your' ?>
              <strong><?= count($_SESSION['pending_checkout_cart']) ?> <?= $fr ? 'article(s)' : 'item(s)' ?></strong>
            </div>
          </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="<?= htmlspecialchars($dr['action']) ?>">
          <?= csrfField() ?>
          <?php if ($redirect): ?>
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
          <?php endif; ?>

          <div class="form-group">
            <label for="email"><?= $fr ? 'Adresse courriel' : 'Email address' ?></label>
            <div class="input-wrap">
              <i class="fa-regular fa-envelope"></i>
              <input id="email" name="email" type="email" autocomplete="email"
                     value="<?= htmlspecialchars(old('email') ?? '') ?>"
                     placeholder="<?= $fr ? 'vous@exemple.com' : 'you@example.com' ?>" required autofocus>
            </div>
          </div>

          <div class="form-group">
            <label for="password"><?= $fr ? 'Mot de passe' : 'Password' ?></label>
            <div class="input-wrap">
              <i class="fa-solid fa-lock"></i>
              <input id="password" name="password" type="password" autocomplete="current-password"
                     placeholder="••••••••" required>
              <button class="toggle-pass" type="button" aria-label="<?= $fr ? 'Afficher le mot de passe' : 'Show password' ?>">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-options">
            <div class="checkbox-row">
              <input type="checkbox" id="remember" name="remember" class="form-checkbox">
              <label for="remember" class="checkbox-label"><?= $fr ? 'Se souvenir' : 'Remember me' ?></label>
            </div>
            <a id="forgotLink" href="<?= htmlspecialchars($dr['forgot']) ?>" class="forgot-link"><?= $fr ? 'Mot de passe oublié?' : 'Forgot password?' ?></a>
          </div>

          <button class="submit" id="submitBtn" type="submit">
            <i class="fa-solid fa-arrow-right-to-bracket" id="submitIcon"></i>
            <span id="submitText"><?= htmlspecialchars($isCheckout && $defaultRole === 'buyer' && $dr['ctaCheckout'] ? $dr['ctaCheckout'] : $dr['cta']) ?></span>
          </button>
        </form>

        <div class="below">
          <span id="noAccountText"><?= htmlspecialchars($dr['noAccount']) ?></span>
          <a id="learnLink" href="<?= htmlspecialchars($dr['learnUrl']) ?>"><?= htmlspecialchars($dr['learnLabel']) ?></a>
        </div>
        <div class="divider"></div>
        <div class="register-note"><?= $fr ? 'Nouveau sur OCSAPP?' : 'New to OCSAPP?' ?> <a href="<?= url('waitlist') ?>"><?= $fr ? "Rejoindre la liste d'attente" : 'Join the waitlist' ?></a></div>
      </div><!-- /auth-main -->
    </section>
  </div>
</main>

<footer class="mc-footer">
  <div class="mc-footer-wrap">
    <div class="mc-footer-top">
      <div class="mc-footer-brand-col">
        <div class="mc-footer-brand">
          <img src="<?= asset('images/logo.png') ?>" alt="<?= $fr ? 'Logo OCSAPP' : 'OCSAPP Logo' ?>">
          <span class="mc-footer-logo-text">OCSAPP</span>
        </div>
        <p class="mc-footer-tagline"><?= $fr ? "L'infrastructure numérique tout-en-un du commerce local." : 'The all-in-one digital infrastructure for local commerce.' ?></p>
        <p><?= $fr
          ? 'OCSAPP Inc. · Constituée sous le régime fédéral de la Loi canadienne sur les sociétés par actions (n<sup>o</sup> de société 1750354-7) · Numéro d\'entreprise du Québec (NEQ) 1181584997'
          : 'OCSAPP Inc. · Federally incorporated under the Canada Business Corporations Act (Corporation No. 1750354-7) · Quebec enterprise number (NEQ) 1181584997'
        ?></p>
        <p><?= $fr ? 'Siège social : Laval, Québec (H7H)' : 'Registered office: Laval, Québec (H7H)' ?></p>
      </div>

      <div class="mc-footer-col">
        <h5><?= $fr ? 'Apprenez à nous connaître' : 'Get to Know Us' ?></h5>
        <a href="<?= url('about') ?>"><?= $fr ? "À propos d'OCSAPP" : 'About OCSAPP' ?></a>
        <a href="<?= url('contact') ?>"><?= $fr ? 'Contactez-nous' : 'Contact Us' ?></a>
      </div>

      <div class="mc-footer-col">
        <h5><?= $fr ? 'Écosystème OCSAPP' : 'OCSAPP Ecosystem' ?></h5>
        <a href="<?= url('home') ?>"><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></a>
        <a href="<?= url('buyer-central') ?>"><?= $fr ? 'Acheteur Central' : 'Buyer Central' ?></a>
        <a href="<?= url('seller-central') ?>"><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></a>
        <a href="<?= url('supplier-central') ?>"><?= $fr ? 'Fournisseur Central' : 'Supplier Central' ?></a>
        <a href="<?= url('driver-central') ?>"><?= $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA' ?></a>
        <a href="<?= url('distribution') ?>"><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></a>
      </div>

      <div class="mc-footer-col">
        <h5><?= $fr ? 'Connectez-vous avec nous' : 'Connect With Us' ?></h5>
        <a href="https://www.facebook.com/ocsapp.ca" target="_blank" rel="noopener">Facebook</a>
        <a href="https://www.instagram.com/ocsapp.ca" target="_blank" rel="noopener">Instagram</a>
        <a href="https://www.linkedin.com/company/ocsapp" target="_blank" rel="noopener">LinkedIn</a>
      </div>
    </div>

    <div class="mc-footer-bottom">
      <p>OCSAPP &copy; <?= date('Y') ?>. <?= $fr ? 'Tous droits réservés.' : 'All rights reserved.' ?></p>
      <div class="mc-footer-legal">
        <a href="<?= url('privacy') ?>"><?= $fr ? 'Politique de confidentialité' : 'Privacy Policy' ?></a>
        <a href="<?= url('terms') ?>"><?= $fr ? "Conditions d'utilisation" : 'Terms of Service' ?></a>
        <a href="<?= url('cookies') ?>"><?= $fr ? 'Politique de cookies' : 'Cookie Policy' ?></a>
        <a href="<?= url('returns') ?>"><?= $fr ? 'Retours' : 'Returns' ?></a>
        <a href="<?= url('accessibility') ?>"><?= $fr ? 'Accessibilité' : 'Accessibility' ?></a>
      </div>
    </div>
  </div>
</footer>

<script>
(function(){
  var navToggle = document.getElementById('navToggle');
  var mobileMenu = document.getElementById('mobileMenu');
  if (navToggle && mobileMenu) {
    navToggle.addEventListener('click', function(){
      var open = mobileMenu.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      navToggle.innerHTML = open ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-bars"></i>';
    });
    mobileMenu.querySelectorAll('a').forEach(function(link){
      link.addEventListener('click', function(){
        mobileMenu.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
      });
    });
  }
})();

(function(){
  const isCheckout = <?= $isCheckout ? 'true' : 'false' ?>;
  const roleButtons = [...document.querySelectorAll('.role-btn')];
  const form = document.getElementById('loginForm');
  const submitText = document.getElementById('submitText');
  const forgot = document.getElementById('forgotLink');
  const contextIcon = document.querySelector('#roleContext i');
  const contextLabel = document.getElementById('roleContextLabel');
  const learnLink = document.getElementById('learnLink');
  const noAccountText = document.getElementById('noAccountText');
  const checkoutNotice = document.getElementById('checkoutNotice');
  const driverNotice = document.getElementById('driverVerifiedNotice');

  function selectRole(role, persist) {
    const btn = roleButtons.find(b => b.dataset.role === role);
    if (!btn) return;

    roleButtons.forEach(b => {
      b.classList.remove('active');
      b.setAttribute('aria-selected', 'false');
    });
    btn.classList.add('active');
    btn.setAttribute('aria-selected', 'true');

    form.action = btn.dataset.action;
    submitText.textContent = (isCheckout && role === 'buyer' && btn.dataset.ctaCheckout)
      ? btn.dataset.ctaCheckout
      : btn.dataset.cta;
    forgot.href = btn.dataset.forgot;
    contextIcon.className = 'fa-solid ' + btn.dataset.fa;
    contextLabel.textContent = btn.dataset.contextLabel;
    learnLink.href = btn.dataset.learnUrl;
    learnLink.textContent = btn.dataset.learnLabel;
    noAccountText.textContent = btn.dataset.noAccount;

    if (checkoutNotice) checkoutNotice.hidden = (role !== 'buyer');
    if (driverNotice) driverNotice.hidden = (role !== 'driver');

    if (persist) {
      try { localStorage.setItem('ocsLoginRole', role); } catch(e) {}
    }
  }

  roleButtons.forEach(btn => {
    btn.addEventListener('click', () => selectRole(btn.dataset.role, true));
  });

  // Restore the last-used role, unless the server already forced one
  // (checkout always forces buyer; a verified driver link forces driver).
  if (!isCheckout && !<?= $driverVerified ? 'true' : 'false' ?>) {
    try {
      const saved = localStorage.getItem('ocsLoginRole');
      if (saved && roleButtons.some(b => b.dataset.role === saved) && saved !== '<?= $defaultRole ?>') {
        selectRole(saved, false);
      }
    } catch(e) {}
  }

  const pass = document.getElementById('password');
  document.querySelector('.toggle-pass').addEventListener('click', function(){
    pass.type = pass.type === 'password' ? 'text' : 'password';
    this.innerHTML = pass.type === 'password'
      ? '<i class="fa-regular fa-eye"></i>'
      : '<i class="fa-regular fa-eye-slash"></i>';
    this.setAttribute('aria-label', pass.type === 'password'
      ? '<?= $fr ? "Afficher le mot de passe" : "Show password" ?>'
      : '<?= $fr ? "Masquer le mot de passe" : "Hide password" ?>');
  });
})();
</script>
</body>
</html>
<?php clearOldInput(); ?>
