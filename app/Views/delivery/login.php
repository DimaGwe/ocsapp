<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'page_title'    => 'Driver Login - OCSAPP Driver Central',
        'portal_badge'  => 'Driver Central',
        'card_title'    => 'Driver Sign In',
        'card_subtitle' => 'Access your driver dashboard',
        'email_label'   => 'Email Address',
        'password_label'=> 'Password',
        'password_ph'   => 'Enter your password',
        'remember'      => 'Remember me for 30 days',
        'forgot'        => 'Forgot password?',
        'submit'        => 'Sign In',
        'no_account'    => 'Not a driver yet?',
        'register_link' => 'Apply to Drive',
        'shop_now'      => 'Shop Now',
        'become_seller' => 'Become a Seller',
        'footer'        => 'All Rights Reserved.',
        'back_label'    => 'Driver Central',
        'home'          => 'Home',
    ],
    'fr' => [
        'page_title'    => 'Connexion livreur - OCSAPP Espace Livreur',
        'portal_badge'  => 'Espace Livreur',
        'card_title'    => 'Connexion livreur',
        'card_subtitle' => 'Accédez à votre tableau de bord livreur',
        'email_label'   => 'Adresse courriel',
        'password_label'=> 'Mot de passe',
        'password_ph'   => 'Entrez votre mot de passe',
        'remember'      => 'Se souvenir de moi 30 jours',
        'forgot'        => 'Mot de passe oublié?',
        'submit'        => 'Se connecter',
        'no_account'    => 'Pas encore livreur?',
        'register_link' => 'Postuler comme livreur',
        'shop_now'      => 'Magasiner',
        'become_seller' => 'Devenir vendeur',
        'footer'        => 'Tous droits réservés.',
        'back_label'    => 'Espace Livreur',
        'home'          => 'Accueil',
    ],
];
$t = $translations[$currentLang] ?? $translations['fr'];
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generateCsrfToken() ?>">
    <title><?= $t['page_title'] ?></title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/pages/delivery-login.css') ?>">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-links">
            <a href="<?= url('driver-central') ?>" class="nav-link primary">
                <i class="fa-solid fa-truck"></i>
                <span><?= $t['back_label'] ?></span>
            </a>
            <a href="<?= url('/') ?>" class="nav-link">
                <i class="fa-solid fa-house"></i>
                <span class="hide-mobile"><?= $t['home'] ?></span>
            </a>
            <a href="<?= url('home') ?>" class="nav-link">
                <i class="fa-solid fa-store"></i>
                <span class="hide-mobile"><?= $currentLang === 'fr' ? 'Marché' : 'Marketplace' ?></span>
            </a>
        </div>
    </nav>

    <main class="main-content">
        <div class="login-container">
            <div class="brand-header">
                <div class="logo-container">
                    <div class="logo-icon">
                        <img src="<?= asset('images/logo.png') ?>" alt="OCSAPP Logo">
                    </div>
                    <h1 class="brand-name">OCSAPP</h1>
                </div>
                <p class="brand-tagline">Zero-Emission Local Commerce</p>
                <div class="portal-badge">
                    <i class="fas fa-truck"></i> <?= $t['portal_badge'] ?>
                </div>
            </div>

            <div class="login-card">
                <div class="card-header">
                    <h2 class="card-title"><?= $t['card_title'] ?></h2>
                    <p class="card-subtitle"><?= $t['card_subtitle'] ?></p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($flash = getFlash('success')): ?>
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash) ?></div>
                <?php endif; ?>
                <?php if ($flash = getFlash('error')): ?>
                    <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($flash) ?></div>
                <?php endif; ?>
                <?php if ($flash = getFlash('info')): ?>
                    <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> <?= htmlspecialchars($flash) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= url('login') ?>">
                    <?= csrfField() ?>
                    <input type="hidden" name="redirect" value="<?= url('delivery/dashboard') ?>">

                    <div class="form-group">
                        <label for="email" class="form-label"><?= $t['email_label'] ?></label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="form-input"
                                placeholder="driver@example.com" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label"><?= $t['password_label'] ?></label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="form-input"
                                style="padding-right: 44px;"
                                placeholder="<?= $t['password_ph'] ?>" required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="remember" name="remember" value="1" class="form-checkbox">
                            <label for="remember" class="checkbox-label"><?= $t['remember'] ?></label>
                        </div>
                        <a href="<?= url('forgot-password') ?>" class="forgot-link"><?= $t['forgot'] ?></a>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <?= $t['submit'] ?>
                    </button>
                </form>

                <div class="register-section">
                    <?= $t['no_account'] ?>
                    <a href="<?= url('delivery/apply') ?>" class="register-link"><?= $t['register_link'] ?></a>
                </div>

                <div class="divider"><span><?= $currentLang === 'fr' ? 'ou' : 'or' ?></span></div>
                <div class="quick-links">
                    <a href="<?= url('home') ?>" class="quick-link">
                        <i class="fa-solid fa-basket-shopping"></i>
                        <?= $t['shop_now'] ?>
                    </a>
                    <a href="<?= url('seller-central') ?>" class="quick-link">
                        <i class="fa-solid fa-store"></i>
                        <?= $t['become_seller'] ?>
                    </a>
                </div>
            </div>

            <div class="login-footer">
                OCSAPP &copy; <?= date('Y') ?>. <?= $t['footer'] ?>
            </div>
        </div>
    </main>

    <script>
        const toggle = document.getElementById('passwordToggle');
        const input  = document.getElementById('password');
        if (toggle && input) {
            toggle.addEventListener('click', () => {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                toggle.innerHTML = show
                    ? '<i class="fa-solid fa-eye-slash"></i>'
                    : '<i class="fa-solid fa-eye"></i>';
            });
        }
    </script>
</body>
</html>
