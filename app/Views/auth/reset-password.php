<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr     = ($currentLang === 'fr');
$token  = $token  ?? '';
$email  = $email  ?? '';
$portal = $portal ?? '';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? 'Réinitialiser le mot de passe' : 'Reset Password' ?> - OCSAPP</title>
  <?= csrfMeta() ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', sans-serif; background: #f5f5f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
    .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 48px 40px; width: 100%; max-width: 420px; }
    .logo { text-align: center; margin-bottom: 32px; }
    .logo span { font-size: 24px; font-weight: 800; color: #00b207; }
    h1 { font-size: 22px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px; text-align: center; }
    .subtitle { font-size: 14px; color: #6b7280; text-align: center; margin-bottom: 32px; }
    .flash { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
    .flash.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .form-group { margin-bottom: 16px; }
    label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .input-wrap { position: relative; }
    input[type="password"], input[type="text"] { width: 100%; padding: 12px 42px 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: inherit; transition: border-color .2s; }
    input:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,.1); }
    .toggle-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9ca3af; padding: 4px; }
    .toggle-btn:hover { color: #6b7280; }
    .hint { font-size: 12px; color: #6b7280; margin-top: 4px; }
    .btn { display: block; width: 100%; padding: 13px; background: #00b207; color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; font-family: inherit; cursor: pointer; margin-top: 8px; transition: background .2s; }
    .btn:hover { background: #009206; }
    .back-link { display: block; text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; text-decoration: none; }
    .back-link:hover { color: #00b207; }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo"><span>OCSAPP</span></div>
    <h1><?= $fr ? 'Créer un nouveau mot de passe' : 'Set a new password' ?></h1>
    <p class="subtitle"><?= $fr ? 'Pour' : 'For' ?> <strong><?= htmlspecialchars($email) ?></strong></p>

    <?php if ($flash = getFlash('error')): ?>
      <div class="flash error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= url('reset-password') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <input type="hidden" name="portal" value="<?= htmlspecialchars($portal) ?>">

      <div class="form-group">
        <label for="password"><?= $fr ? 'Nouveau mot de passe' : 'New Password' ?></label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" minlength="8" required autofocus>
          <button type="button" class="toggle-btn" onclick="togglePw('password', this)" title="<?= $fr ? 'Afficher/masquer' : 'Show/hide password' ?>">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <div class="hint"><?= $fr ? 'Minimum 8 caractères' : 'Minimum 8 characters' ?></div>
      </div>

      <div class="form-group">
        <label for="password_confirmation"><?= $fr ? 'Confirmer le nouveau mot de passe' : 'Confirm New Password' ?></label>
        <div class="input-wrap">
          <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" required>
          <button type="button" class="toggle-btn" onclick="togglePw('password_confirmation', this)" title="<?= $fr ? 'Afficher/masquer' : 'Show/hide password' ?>">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn"><?= $fr ? 'Réinitialiser le mot de passe' : 'Reset Password' ?></button>
    </form>

    <a href="<?= url($portal === 'distribution' ? 'distribution/login' : 'login') ?>" class="back-link">
      <i class="fas fa-arrow-left"></i> <?= $fr ? 'Retour à la connexion' : 'Back to login' ?>
    </a>
  </div>

  <script>
    function togglePw(id, btn) {
      var f = document.getElementById(id);
      var i = btn.querySelector('i');
      if (f.type === 'password') { f.type = 'text'; i.classList.replace('fa-eye','fa-eye-slash'); }
      else { f.type = 'password'; i.classList.replace('fa-eye-slash','fa-eye'); }
    }
  </script>
</body>
</html>
