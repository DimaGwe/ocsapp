<?php
/**
 * Buyer settings (/account/settings, AccountController::settings)
 * Updated 2026-09-28: Marché Central theme via the shared account partials, bilingual EN/FR.
 * Same three POST forms and field names. The email field is read-only: updateProfile() never
 * saved it, so editing it silently did nothing.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$user = $user ?? [];
$preferences = $preferences ?? ['email_orders' => 1, 'email_promotions' => 0, 'email_newsletter' => 0];
$accountActive = 'settings';
require __DIR__ . '/partials/account-helpers.php';
$u = acct_plain_row($user);

$toggles = [
    'email_orders'     => [$fr ? 'Suivi des commandes' : 'Order updates', $fr ? "Courriels sur l'état de vos commandes." : 'Emails about your order status.'],
    'email_promotions' => [$fr ? 'Promotions et offres' : 'Promotions and deals', $fr ? 'Offres spéciales et rabais.' : 'Special offers and discounts.'],
    'email_newsletter' => [$fr ? 'Infolettre' : 'Newsletter', $fr ? 'Les nouvelles du Marché Central.' : 'Marketplace Central news and updates.'],
];

$acctTitle   = $fr ? 'Paramètres' : 'Settings';
$acctHeading = $acctTitle;
$acctCrumb   = [$acctTitle, 'fa-gear'];
$acctSub     = $fr ? 'Votre profil, votre mot de passe et vos préférences de courriel.' : 'Your profile, password and email preferences.';
require __DIR__ . '/partials/account-top.php';
?>
                    <section class="acct-card acct-panel">
                        <h2><i class="fas fa-user acct-h-ico"></i> <?= $fr ? 'Profil' : 'Profile' ?></h2>
                        <form class="acct-form" method="POST" action="<?= url('account/settings/update-profile') ?>">
                            <?= csrfField() ?>
                            <div class="acct-form-row">
                                <label><?= $fr ? 'Prénom' : 'First name' ?><input type="text" name="first_name" value="<?= htmlspecialchars($u['first_name'] ?? '') ?>" required minlength="2" maxlength="50" autocomplete="given-name"></label>
                                <label><?= $fr ? 'Nom' : 'Last name' ?><input type="text" name="last_name" value="<?= htmlspecialchars($u['last_name'] ?? '') ?>" required minlength="2" maxlength="50" autocomplete="family-name"></label>
                            </div>
                            <label><?= $fr ? 'Adresse courriel' : 'Email address' ?>
                                <input type="email" value="<?= htmlspecialchars($u['email'] ?? '') ?>" readonly aria-describedby="emailHelp">
                                <small id="emailHelp"><?= $fr ? 'Pour changer votre adresse courriel, écrivez-nous à ' : 'To change your email address, write to ' ?><a href="mailto:info@ocsapp.ca">info@ocsapp.ca</a>.</small>
                            </label>
                            <label><?= $fr ? 'Téléphone' : 'Phone' ?><input type="tel" name="phone" value="<?= htmlspecialchars($u['phone'] ?? '') ?>" autocomplete="tel"></label>
                            <div><button type="submit" class="acct-btn acct-btn-primary"><?= $fr ? 'Enregistrer' : 'Save changes' ?></button></div>
                        </form>
                    </section>

                    <section class="acct-card acct-panel">
                        <h2><i class="fas fa-lock acct-h-ico"></i> <?= $fr ? 'Mot de passe' : 'Password' ?></h2>
                        <form class="acct-form" method="POST" action="<?= url('account/settings/update-password') ?>">
                            <?= csrfField() ?>
                            <label><?= $fr ? 'Mot de passe actuel' : 'Current password' ?><input type="password" name="current_password" required autocomplete="current-password"></label>
                            <div class="acct-form-row">
                                <label><?= $fr ? 'Nouveau mot de passe' : 'New password' ?><input type="password" name="new_password" required minlength="8" maxlength="72" autocomplete="new-password"></label>
                                <label><?= $fr ? 'Confirmer le nouveau mot de passe' : 'Confirm new password' ?><input type="password" name="new_password_confirmation" required minlength="8" maxlength="72" autocomplete="new-password"></label>
                            </div>
                            <small class="acct-dim"><?= $fr ? 'Au moins 8 caractères.' : 'At least 8 characters.' ?></small>
                            <div><button type="submit" class="acct-btn acct-btn-primary"><?= $fr ? 'Mettre à jour le mot de passe' : 'Update password' ?></button></div>
                        </form>
                    </section>

                    <section class="acct-card acct-panel">
                        <h2><i class="fas fa-bell acct-h-ico"></i> <?= $fr ? 'Notifications par courriel' : 'Email notifications' ?></h2>
                        <form method="POST" action="<?= url('account/settings/update-notifications') ?>">
                            <?= csrfField() ?>
                            <?php foreach ($toggles as $name => [$title, $desc]): ?>
                                <label class="acct-toggle-row">
                                    <span><strong><?= $title ?></strong><small><?= $desc ?></small></span>
                                    <span class="acct-switch">
                                        <input type="checkbox" name="<?= $name ?>" value="1" <?= !empty($preferences[$name]) ? 'checked' : '' ?>>
                                        <span class="acct-switch-slider"></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                            <div class="acct-form-foot"><button type="submit" class="acct-btn acct-btn-primary"><?= $fr ? 'Enregistrer mes préférences' : 'Save preferences' ?></button></div>
                        </form>
                    </section>
<?php require __DIR__ . '/partials/account-bottom.php'; ?>
</body>
</html>
