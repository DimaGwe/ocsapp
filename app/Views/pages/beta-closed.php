<?php
/**
 * Beta-mode page shown by BetaAccessHelper on every signup page while
 * settings.allow_registration is off. Points the visitor to the waitlist for their role.
 * $role: buyer|seller|supplier|driver|business   $badInvite: arrived with an invalid invite link
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr        = $currentLang === 'fr';
$role      = $role ?? 'buyer';
$badInvite = !empty($badInvite);

$roleNames = [
    'buyer'    => $fr ? 'acheteur'   : 'buyer',
    'seller'   => $fr ? 'vendeur'    : 'seller',
    'supplier' => $fr ? 'fournisseur' : 'supplier',
    'driver'   => $fr ? 'livreur'    : 'driver',
    'business' => $fr ? 'entreprise' : 'business',
];
$roleName    = $roleNames[$role] ?? $roleNames['buyer'];
$waitlistUrl = url('waitlist') . '?role=' . urlencode($role);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <title><?= $fr ? 'Inscriptions en mode bêta - OCSAPP' : 'Signup in beta - OCSAPP' ?></title>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/beta-closed.css') ?>">
</head>
<body>
  <main class="bc-card">
    <a href="<?= url('/') ?>" class="bc-logo"><img src="<?= asset('images/logo.png') ?>" alt="OCSAPP"></a>
    <span class="bc-eyebrow"><i class="fa-solid fa-flask"></i> <?= $fr ? 'Mode bêta' : 'Beta mode' ?></span>
    <h1><?= $fr ? 'Les inscriptions ne sont pas encore ouvertes' : 'Signup is not open yet' ?></h1>
    <p><?= $fr
      ? "OCSAPP est présentement en période bêta : la création de compte se fait sur invitation seulement. Rejoignez la liste d'attente et nous vous enverrons votre lien d'accès personnel, avec votre guide d'accueil, dès qu'une place se libère."
      : "OCSAPP is currently in beta, so accounts are by invitation only. Join the waitlist and we'll email you your personal access link, along with your onboarding guide, as soon as a spot opens up." ?></p>

    <?php if ($badInvite): ?>
    <div class="bc-note">
      <i class="fa-solid fa-circle-info"></i>
      <span><?= $fr
        ? "Ce lien d'invitation n'est pas valide pour cette page. Utilisez le lien exact reçu par courriel, ou écrivez-nous à info@ocsapp.ca."
        : "This invitation link isn't valid for this page. Use the exact link from your email, or contact us at info@ocsapp.ca." ?></span>
    </div>
    <?php endif; ?>

    <a class="bc-btn" href="<?= htmlspecialchars($waitlistUrl) ?>">
      <?= $fr ? "Rejoindre la liste d'attente ({$roleName})" : "Join the waitlist ({$roleName})" ?> <i class="fa-solid fa-arrow-right"></i>
    </a>
    <p class="bc-small">
      <?= $fr ? 'Vous avez reçu une invitation ? Utilisez le lien reçu par courriel.' : 'Already invited? Use the link in your email.' ?>
      <a href="<?= url('login') ?>"><?= $fr ? 'Déjà un compte ? Se connecter' : 'Have an account? Log in' ?></a>
    </p>
  </main>
</body>
</html>
