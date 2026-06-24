<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'page_title'    => 'Seller Login - OCSAPP Seller Central',
        'portal_badge'  => 'Seller Central',
        'card_title'    => 'Seller Sign In',
        'card_subtitle' => 'Access your seller dashboard',
        'email_label'   => 'Email Address',
        'password_label'=> 'Password',
        'password_ph'   => 'Enter your password',
        'remember'      => 'Remember me for 30 days',
        'forgot'        => 'Forgot password?',
        'submit'        => 'Sign In',
        'no_account'    => 'Not a seller yet?',
        'register_link' => 'Apply to Sell',
        'shop_now'      => 'Shop Now',
        'go_marketplace'=> 'Marketplace',
        'footer'        => 'All Rights Reserved.',
        'back_label'    => 'Seller Central',
        'home'          => 'Home',
        'show_password' => 'Show password',
        'hide_password' => 'Hide password',
    ],
    'fr' => [
        'page_title'    => 'Connexion vendeur - OCSAPP Seller Central',
        'portal_badge'  => 'Espace Vendeur',
        'card_title'    => 'Connexion vendeur',
        'card_subtitle' => 'Accédez à votre tableau de bord vendeur',
        'email_label'   => 'Adresse courriel',
        'password_label'=> 'Mot de passe',
        'password_ph'   => 'Entrez votre mot de passe',
        'remember'      => 'Se souvenir de moi 30 jours',
        'forgot'        => 'Mot de passe oublié?',
        'submit'        => 'Se connecter',
        'no_account'    => 'Pas encore vendeur?',
        'register_link' => 'Devenir vendeur',
        'shop_now'      => 'Magasiner',
        'go_marketplace'=> 'Marché',
        'footer'        => 'Tous droits réservés.',
        'back_label'    => 'Espace Vendeur',
        'home'          => 'Accueil',
        'show_password' => 'Afficher le mot de passe',
        'hide_password' => 'Masquer le mot de passe',
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #00b207;
            --primary-600: #009206;
            --primary-700: #007a05;
            --dark: #1a1a1a;
            --gray-50: #fafafa;
            --gray-100: #f5f5f5;
            --gray-200: #e5e5e5;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --border: #e5e5e5;
            --radius-md: 10px;
            --radius-xl: 16px;
            --radius-full: 999px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 28px rgba(0,0,0,0.12);
            --transition: 200ms ease;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--dark);
        }
        .top-nav {
            background: var(--gray-800);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: var(--radius-full);
            transition: all var(--transition);
        }
        .nav-link:hover { background: rgba(255,255,255,0.1); }
        .nav-link.primary { background: var(--primary); }
        .nav-link.primary:hover { background: var(--primary-600); }
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e9 50%, #c8e6c9 100%);
        }
        .login-container { width: 100%; max-width: 460px; }
        .brand-header { text-align: center; margin-bottom: 32px; }
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        .logo-icon {
            width: 56px; height: 56px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-md);
            padding: 8px;
            border: 3px solid var(--primary);
        }
        .logo-icon img { width: 100%; height: 100%; object-fit: contain; }
        .brand-name { font-size: 38px; font-weight: 800; color: var(--primary); letter-spacing: -0.5px; }
        .brand-tagline { font-size: 14px; color: var(--gray-600); font-weight: 500; }
        .portal-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: var(--primary-700);
            padding: 8px 16px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
            border: 1px solid var(--primary);
        }
        .login-card {
            background: #fff;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            padding: 36px;
        }
        .card-header { text-align: center; margin-bottom: 28px; }
        .card-title { font-size: 26px; font-weight: 700; color: var(--dark); margin-bottom: 6px; }
        .card-subtitle { font-size: 14px; color: var(--gray-500); }
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-error { background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; }
        .alert-success { background: #f0fdf4; border-left: 4px solid #22c55e; color: #166534; }
        .alert-info { background: #eff6ff; border-left: 4px solid #3b82f6; color: #1e40af; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 8px; }
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 16px;
            pointer-events: none;
        }
        .password-toggle {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            transition: color var(--transition);
            background: none;
            border: none;
        }
        .password-toggle:hover { color: var(--primary); }
        .form-input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 15px;
            font-family: inherit;
            transition: all var(--transition);
        }
        .form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,178,7,0.1); }
        .form-input::placeholder { color: var(--gray-500); }
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .checkbox-wrapper { display: flex; align-items: center; gap: 8px; }
        .form-checkbox { width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer; }
        .checkbox-label { font-size: 13px; color: var(--gray-600); cursor: pointer; user-select: none; }
        .forgot-link { font-size: 13px; color: var(--primary); font-weight: 600; text-decoration: none; }
        .forgot-link:hover { text-decoration: underline; }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all var(--transition);
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover { background: var(--primary-600); transform: translateY(-1px); box-shadow: var(--shadow-md); }
        .btn-submit:active { transform: translateY(0); }
        .register-section { margin-top: 24px; text-align: center; font-size: 14px; color: var(--gray-600); }
        .register-link { color: var(--primary); font-weight: 600; text-decoration: none; }
        .register-link:hover { text-decoration: underline; }
        .divider { display: flex; align-items: center; margin: 24px 0; color: var(--gray-500); font-size: 13px; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .divider span { padding: 0 16px; }
        .quick-links { display: flex; gap: 12px; }
        .quick-link {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            color: var(--gray-700);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all var(--transition);
        }
        .quick-link:hover { border-color: var(--primary); color: var(--primary); background: rgba(0,178,7,0.05); }
        .login-footer { text-align: center; margin-top: 24px; font-size: 13px; color: var(--gray-600); }
        @media (max-width: 768px) {
            .top-nav { padding: 10px 16px; }
            .nav-link { padding: 8px 12px; font-size: 13px; }
            .nav-link span.hide-mobile { display: none; }
            .main-content { padding: 24px 16px; }
            .login-card { padding: 28px 24px; }
            .brand-name { font-size: 32px; }
            .card-title { font-size: 22px; }
            .quick-links { flex-direction: column; }
        }
        @media (max-width: 480px) {
            .login-card { padding: 24px 20px; }
        }
    </style>
</head>
<body>
    <nav class="top-nav">
        <div class="nav-links">
            <a href="<?= url('seller-central') ?>" class="nav-link primary">
                <i class="fa-solid fa-store"></i>
                <span><?= $t['back_label'] ?></span>
            </a>
            <a href="<?= url('/') ?>" class="nav-link">
                <i class="fa-solid fa-house"></i>
                <span class="hide-mobile"><?= $t['home'] ?></span>
            </a>
            <a href="<?= url('home') ?>" class="nav-link">
                <i class="fa-solid fa-basket-shopping"></i>
                <span class="hide-mobile"><?= $t['go_marketplace'] ?></span>
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
                    <i class="fas fa-store"></i> <?= $t['portal_badge'] ?>
                </div>
            </div>

            <div class="login-card">
                <div class="card-header">
                    <h2 class="card-title"><?= $t['card_title'] ?></h2>
                    <p class="card-subtitle"><?= $t['card_subtitle'] ?></p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
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
                    <input type="hidden" name="redirect" value="<?= url('seller/dashboard') ?>">

                    <div class="form-group">
                        <label for="email" class="form-label"><?= $t['email_label'] ?></label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="form-input"
                                placeholder="seller@example.com" required autofocus>
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
                    <a href="<?= url('seller-central') ?>" class="register-link"><?= $t['register_link'] ?></a>
                </div>

                <div class="divider"><span><?= $currentLang === 'fr' ? 'ou' : 'or' ?></span></div>
                <div class="quick-links">
                    <a href="<?= url('home') ?>" class="quick-link">
                        <i class="fa-solid fa-basket-shopping"></i>
                        <?= $t['shop_now'] ?>
                    </a>
                    <a href="<?= url('buyer-central') ?>" class="quick-link">
                        <i class="fa-solid fa-user"></i>
                        <?= $currentLang === 'fr' ? 'Espace Acheteur' : 'Buyer Central' ?>
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
