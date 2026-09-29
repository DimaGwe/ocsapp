<?php
/**
 * Home Profile invite acceptance (/home-profile/join?token=, HomeProfileController::joinForm)
 * A teen (13 to 17) invited by a parent or guardian creates their member account.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr     = ($currentLang === 'fr');
$token  = $token ?? '';
$invite = $invite ?? null;
$guardianName = $invite ? trim(html_entity_decode($invite['guardian_first_name'] . ' ' . $invite['guardian_last_name'], ENT_QUOTES)) : '';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <title><?= $fr ? 'Rejoindre le Profil Maison' : 'Join the Home Profile' ?> - OCSAPP</title>
  <?= csrfMeta() ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', sans-serif; background: #f5f5f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
    .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 44px 36px; width: 100%; max-width: 460px; }
    .logo { text-align: center; margin-bottom: 26px; }
    .logo span { font-size: 24px; font-weight: 800; color: #00b207; }
    h1 { font-size: 22px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px; text-align: center; }
    .subtitle { font-size: 14px; color: #6b7280; text-align: center; margin-bottom: 24px; line-height: 1.6; }
    .flash { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .rules { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px 16px 14px 32px; font-size: 13px; color: #166534; line-height: 1.7; margin-bottom: 22px; }
    .form-group { margin-bottom: 16px; }
    label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    input[type="password"], input[type="text"] { width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: inherit; }
    input:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,.1); }
    .hint { font-size: 12px; color: #6b7280; margin-top: 4px; }
    .consent { display: flex; gap: 10px; align-items: flex-start; font-size: 13px; font-weight: 400; color: #4b5563; line-height: 1.55; }
    .consent input { margin-top: 3px; accent-color: #00b207; }
    .consent a { color: #00b207; }
    .btn { display: block; width: 100%; padding: 13px; background: #00b207; color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; font-family: inherit; cursor: pointer; margin-top: 8px; }
    .btn:hover { background: #009206; }
    .back-link { display: block; text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; text-decoration: none; }
    @media (max-width: 480px) { .card { padding: 32px 20px; } }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo"><span>OCSAPP</span></div>

    <?php if (!$invite): ?>
      <h1><?= $fr ? 'Invitation invalide' : 'Invalid invite' ?></h1>
      <p class="subtitle"><?= $fr
        ? "Ce lien est invalide, a expiré ou a déjà été utilisé. Demandez à votre parent ou tuteur de vous envoyer une nouvelle invitation depuis son Profil Maison."
        : 'This link is invalid, has expired or was already used. Ask your parent or guardian to send you a new invite from their Home Profile.' ?></p>
      <a class="back-link" href="<?= url('/') ?>"><?= $fr ? "Retour à l'accueil" : 'Back to home' ?></a>
    <?php else: ?>
      <h1><?= $fr ? 'Rejoindre le Profil Maison' : 'Join the Home Profile' ?></h1>
      <p class="subtitle"><?= $fr
        ? 'Bonjour ' . htmlspecialchars(html_entity_decode($invite['first_name'], ENT_QUOTES)) . ', <strong>' . htmlspecialchars($guardianName) . '</strong> vous invite à rejoindre son Profil Maison.'
        : 'Hi ' . htmlspecialchars(html_entity_decode($invite['first_name'], ENT_QUOTES)) . ', <strong>' . htmlspecialchars($guardianName) . '</strong> is inviting you to their Home Profile.' ?></p>

      <?php if ($flash = getFlash('error')): ?>
        <div class="flash"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>

      <ul class="rules">
        <li><?= $fr ? 'Vos commandes sont payées par votre parent ou tuteur, qui en reçoit un avis.' : 'Your orders are paid by your parent or guardian, who is notified of them.' ?></li>
        <li><?= $fr ? 'Les produits réservés aux 18 ans et plus ne vous sont pas offerts.' : 'Products for ages 18+ are not available to you.' ?></li>
      </ul>

      <form method="POST" action="<?= url('home-profile/join') ?>">
        <?= csrfField() ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="form-group">
          <label><?= $fr ? 'Courriel' : 'Email' ?></label>
          <input type="text" value="<?= htmlspecialchars($invite['invite_email']) ?>" readonly>
        </div>
        <div class="form-group">
          <label for="last_name"><?= $fr ? 'Nom de famille' : 'Last name' ?></label>
          <input type="text" id="last_name" name="last_name" required maxlength="100" autocomplete="family-name">
        </div>
        <div class="form-group">
          <label for="password"><?= $fr ? 'Mot de passe' : 'Password' ?></label>
          <input type="password" id="password" name="password" required minlength="10" autocomplete="new-password">
          <div class="hint"><?= $fr ? '10 caractères minimum, avec majuscule, minuscule, chiffre et caractère spécial.' : 'At least 10 characters, with upper and lower case, a number and a special character.' ?></div>
        </div>
        <div class="form-group">
          <label for="password_confirmation"><?= $fr ? 'Confirmer le mot de passe' : 'Confirm password' ?></label>
          <input type="password" id="password_confirmation" name="password_confirmation" required minlength="10" autocomplete="new-password">
        </div>
        <div class="form-group">
          <label class="consent"><input type="checkbox" name="consent" required>
            <span><?= $fr
              ? "J'accepte les <a href=\"" . url('conditions-utilisation') . "\" target=\"_blank\">Conditions d'utilisation</a> et la <a href=\"" . url('confidentialite') . "\" target=\"_blank\">Politique de confidentialité</a>, avec le consentement de mon parent ou tuteur."
              : 'I agree to the <a href="' . url('terms') . '" target="_blank">Terms of Service</a> and <a href="' . url('privacy') . '" target="_blank">Privacy Policy</a>, with my parent\'s or guardian\'s consent.' ?></span>
          </label>
        </div>
        <button type="submit" class="btn"><?= $fr ? 'Créer mon compte' : 'Create my account' ?></button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
