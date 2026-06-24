<?php
/**
 * Supplier Login Page
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
  <title><?= $fr ? 'Connexion fournisseur' : 'Supplier Login' ?> - OCSAPP</title>
  <?= csrfMeta() ?>

  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">

  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    .login-page {
      min-height: calc(100vh - 200px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
      background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
    }
    .login-container {
      background: white;
      border-radius: 16px;
      box-shadow: 0 10px 40px rgba(0, 178, 7, 0.1);
      max-width: 480px;
      width: 100%;
      padding: 48px 40px;
    }
    .login-header { text-align: center; margin-bottom: 40px; }
    .login-icon {
      width: 80px; height: 80px;
      background: linear-gradient(135deg, #00b207 0%, #008505 100%);
      border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 24px;
      font-size: 40px; color: white;
    }
    .login-header h1 { font-size: 28px; color: #1f2937; margin-bottom: 8px; }
    .login-header p  { color: #6b7280; font-size: 14px; }
    .form-group { margin-bottom: 24px; }
    .form-label { display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; }
    .form-input {
      width: 100%; padding: 12px 16px;
      border: 2px solid #e5e7eb; border-radius: 8px;
      font-size: 15px; transition: all 0.2s; font-family: inherit;
    }
    .form-input:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,0.1); }
    .password-wrapper { position: relative; }
    .password-wrapper .form-input { padding-right: 48px; }
    .password-toggle {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer;
      color: #9ca3af; font-size: 18px; padding: 4px;
      display: flex; align-items: center; justify-content: center; transition: color 0.2s;
    }
    .password-toggle:hover { color: #00b207; }
    .checkbox-group { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; }
    .checkbox-group input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; accent-color: #00b207; }
    .checkbox-group label { font-size: 14px; color: #6b7280; cursor: pointer; }
    .btn-login {
      width: 100%; padding: 14px;
      background: linear-gradient(135deg, #00b207 0%, #008505 100%);
      color: white; border: none; border-radius: 8px;
      font-size: 16px; font-weight: 600; cursor: pointer;
      transition: all 0.3s;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,178,7,0.3); }
    .alert {
      padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;
      font-size: 14px; display: flex; align-items: center; gap: 10px;
    }
    .alert-error   { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }
    .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
    .divider { text-align: center; margin: 32px 0; position: relative; }
    .divider::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e5e7eb; }
    .divider span { background: white; padding: 0 16px; position: relative; color: #9ca3af; font-size: 13px; }
    .help-text { text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; line-height: 1.6; }
    .help-text a { color: #00b207; text-decoration: none; font-weight: 600; }
    .help-text a:hover { text-decoration: underline; }
    .portal-note {
      text-align: center; margin-top: 24px; padding: 12px 16px;
      background: #f0f9ff; border: 1px solid #bae6fd;
      border-radius: 8px; font-size: 13px; color: #475569; line-height: 1.5;
    }
    .portal-note i { color: #0ea5e9; margin-right: 4px; }
    .portal-note a { color: #00b207; font-weight: 600; text-decoration: none; }
    .portal-note a:hover { text-decoration: underline; }
    .back-link { text-align: center; margin-top: 16px; }
    .back-link a {
      color: #00b207; text-decoration: none; font-size: 14px; font-weight: 500;
      display: inline-flex; align-items: center; gap: 6px;
    }
    .back-link a:hover { text-decoration: underline; }

    /* suppress global header/footer spacing against colored sections */
    .header { margin-bottom: 0; }
    footer.footer { margin-top: 0; }
  </style>
</head>
<body>

<?php include __DIR__ . '/../components/header.php'; ?>

<div class="login-page">
  <div class="login-container">
    <div class="login-header">
      <div class="login-icon">
        <i class="fas fa-truck-ramp-box"></i>
      </div>
      <h1><?= $fr ? 'Portail fournisseur' : 'Supplier Portal' ?></h1>
      <p><?= $fr ? 'Connectez-vous pour gérer vos produits et commandes' : 'Sign in to manage your products and orders' ?></p>
    </div>

    <?php if (hasFlash('error')): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span><?= getFlash('error') ?></span>
      </div>
    <?php endif; ?>

    <?php if (hasFlash('success')): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span><?= getFlash('success') ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('supplier/login') ?>">
      <?= csrfField() ?>

      <div class="form-group">
        <label for="email" class="form-label"><?= $fr ? 'Adresse courriel' : 'Email Address' ?></label>
        <input type="email" id="email" name="email" class="form-input"
          placeholder="fournisseur@entreprise.com" required autofocus>
      </div>

      <div class="form-group">
        <label for="password" class="form-label"><?= $fr ? 'Mot de passe' : 'Password' ?></label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" class="form-input"
            placeholder="<?= $fr ? 'Entrez votre mot de passe' : 'Enter your password' ?>" required>
          <button type="button" class="password-toggle" id="passwordToggle"
                  title="<?= $fr ? 'Afficher le mot de passe' : 'Show password' ?>"
                  aria-label="<?= $fr ? 'Afficher le mot de passe' : 'Show password' ?>">
            <i class="fas fa-eye" id="toggleIcon"></i>
          </button>
        </div>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div class="checkbox-group" style="margin-bottom:0;">
          <input type="checkbox" id="remember" name="remember" value="1">
          <label for="remember"><?= $fr ? 'Se souvenir de moi pendant 30 jours' : 'Remember me for 30 days' ?></label>
        </div>
        <a href="<?= url('supplier/forgot-password') ?>" style="color:#00b207;text-decoration:none;font-size:13px;font-weight:500;white-space:nowrap;">
          <?= $fr ? 'Mot de passe oublié ?' : 'Forgot password?' ?>
        </a>
      </div>

      <button type="submit" class="btn-login">
        <i class="fas fa-sign-in-alt"></i>
        <span><?= $fr ? 'Se connecter' : 'Sign In' ?></span>
      </button>
    </form>

    <div class="divider">
      <span><?= $fr ? 'Besoin d\'aide ?' : 'Need Help?' ?></span>
    </div>

    <div class="help-text">
      <?= $fr ? 'Vous n\'avez pas accès ? Contactez votre gestionnaire de compte à' : 'Don\'t have access? Contact your account manager at' ?><br>
      <a href="mailto:info@ocsapp.ca">info@ocsapp.ca</a>
    </div>

    <div class="portal-note">
      <i class="fas fa-info-circle"></i>
      <?= $fr ? 'Ceci est le <strong>Portail Fournisseur</strong>. Vous cherchez un compte acheteur ou vendeur ?' : 'This is the <strong>Supplier Portal</strong>. Looking for a buyer or seller account?' ?>
      <a href="<?= url('login') ?>"><?= $fr ? 'Connectez-vous ici' : 'Log in here' ?></a>
    </div>

    <div class="back-link">
      <a href="<?= url('supplier-central') ?>">
        <i class="fas fa-arrow-left"></i>
        <?= $fr ? 'Retour au portail fournisseur' : 'Back to Supplier Portal' ?>
      </a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>

<script>
(function() {
  var toggle   = document.getElementById('passwordToggle');
  var input    = document.getElementById('password');
  var icon     = document.getElementById('toggleIcon');
  var showText = <?= json_encode($fr ? 'Afficher le mot de passe' : 'Show password') ?>;
  var hideText = <?= json_encode($fr ? 'Masquer le mot de passe' : 'Hide password') ?>;

  toggle.addEventListener('click', function() {
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.replace('fa-eye', 'fa-eye-slash');
      toggle.setAttribute('title', hideText);
      toggle.setAttribute('aria-label', hideText);
    } else {
      input.type = 'password';
      icon.classList.replace('fa-eye-slash', 'fa-eye');
      toggle.setAttribute('title', showText);
      toggle.setAttribute('aria-label', showText);
    }
  });
})();
</script>

</body>
</html>
