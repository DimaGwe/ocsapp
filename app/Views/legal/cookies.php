<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$cartCount = 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie Policy - OCS Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f9f9f9; color: #333; }
        .legal-wrap { max-width: 860px; margin: 40px auto; padding: 0 24px 60px; }
        .legal-card { background: #fff; border-radius: 16px; padding: 48px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        h1 { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        .updated { font-size: 13px; color: #888; margin-bottom: 32px; }
        h2 { font-size: 18px; font-weight: 600; margin: 28px 0 10px; color: #00b207; }
        p, li { font-size: 14px; line-height: 1.8; color: #555; margin-bottom: 10px; }
        ul { padding-left: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { padding: 10px 14px; border: 1px solid #e0e0e0; font-size: 13px; text-align: left; }
        th { background: #f5f5f5; font-weight: 600; }
        a { color: #00b207; }
        @media (max-width: 640px) { .legal-card { padding: 24px; } h1 { font-size: 22px; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../components/header.php'; ?>

<div class="legal-wrap">
    <div class="legal-card">
        <h1>Cookie Policy</h1>
        <p class="updated">Last updated: February 25, 2026</p>

        <p>OCS Marketplace ("we", "us", "our") uses cookies and similar tracking technologies on our website to enhance your experience. This policy explains what cookies are, how we use them, and your choices.</p>

        <h2>What Are Cookies?</h2>
        <p>Cookies are small text files placed on your device when you visit a website. They allow the site to remember your preferences and actions over a period of time, so you don't have to re-enter them whenever you revisit.</p>

        <h2>Cookies We Use</h2>
        <table>
            <thead>
                <tr><th>Cookie</th><th>Type</th><th>Purpose</th><th>Duration</th></tr>
            </thead>
            <tbody>
                <tr><td>PHPSESSID</td><td>Essential</td><td>Maintains your login session and cart</td><td>Session</td></tr>
                <tr><td>_csrf_token</td><td>Essential</td><td>Security — prevents cross-site request forgery</td><td>Session</td></tr>
                <tr><td>cookie_consent</td><td>Functional</td><td>Remembers your cookie consent choice</td><td>1 year</td></tr>
                <tr><td>language</td><td>Functional</td><td>Remembers your preferred language (EN/FR)</td><td>1 year</td></tr>
                <tr><td>location</td><td>Functional</td><td>Remembers your delivery zone selection</td><td>30 days</td></tr>
            </tbody>
        </table>

        <h2>Essential Cookies</h2>
        <p>These cookies are strictly necessary for the website to function. They enable core features like login, shopping cart, and security. You cannot opt out of essential cookies.</p>

        <h2>Functional Cookies</h2>
        <p>These cookies remember your preferences (language, location) to provide a better experience. You can disable them, but some features may not work as expected.</p>

        <h2>Analytics &amp; Advertising</h2>
        <p>Currently, OCS Marketplace does not use third-party analytics or advertising cookies.</p>

        <h2>Your Choices</h2>
        <p>You can control cookies through your browser settings. Note that blocking essential cookies will prevent the site from functioning properly. You can also use the cookie consent banner at the bottom of any page to manage your preferences.</p>

        <h2>Contact Us</h2>
        <p>If you have questions about our use of cookies, contact us at <a href="mailto:privacy@ocsapp.ca">privacy@ocsapp.ca</a>.</p>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
