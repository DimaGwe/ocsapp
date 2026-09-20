<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#00B207">
  <title><?= $fr ? 'Connexion - Vendeur Central - OCSAPP' : 'Sign in - Seller Central - OCSAPP' ?></title>
  <?= csrfMeta() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/portal-login.css') ?>">
</head>
<body>
<header class="auth-topbar">
  <a class="auth-logo" href="<?= url('/') ?>" aria-label="OCSAPP">OCSAPP</a>
  <nav class="auth-toplinks" aria-label="<?= $fr ? 'Navigation de connexion' : 'Login navigation' ?>">
    <a href="<?= url('/') ?>"><?= $fr ? 'Écosystème' : 'Ecosystem' ?></a>
    <a href="<?= url('home') ?>"><?= $fr ? 'Marché Central' : 'Market Central' ?></a>
    <a class="central-link" href="<?= url('seller-central') ?>"><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></a>
  </nav>
</header>
<main class="auth-page">
  <section class="auth-brand-panel" aria-label="<?= $fr ? 'Vendeur Central' : 'Seller Central' ?>">
    <div class="auth-brand-content">
      <div class="auth-eyebrow"><?= $fr ? 'VENDEUR CENTRAL' : 'SELLER CENTRAL' ?></div>
      <div class="central-icon"><img src="<?= asset('images/about/central-seller.png') ?>" alt="<?= $fr ? 'Vendeur Central' : 'Seller Central' ?>"></div>
      <h1><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></h1>
      <p><?= $fr
        ? 'Accédez à votre tableau de bord vendeur pour gérer votre boutique, vos produits et vos commandes.'
        : 'Access your seller dashboard to manage your store, products and orders.' ?></p>
      <p class="ecosystem-note"><strong><?= $fr ? 'Un seul écosystème OCSAPP.' : 'One OCSAPP ecosystem.' ?></strong> <?= $fr ? 'Votre accès est lié au Central correspondant à votre rôle.' : 'Your access is linked to the Central associated with your role.' ?></p>
    </div>
  </section>
  <section class="auth-form-panel" id="main-content">
    <div class="auth-card">
      <div class="auth-card-kicker"><?= $fr ? 'CONNEXION SÉCURISÉE' : 'SECURE SIGN IN' ?></div>
      <h2><?= $fr ? 'Bienvenue' : 'Welcome' ?></h2>
      <p class="subtitle"><?= $fr
        ? 'Connectez-vous avec les identifiants associés à votre compte Vendeur Central.'
        : 'Sign in with the credentials associated with your Seller Central account.' ?></p>

      <?php if (!empty($error)): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($flash = getFlash('success')): ?>
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>
      <?php if ($flash = getFlash('error')): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>
      <?php if ($flash = getFlash('info')): ?>
        <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> <?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>

      <form action="<?= url('login') ?>" method="POST">
        <?= csrfField() ?>
        <div class="form-group">
          <label class="form-label" for="email"><?= $fr ? 'Adresse courriel' : 'Email address' ?></label>
          <div class="input-wrapper">
            <i class="fa-solid fa-envelope input-icon"></i>
            <input autocomplete="email" autofocus class="form-input" id="email" name="email" placeholder="seller@example.com" required type="email" value="<?= htmlspecialchars(old('email') ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="password"><?= $fr ? 'Mot de passe' : 'Password' ?></label>
          <div class="input-wrapper">
            <i class="fa-solid fa-lock input-icon"></i>
            <input autocomplete="current-password" class="form-input" id="password" name="password" placeholder="<?= $fr ? 'Entrez votre mot de passe' : 'Enter your password' ?>" required style="padding-right: 44px;" type="password">
            <button class="password-toggle" id="passwordToggle" type="button" aria-label="<?= $fr ? 'Afficher le mot de passe' : 'Show password' ?>">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>
        <div class="form-options">
          <div class="checkbox-wrapper">
            <input class="form-checkbox" id="remember" name="remember" type="checkbox" value="1">
            <label class="checkbox-label form-label" for="remember"><?= $fr ? 'Se souvenir de moi 30 jours' : 'Remember me for 30 days' ?></label>
          </div>
          <a class="forgot-link" href="<?= url('forgot-password') ?>"><?= $fr ? 'Mot de passe oublié?' : 'Forgot password?' ?></a>
        </div>
        <button class="btn-submit ocs-submit" type="submit"><i class="fa-solid fa-right-to-bracket"></i> <?= $fr ? 'Se connecter' : 'Sign in' ?></button>
      </form>
      <div class="register-block"><?= $fr ? 'Vous n’avez pas encore accès?' : "Don't have access yet?" ?> <a href="<?= url('seller-central') ?>"><?= $fr ? 'Découvrir Vendeur Central' : 'Explore Seller Central' ?></a></div>
      <div class="auth-help"><a href="<?= url('forgot-password') ?>"><?= $fr ? 'Mot de passe oublié?' : 'Forgot password?' ?></a><a href="<?= url('contact') ?>"><?= $fr ? 'Besoin d’aide? Contactez OCSAPP' : 'Need help? Contact OCSAPP' ?></a></div>
      <div class="secure-note"><?= $fr
        ? 'Pour votre sécurité, OCSAPP ne vous demandera jamais votre mot de passe par courriel ou par téléphone.'
        : 'For your security, OCSAPP will never ask for your password by email or phone.' ?></div>
    </div>
  </section>
</main>
<footer class="auth-footer">OCSAPP &copy; <?= date('Y') ?> · <?= $fr ? 'Tous droits réservés' : 'All rights reserved' ?> · <a href="<?= url('privacy') ?>"><?= $fr ? 'Confidentialité' : 'Privacy' ?></a> · <a href="<?= url('terms') ?>"><?= $fr ? 'Conditions' : 'Terms' ?></a></footer>
<script>
(function(){
  document.querySelectorAll('.password-toggle').forEach(function(toggle){
    toggle.addEventListener('click', function(){
      var wrap = toggle.closest('.input-wrapper, .password-wrapper') || toggle.parentElement;
      var input = wrap ? wrap.querySelector('input[type="password"], input[type="text"]') : null;
      if (!input) return;
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      toggle.setAttribute('aria-label', show ? <?= json_encode($fr ? 'Masquer le mot de passe' : 'Hide password') ?> : <?= json_encode($fr ? 'Afficher le mot de passe' : 'Show password') ?>);
      var icon = toggle.querySelector('i');
      if (icon) icon.className = show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
    });
  });
})();
</script>
</body>
</html>
