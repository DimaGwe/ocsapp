<?php
/**
 * Buyer account sidebar (user card + navigation), shared by /account and its sub-pages.
 * Needs: $fr (bool), $user (array: first_name, last_name, email), $accountActive (dashboard|orders|addresses|wishlist|settings).
 * Optional: $founding (array with founding_buyer, founding_buyer_number) for the founder badge.
 */
$accountActive = $accountActive ?? 'dashboard';
$acctFirst = trim((string) ($user['first_name'] ?? ''));
$acctName  = trim($acctFirst . ' ' . ($user['last_name'] ?? ''));
$acctLinks = [
    'dashboard' => ['account',           'fa-gauge-high',     'Tableau de bord', 'Dashboard'],
    'orders'    => ['account/orders',    'fa-box',            'Mes commandes',   'My orders'],
    'addresses' => ['account/addresses', 'fa-location-dot',   'Mes adresses',    'Addresses'],
    'wishlist'  => ['account/wishlist',  'fa-heart',          'Liste de souhaits', 'Wishlist'],
    'settings'  => ['account/settings',  'fa-gear',           'Paramètres',      'Settings'],
];
?>
<aside class="acct-side">
  <div class="acct-card acct-user">
    <div class="acct-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($acctFirst !== '' ? $acctFirst : 'U', 0, 1))) ?></div>
    <div class="acct-user-name"><?= htmlspecialchars($acctName !== '' ? $acctName : ($fr ? 'Mon compte' : 'My account')) ?></div>
    <div class="acct-user-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
    <?php if (!empty($founding['founding_buyer'])): ?>
      <a class="acct-founder-pill" href="<?= url('founding') ?>"><i class="fa-solid fa-star"></i>
        <?= $fr ? 'Acheteur fondateur n° ' : 'Founding Buyer #' ?><?= (int) $founding['founding_buyer_number'] ?></a>
    <?php endif; ?>
  </div>
  <nav class="acct-card acct-nav" aria-label="<?= $fr ? 'Navigation du compte' : 'Account navigation' ?>">
    <?php foreach ($acctLinks as $key => [$path, $icon, $labelFr, $labelEn]): ?>
      <a href="<?= url($path) ?>" class="<?= $accountActive === $key ? 'active' : '' ?>"<?= $accountActive === $key ? ' aria-current="page"' : '' ?>>
        <i class="fa-solid <?= $icon ?>"></i><span><?= $fr ? $labelFr : $labelEn ?></span></a>
    <?php endforeach; ?>
    <a href="<?= url('logout') ?>" class="acct-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i><span><?= $fr ? 'Déconnexion' : 'Log out' ?></span></a>
  </nav>
</aside>
