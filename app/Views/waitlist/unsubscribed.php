<?php
/**
 * Waitlist unsubscribe result page (link in waitlist email footers).
 * $done: true when the token matched and the person is now unsubscribed.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr   = $currentLang === 'fr';
$done = !empty($done);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <title><?= $fr ? 'Désabonnement - OCSAPP' : 'Unsubscribe - OCSAPP' ?></title>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;box-sizing:border-box;font-family:'Inter',sans-serif;background:linear-gradient(145deg,#0d1c10,#14261a)}
    .wu-card{background:#fff;border-radius:18px;padding:44px 36px;max-width:480px;width:100%;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,.28)}
    .wu-icon{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:1.6rem;background:rgba(0,178,7,.12);color:#00b207}
    .wu-icon.warn{background:rgba(217,119,6,.12);color:#d97706}
    h1{font-family:'Poppins',sans-serif;font-size:1.45rem;font-weight:800;color:#111;margin:0 0 10px}
    p{color:#6b7280;font-size:.95rem;line-height:1.6;margin:0 0 22px}
    a.wu-btn{display:inline-block;background:#00b207;color:#fff;font-weight:700;text-decoration:none;padding:12px 26px;border-radius:10px}
    a.wu-link{color:#00b207;font-weight:600}
  </style>
</head>
<body>
  <div class="wu-card">
    <?php if ($done): ?>
      <div class="wu-icon"><i class="fa-solid fa-check"></i></div>
      <h1><?= $fr ? 'Désabonnement confirmé' : "You're unsubscribed" ?></h1>
      <p><?= $fr
        ? "Vous ne recevrez plus de courriels liés à la liste d'attente OCSAPP. Pour revenir sur votre décision, écrivez-nous à info@ocsapp.ca."
        : 'You will no longer receive OCSAPP waitlist emails. To change your mind, email us at info@ocsapp.ca.' ?></p>
    <?php else: ?>
      <div class="wu-icon warn"><i class="fa-solid fa-link-slash"></i></div>
      <h1><?= $fr ? 'Lien invalide ou expiré' : 'Invalid or expired link' ?></h1>
      <p><?= $fr ? 'Nous n\'avons pas pu traiter ce lien. Pour vous désabonner, écrivez-nous à' : "We couldn't process this link. To unsubscribe, email us at" ?>
        <a class="wu-link" href="mailto:info@ocsapp.ca">info@ocsapp.ca</a>.</p>
    <?php endif; ?>
    <a class="wu-btn" href="<?= url('/') ?>"><?= $fr ? "Retour à l'accueil" : 'Back to home' ?></a>
  </div>
</body>
</html>
