<?php
/**
 * Supplier Reset Password Page
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
  <title><?= $fr ? 'Réinitialiser le mot de passe' : 'Reset Password' ?> - OCSAPP</title>
  <?= csrfMeta() ?>

  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">

  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    .reset-page {
      min-height: calc(100vh - 200px);
      display: flex; align-items: center; justify-content: center;
      padding: 60px 20px;
      background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
    }
    .reset-container {
      background: white; border-radius: 16px;
      box-shadow: 0 10px 40px rgba(0,178,7,0.1);
      max-width: 480px; width: 100%; padding: 48px 40px;
    }
    .reset-header { text-align: center; margin-bottom: 32px; }
    .reset-icon {
      width: 80px; height: 80px;
      background: linear-gradient(135deg, #00b207 0%, #008505 100%);
      border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 24px; font-size: 36px; color: white;
    }
    .reset-header h1 { font-size: 26px; color: #1f2937; margin-bottom: 8px; }
    .reset-header p  { color: #6b7280; font-size: 14px; line-height: 1.6; }
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
    .password-hint { font-size: 12px; color: #9ca3af; margin-top: 6px; }
    .btn-reset {
      width: 100%; padding: 14px;
      background: linear-gradient(135deg, #00b207 0%, #008505 100%);
      color: white; border: none; border-radius: 8px;
      font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-reset:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,178,7,0.3); }
    .alert {
      padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;
      font-size: 14px; display: flex; align-items: center; gap: 10px;
    }
    .alert-error   { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }
    .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
    .email-display {
      background: #f3f4f6; padding: 10px 16px; border-radius: 8px;
      font-size: 14px; color: #374151; margin-bottom: 24px;
      display: flex; align-items: center; gap: 8px;
    }
    .email-display i { color: #00b207; }
    .back-link { text-align: center; margin-top: 24px; }
    .back-link a {
      color: #00b207; text-decoration: none; font-size: 14px; font-weight: 500;
      display: inline-flex; align-items: center; gap: 6px;
    }
    .back-link a:hover { text-decoration: underline; }
  </style>
</head>
<body>

<?php include __DIR__ . '/../components/header.php'; ?>

<div class="reset-page">
  <div class="reset-container">
    <div class="reset-header">
      <div class="reset-icon">
        <i class="fas fa-lock-open"></i>
      </div>
      <h1><?= $fr ? 'Définir un nouveau mot de passe' : 'Set New Password' ?></h1>
      <p><?= $fr ? 'Entrez votre nouveau mot de passe ci-dessous.' : 'Enter your new password below.' ?></p>
    </div>

    <?php if (hasFlash('error')): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span><?= getFlash('error') ?></span>
      </div>
    <?php endif; ?>

    <div class="email-display">
      <i class="fas fa-envelope"></i>
      <span><?= htmlspecialchars($email ?? '') ?></span>
    </div>

    <form method="POST" action="<?= url('supplier/reset-password') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

      <div class="form-group">
        <label for="password" class="form-label"><?= $fr ? 'Nouveau mot de passe' : 'New Password' ?></label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" class="form-input"
            placeholder="<?= $fr ? 'Entrez un nouveau mot de passe' : 'Enter new password' ?>"
            required minlength="8" autofocus>
          <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <p class="password-hint"><?= $fr ? '8 caractères minimum' : 'Minimum 8 characters' ?></p>
      </div>

      <div class="form-group">
        <label for="password_confirmation" class="form-label"><?= $fr ? 'Confirmer le mot de passe' : 'Confirm Password' ?></label>
        <div class="password-wrapper">
          <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
            placeholder="<?= $fr ? 'Confirmez le nouveau mot de passe' : 'Confirm new password' ?>"
            required minlength="8">
          <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-reset">
        <i class="fas fa-check"></i>
        <span><?= $fr ? 'Réinitialiser le mot de passe' : 'Reset Password' ?></span>
      </button>
    </form>

    <div class="back-link">
      <a href="<?= url('supplier/login') ?>">
        <i class="fas fa-arrow-left"></i>
        <?= $fr ? 'Retour à la connexion' : 'Back to Login' ?>
      </a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>

<script>
function togglePassword(inputId, btn) {
  var input = document.getElementById(inputId);
  var icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
}
</script>

</body>
</html>
