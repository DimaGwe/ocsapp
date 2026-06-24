<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr     = ($currentLang === 'fr');
$portal = sanitize($_GET['portal'] ?? '');

$portalLabels = [
    'business' => $fr ? 'Portail entreprise' : 'Business Portal',
];
$portalLabel = $portalLabels[$portal] ?? '';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? 'Mot de passe oublié' : 'Forgot Password' ?> - OCSAPP</title>
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
    .subtitle { font-size: 14px; color: #6b7280; text-align: center; margin-bottom: 32px; line-height: 1.5; }
    .flash { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
    .flash.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .flash.error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    input[type="email"] { width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: inherit; transition: border-color .2s; }
    input[type="email"]:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,.1); }
    .btn { display: block; width: 100%; padding: 13px; background: #00b207; color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; font-family: inherit; cursor: pointer; margin-top: 20px; transition: background .2s; }
    .btn:hover { background: #009206; }
    .back-link { display: block; text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; text-decoration: none; }
    .back-link:hover { color: #00b207; }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo"><span>OCSAPP</span></div>
    <?php if ($portalLabel): ?>
      <div style="text-align:center;margin-bottom:18px;">
        <span style="display:inline-block;background:#f0fdf4;color:#007a05;border:1px solid #bbf7d0;border-radius:999px;padding:5px 16px;font-size:12px;font-weight:600;"><i class="fas fa-building"></i> <?= $portalLabel ?></span>
      </div>
    <?php endif; ?>
    <h1><?= $fr ? 'Mot de passe oublié?' : 'Forgot your password?' ?></h1>
    <p class="subtitle"><?= $fr ? 'Entrez votre courriel et nous vous enverrons un lien pour réinitialiser votre mot de passe.' : "Enter your email and we'll send you a link to reset your password." ?></p>

    <?php if ($flash = getFlash('success')): ?>
      <div class="flash success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>
    <?php if ($flash = getFlash('error')): ?>
      <div class="flash error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= url('forgot-password' . ($portal ? '?portal=' . urlencode($portal) : '')) ?>">
      <?= csrfField() ?>
      <div>
        <label for="email"><?= $fr ? 'Adresse courriel' : 'Email Address' ?></label>
        <input type="email" id="email" name="email" placeholder="vous@exemple.com" required autofocus>
      </div>
      <button type="submit" class="btn"><?= $fr ? 'Envoyer le lien' : 'Send Reset Link' ?></button>
    </form>

    <a href="<?= url($portal === 'distribution' ? 'distribution/login' : 'login') ?>" class="back-link">
      <i class="fas fa-arrow-left"></i> <?= $fr ? 'Retour à la connexion' : 'Back to login' ?>
    </a>
  </div>
</body>
</html>
