<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'page_title'       => 'Business Login - OCSAPP Distribution',
        'portal_badge'     => 'Distribution Portal',
        'card_title'       => 'Business Sign In',
        'card_subtitle'    => 'Access your business account',
        'email_label'      => 'Email Address',
        'password_label'   => 'Password',
        'password_ph'      => 'Enter your password',
        'submit'           => 'Sign In',
        'no_account'       => "Don't have a business account?",
        'register_link'    => 'Register Business',
        'shop_now'         => 'Shop Now',
        'become_seller'    => 'Become a Seller',
        'footer'           => 'All Rights Reserved.',
        'back_dist'        => 'Distribution',
        'home'             => 'Home',
        'marketplace'      => 'Marketplace',
        'customer_login'   => 'Customer Login',
        'show_password'    => 'Show password',
        'hide_password'    => 'Hide password',
    ],
    'fr' => [
        'page_title'       => 'Connexion entreprise - OCSAPP Distribution',
        'portal_badge'     => 'Portail de Distribution',
        'card_title'       => 'Connexion entreprise',
        'card_subtitle'    => 'Acc&#233;dez &#224; votre compte entreprise',
        'email_label'      => 'Adresse courriel',
        'password_label'   => 'Mot de passe',
        'password_ph'      => 'Entrez votre mot de passe',
        'submit'           => 'Se connecter',
        'no_account'       => 'Vous n\'avez pas de compte?',
        'register_link'    => 'S\'inscrire ici',
        'shop_now'         => 'Magasiner',
        'become_seller'    => 'Devenir vendeur',
        'footer'           => 'Tous droits r&#233;serv&#233;s.',
        'back_dist'        => 'Distribution',
        'home'             => 'Accueil',
        'marketplace'      => 'March&#233;',
        'customer_login'   => 'Connexion client',
        'show_password'    => 'Afficher le mot de passe',
        'hide_password'    => 'Masquer le mot de passe',
    ],
];
$t = $translations[$currentLang] ?? $translations['en'];
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generateCsrfToken() ?>">
    <title><?= $t['page_title'] ?></title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">
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
            --radius-sm: 8px;
            --radius-md: 10px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-full: 999px;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 28px rgba(0, 0, 0, 0.12);
            --transition-base: 200ms ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--dark);
        }

        /* Top Navigation Bar */
        .top-nav {
            background: var(--gray-800);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

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
            transition: all var(--transition-base);
            background: transparent;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link.primary {
            background: var(--primary);
        }

        .nav-link.primary:hover {
            background: var(--primary-600);
        }

        .nav-link i {
            font-size: 14px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e9 50%, #c8e6c9 100%);
        }

        .login-container {
            width: 100%;
            max-width: 460px;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .logo-icon {
            width: 56px;
            height: 56px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-md);
            padding: 8px;
            border: 3px solid var(--primary);
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-name {
            font-size: 38px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .brand-tagline {
            font-size: 14px;
            color: var(--gray-600);
            font-weight: 500;
        }

        /* Portal Badge */
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

        .card-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .card-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 6px;
        }

        .card-subtitle {
            font-size: 14px;
            color: var(--gray-500);
        }

        /* Flash Messages */
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

        .alert-error {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .alert-success {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            color: #166534;
        }

        .alert-info {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            color: #1e40af;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 16px;
            pointer-events: none;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            transition: color var(--transition-base);
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .form-input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 15px;
            font-family: inherit;
            transition: all var(--transition-base);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
        }

        .form-input::placeholder {
            color: var(--gray-500);
        }

        /* Submit Button */
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
            transition: all var(--transition-base);
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: var(--primary-600);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Register Link */
        .register-section {
            margin-top: 24px;
            text-align: center;
            font-size: 14px;
            color: var(--gray-600);
        }

        .register-link {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: color var(--transition-base);
        }

        .register-link:hover {
            color: var(--primary-600);
            text-decoration: underline;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: var(--gray-500);
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider span {
            padding: 0 16px;
        }

        /* Quick Links */
        .quick-links {
            display: flex;
            gap: 12px;
        }

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
            transition: all var(--transition-base);
        }

        .quick-link:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(0, 178, 7, 0.05);
        }

        .quick-link i {
            font-size: 16px;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: var(--gray-600);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .top-nav {
                padding: 10px 16px;
            }

            .nav-link {
                padding: 8px 12px;
                font-size: 13px;
            }

            .nav-link span.hide-mobile {
                display: none;
            }

            .main-content {
                padding: 24px 16px;
            }

            .login-card {
                padding: 28px 24px;
            }

            .brand-name {
                font-size: 32px;
            }

            .card-title {
                font-size: 22px;
            }

            .quick-links {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 24px 20px;
            }

            .nav-links {
                gap: 4px;
            }

            .nav-link {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="nav-links">
            <a href="<?= url('distribution') ?>" class="nav-link primary">
                <i class="fa-solid fa-truck"></i>
                <span><?= $t['back_dist'] ?></span>
            </a>
            <a href="<?= url('/') ?>" class="nav-link">
                <i class="fa-solid fa-house"></i>
                <span class="hide-mobile"><?= $t['home'] ?></span>
            </a>
            <a href="<?= url('home') ?>" class="nav-link">
                <i class="fa-solid fa-store"></i>
                <span class="hide-mobile"><?= $t['marketplace'] ?></span>
            </a>
        </div>
        <div class="nav-right">
            <a href="<?= url('login') ?>" class="nav-link">
                <i class="fa-solid fa-user"></i>
                <span class="hide-mobile"><?= $t['customer_login'] ?></span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="login-container">
            <!-- Brand Header -->
            <div class="brand-header">
                <div class="logo-container">
                    <div class="logo-icon">
                        <img src="<?= asset('images/logo.png') ?>" alt="OCSAPP Logo">
                    </div>
                    <h1 class="brand-name">OCSAPP</h1>
                </div>
                <p class="brand-tagline">Zero-Emission Grocery Delivery</p>
                <div class="portal-badge">
                    <i class="fas fa-building"></i> <?= $t['portal_badge'] ?>
                </div>
            </div>

            <!-- Login Card -->
            <div class="login-card">
                <div class="card-header">
                    <h2 class="card-title"><?= $t['card_title'] ?></h2>
                    <p class="card-subtitle"><?= $t['card_subtitle'] ?></p>
                </div>

                <!-- Flash Messages -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($flash = getFlash('success')): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <?= htmlspecialchars($flash) ?>
                    </div>
                <?php endif; ?>

                <?php if ($flash = getFlash('error')): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($flash) ?>
                    </div>
                <?php endif; ?>

                <?php if ($flash = getFlash('info')): ?>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-circle-info"></i>
                        <?= htmlspecialchars($flash) ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="<?= url('distribution/login') ?>">
                    <?= csrfField() ?>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label"><?= $t['email_label'] ?></label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                placeholder="business@company.com"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label"><?= $t['password_label'] ?></label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                style="padding-right: 44px;"
                                placeholder="<?= $t['password_ph'] ?>"
                                required
                            >
                            <i class="fa-solid fa-eye password-toggle" id="passwordToggle" title="<?= $t['show_password'] ?>"></i>
                        </div>
                    </div>

                    <!-- Remember Me + Forgot Password -->
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:16px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <input type="checkbox" id="remember" name="remember" value="1" style="width:16px;height:16px;accent-color:#00b207;cursor:pointer;">
                            <label for="remember" style="font-size:13px;color:#6b7280;cursor:pointer;user-select:none;"><?= $currentLang === 'fr' ? 'Se souvenir de moi' : 'Remember me' ?></label>
                        </div>
                        <a href="<?= url('forgot-password?portal=distribution') ?>" style="font-size:13px;color:#00b207;text-decoration:none;font-weight:500;"><?= $currentLang === 'fr' ? 'Mot de passe oublié?' : 'Forgot password?' ?></a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <?= $t['submit'] ?>
                    </button>
                </form>

                <!-- Register Link -->
                <div class="register-section">
                    <?= $t['no_account'] ?>
                    <a href="<?= url('distribution/register') ?>" class="register-link"><?= $t['register_link'] ?></a>
                </div>

                <!-- Quick Links -->
                <div class="divider"><span>or</span></div>
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

            <!-- Footer -->
            <div class="login-footer">
                OCSAPP &copy; <?= date('Y') ?>. <?= $t['footer'] ?>
            </div>
        </div>
    </main>

    <script>
        // Password visibility toggle
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        const showText = <?= json_encode($t['show_password']) ?>;
        const hideText = <?= json_encode($t['hide_password']) ?>;

        if (passwordToggle && passwordInput) {
            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'text') {
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                    this.title = hideText;
                } else {
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                    this.title = showText;
                }
            });
        }
    </script>
</body>
</html>
