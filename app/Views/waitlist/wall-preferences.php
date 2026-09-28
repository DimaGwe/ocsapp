<?php
/**
 * Founders' Wall preferences (no login, token link from the founding-status email).
 * $row: waitlist row or null (bad/unknown token); $saved: just saved; $error: bad token/CSRF on save.
 * Both choices are presented with equal weight (CAI guidelines 2023-1, section 2.2).
 */
use App\Helpers\FoundersWallHelper;

$currentLang = $_SESSION['language'] ?? 'fr';
$fr    = $currentLang === 'fr';
$saved = !empty($saved);
$error = !empty($error);
$T     = fn(array $pair) => $fr ? $pair[0] : $pair[1];

if ($row) {
    $role    = $row['role'];
    $shownAs = FoundersWallHelper::displayName($role, $row['first_name'], $row['last_name'], $row['business_name']);
    $city    = FoundersWallHelper::displayCity($row['city_region']);
    $program = isset(FoundersWallHelper::PROGRAM_NAMES[$role]) ? $T(FoundersWallHelper::PROGRAM_NAMES[$role]) : '';
    $on      = (int) $row['wall_consent'] === 1;
    [$wallTitle, $wallBody] = FoundersWallHelper::consentText($fr);
    $self    = url('founders-wall/preferences') . '?t=' . urlencode($row['wall_token']);
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= $fr ? 'Mur des fondateurs : mon choix - OCSAPP' : "Founders' Wall: my choice - OCSAPP" ?></title>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;box-sizing:border-box;font-family:'Inter',sans-serif;background:linear-gradient(145deg,#0d1c10,#14261a)}
    .wp-card{background:#fff;border-radius:18px;padding:36px 32px;max-width:540px;width:100%;box-shadow:0 24px 64px rgba(0,0,0,.28)}
    .wp-lang{text-align:right;font-size:.8rem;margin-bottom:8px}
    .wp-lang a{color:#6b7280;text-decoration:none;margin-left:8px}
    .wp-lang a.on{color:#00b207;font-weight:700}
    h1{font-family:'Poppins',sans-serif;font-size:1.35rem;font-weight:800;color:#111;margin:0 0 8px}
    p{color:#4b5563;font-size:.93rem;line-height:1.6;margin:0 0 16px}
    .wp-status{display:flex;gap:10px;align-items:center;border-radius:12px;padding:12px 14px;font-size:.9rem;margin-bottom:18px}
    .wp-status.on{background:#ecfdf0;color:#166534}
    .wp-status.off{background:#f3f4f6;color:#374151}
    .wp-preview{border:1px solid #e5e7e4;border-radius:12px;padding:12px 14px;margin-bottom:18px;font-size:.88rem;color:#374151}
    .wp-preview strong{display:block;font-family:'Poppins',sans-serif;color:#142017;font-size:1rem}
    .wp-choice{display:grid;gap:10px;margin-bottom:16px}
    .wp-choice label{display:flex;gap:10px;align-items:flex-start;border:1px solid #e5e7e4;border-radius:12px;padding:12px 14px;cursor:pointer;font-size:.88rem;line-height:1.5;color:#374151}
    .wp-choice input{margin-top:3px;accent-color:#00b207}
    .wp-choice strong{color:#142017}
    button{width:100%;background:#142017;color:#fff;border:0;border-radius:10px;padding:13px;font:700 .95rem 'Poppins',sans-serif;cursor:pointer}
    .wp-saved{background:#ecfdf0;color:#166534;border-radius:10px;padding:10px 12px;font-size:.88rem;margin-bottom:14px}
    .wp-err{background:#fef2f2;color:#991b1b;border-radius:10px;padding:10px 12px;font-size:.88rem;margin-bottom:14px}
    .wp-foot{font-size:.78rem;color:#6b7280;margin:16px 0 0}
    .wp-foot a{color:#00b207;font-weight:600}
  </style>
</head>
<body>
  <div class="wp-card">
    <?php if (!$row): ?>
      <h1><?= $fr ? 'Lien invalide ou expiré' : 'Invalid or expired link' ?></h1>
      <p><?= $fr
        ? "Nous n'avons pas pu retrouver votre choix avec ce lien. Pour modifier votre choix pour le Mur des fondateurs, écrivez-nous à"
        : "We couldn't find your choice with this link. To change your Founders' Wall choice, email us at" ?>
        <a href="mailto:privacy@ocsapp.ca" style="color:#00b207;font-weight:600">privacy@ocsapp.ca</a>.</p>
    <?php else: ?>
      <div class="wp-lang">
        <a href="<?= $self ?>&lang=fr" class="<?= $fr ? 'on' : '' ?>">FR</a>
        <a href="<?= $self ?>&lang=en" class="<?= !$fr ? 'on' : '' ?>">EN</a>
      </div>
      <h1><?= $fr ? 'Mur des fondateurs : votre choix' : "Founders' Wall: your choice" ?></h1>
      <p><?= $fr
        ? "Le Mur des fondateurs est une section publique de la page Programmes fondateurs d'OCSAPP. Vous décidez si votre nom y apparaît, et vous pouvez changer d'avis en tout temps."
        : "The Founders' Wall is a public section of OCSAPP's Founding programs page. You decide whether your name appears there, and you can change your mind at any time." ?></p>

      <?php if ($saved): ?><div class="wp-saved"><i class="fa-solid fa-check"></i> <?= $fr ? 'Votre choix est enregistré.' : 'Your choice is saved.' ?></div><?php endif; ?>
      <?php if ($error): ?><div class="wp-err"><?= $fr ? "Votre choix n'a pas pu être enregistré. Rechargez la page et réessayez." : "Your choice couldn't be saved. Reload the page and try again." ?></div><?php endif; ?>

      <div class="wp-status <?= $on ? 'on' : 'off' ?>">
        <i class="fa-solid <?= $on ? 'fa-eye' : 'fa-eye-slash' ?>"></i>
        <span><?= $on
          ? ($fr ? 'Votre nom est affiché sur le Mur des fondateurs dès que votre statut fondateur est confirmé.' : 'Your name is shown on the Founders\' Wall once your founding status is confirmed.')
          : ($fr ? "Votre nom n'est pas affiché sur le Mur des fondateurs." : "Your name is not shown on the Founders' Wall.") ?></span>
      </div>

      <?php if ($role !== 'partner'): ?>
      <div class="wp-preview">
        <?= $fr ? 'Si vous acceptez, voici ce qui serait affiché :' : 'If you agree, this is what would be shown:' ?>
        <strong><?= htmlspecialchars($shownAs) ?></strong>
        <?= htmlspecialchars($program) ?><?= $city !== '' ? ' · ' . htmlspecialchars($city) : '' ?>
      </div>

      <form method="POST" action="<?= url('founders-wall/preferences') ?>">
        <?= csrfField() ?>
        <input type="hidden" name="t" value="<?= htmlspecialchars($row['wall_token']) ?>">
        <div class="wp-choice">
          <label><input type="radio" name="wall_consent" value="yes" <?= $on ? 'checked' : '' ?>>
            <span><strong><?= htmlspecialchars($wallTitle) ?></strong> <?= htmlspecialchars($wallBody) ?></span></label>
          <label><input type="radio" name="wall_consent" value="no" <?= !$on ? 'checked' : '' ?>>
            <span><strong><?= $fr ? 'Ne pas afficher mon nom.' : "Don't show my name." ?></strong> <?= $fr
              ? 'Votre nom est retiré du mur immédiatement. Vos avantages fondateurs ne changent pas.'
              : 'Your name is removed from the wall immediately. Your founding perks do not change.' ?></span></label>
        </div>
        <button type="submit"><?= $fr ? 'Enregistrer mon choix' : 'Save my choice' ?></button>
      </form>
      <?php else: ?>
      <p><?= $fr ? "Le rôle Partenaire ne fait pas partie d'un programme fondateur : votre nom n'est jamais affiché sur le mur." : "The Partner role is not part of a founding program: your name is never shown on the wall." ?></p>
      <?php endif; ?>

      <p class="wp-foot"><?= $fr ? 'Questions sur vos renseignements personnels :' : 'Questions about your personal information:' ?>
        <a href="mailto:privacy@ocsapp.ca">privacy@ocsapp.ca</a> ·
        <a href="<?= url('privacy') ?>"><?= $fr ? 'Politique de confidentialité' : 'Privacy policy' ?></a></p>
    <?php endif; ?>
  </div>
</body>
</html>
