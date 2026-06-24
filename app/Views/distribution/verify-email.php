<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$t = getTranslations($currentLang);

$pending = $_SESSION['pending_business_verification'] ?? [];
$email   = $pending['email'] ?? '';
// Mask: show first 2 chars + *** + @ + domain
$maskedEmail = '';
if ($email) {
    [$local, $domain] = explode('@', $email, 2);
    $maskedEmail = substr($local, 0, 2) . str_repeat('*', max(3, strlen($local) - 2)) . '@' . $domain;
}

$errors   = $_SESSION['verify_errors'] ?? [];
$attempts = $_SESSION['verification_attempts'] ?? 0;
$maxAttempts = 5;
$remaining = $maxAttempts - $attempts;
unset($_SESSION['verify_errors']);
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $fr ? 'Vérification courriel - OCSAPP Distribution' : 'Email Verification - OCSAPP Distribution' ?></title>
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; }
        main.page { background: #f3f4f6; }

        /* Hero */
        .verify-hero {
            background: linear-gradient(135deg, #0a1628 0%, #0d2137 50%, #071220 100%);
            color: white;
            text-align: center;
            padding: 56px 24px 48px;
            position: relative;
            overflow: hidden;
        }
        .verify-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        .verify-hero-badge {
            display: inline-block;
            background: rgba(0,178,7,0.18);
            color: #4ade80;
            border: 1px solid rgba(0,178,7,0.35);
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        .verify-hero h1 {
            font-size: clamp(22px, 4vw, 32px);
            font-weight: 800;
            color: white;
            margin-bottom: 10px;
        }
        .verify-hero p {
            font-size: 15px;
            color: rgba(255,255,255,0.7);
            max-width: 440px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Card */
        .verify-page { max-width: 520px; margin: 0 auto; padding: 32px 20px 64px; }
        .verify-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-top: 3px solid #00b207;
            border-radius: 14px;
            padding: 36px 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-align: center;
        }

        .lock-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #dcfce7, #f0fdf4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            color: #00b207;
            border: 2px solid #bbf7d0;
        }
        .verify-card h2 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin-bottom: 10px; }
        .verify-card .sub {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .verify-card .sub strong { color: #1f2937; }

        /* OTP boxes */
        .otp-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 24px;
        }
        .otp-box {
            width: 52px;
            height: 60px;
            border: 2px solid #d1d5db;
            border-radius: 10px;
            font-size: 26px;
            font-weight: 700;
            text-align: center;
            color: #1a1a1a;
            background: #f9fafb;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            font-family: 'Inter', monospace;
            outline: none;
        }
        .otp-box:focus {
            border-color: #00b207;
            box-shadow: 0 0 0 3px rgba(0,178,7,0.12);
            background: #fff;
        }
        .otp-box.filled { border-color: #00b207; background: #f0fdf4; }
        .otp-box.error  { border-color: #ef4444; background: #fef2f2; }

        /* Hidden real input */
        #otpHidden { display: none; }

        /* Attempts */
        .attempts-bar {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 20px;
        }
        .attempts-bar.low { color: #ef4444; font-weight: 600; }

        /* Alerts */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px; text-align: left; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

        /* Buttons */
        .btn-verify {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #00b207 0%, #008505 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 16px;
        }
        .btn-verify:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,178,7,0.3); }
        .btn-verify:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .btn-verify .spinner {
            display: none; width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3); border-top-color: white;
            border-radius: 50%; animation: spin 0.6s linear infinite;
        }
        .btn-verify.loading .spinner { display: inline-block; }
        .btn-verify.loading .btn-label { display: none; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .resend-form { margin-top: 4px; }
        .btn-resend {
            background: none;
            border: none;
            color: #00b207;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            text-decoration: underline;
            padding: 0;
        }
        .btn-resend:hover { color: #008505; }
        .resend-note { font-size: 13px; color: #9ca3af; }

        @media (max-width: 480px) {
            .verify-card { padding: 24px 16px; }
            .otp-box { width: 42px; height: 52px; font-size: 22px; }
            .otp-group { gap: 8px; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../components/distribution-applicant-header.php'; ?>

<div class="verify-hero">
    <div class="verify-hero-badge">
        <i class="fas fa-shield-halved"></i>
        <?= $fr ? 'Portail Distribution' : 'Distribution Portal' ?>
    </div>
    <h1><?= $fr ? 'Vérification de votre courriel' : 'Verify Your Email' ?></h1>
    <p><?= $fr ? 'Une étape rapide pour sécuriser votre compte.' : 'One quick step to secure your account.' ?></p>
</div>

<main class="page">
<div class="verify-page">

    <div class="verify-card">

        <div class="lock-icon"><i class="fas fa-lock"></i></div>

        <h2><?= $fr ? 'Entrez votre code' : 'Enter your code' ?></h2>
        <p class="sub">
            <?= $fr
                ? 'Nous avons envoyé un code à 6 chiffres à <strong>' . htmlspecialchars($maskedEmail) . '</strong>. Il expire dans 30 minutes.'
                : 'We sent a 6-digit code to <strong>' . htmlspecialchars($maskedEmail) . '</strong>. It expires in 30 minutes.'
            ?>
        </p>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['verify_success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['verify_success']) ?>
            </div>
            <?php unset($_SESSION['verify_success']); ?>
        <?php endif; ?>

        <form method="POST" action="<?= url('distribution/verify-email') ?>" id="verifyForm">
            <?= csrfField() ?>
            <input type="hidden" name="code" id="otpHidden">

            <div class="otp-group" id="otpGroup">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text"
                           class="otp-box <?= !empty($errors['general']) ? 'error' : '' ?>"
                           maxlength="1"
                           inputmode="numeric"
                           pattern="[0-9]"
                           autocomplete="<?= $i === 0 ? 'one-time-code' : 'off' ?>"
                           data-index="<?= $i ?>">
                <?php endfor; ?>
            </div>

            <?php if ($attempts > 0 && $remaining > 0): ?>
                <p class="attempts-bar <?= $remaining <= 2 ? 'low' : '' ?>">
                    <?= $fr
                        ? "Tentatives restantes : {$remaining}"
                        : "Attempts remaining: {$remaining}"
                    ?>
                </p>
            <?php endif; ?>

            <button type="submit" class="btn-verify" id="verifyBtn" disabled>
                <span class="spinner"></span>
                <span class="btn-label">
                    <i class="fas fa-check"></i>
                    <?= $fr ? 'Confirmer le code' : 'Confirm Code' ?>
                </span>
            </button>
        </form>

        <div class="resend-form">
            <span class="resend-note">
                <?= $fr ? 'Code non reçu ?' : "Didn't receive it?" ?>
            </span>
            <form method="POST" action="<?= url('distribution/resend-verification') ?>" style="display:inline;">
                <?= csrfField() ?>
                <button type="submit" class="btn-resend">
                    <?= $fr ? 'Renvoyer' : 'Resend code' ?>
                </button>
            </form>
        </div>

    </div>

</div>
</main>

<?php include __DIR__ . '/../components/footer.php'; ?>

<script>
const boxes = document.querySelectorAll('.otp-box');
const hidden = document.getElementById('otpHidden');
const submitBtn = document.getElementById('verifyBtn');

function syncHidden() {
    const val = Array.from(boxes).map(b => b.value).join('');
    hidden.value = val;
    const complete = val.length === 6 && /^\d{6}$/.test(val);
    submitBtn.disabled = !complete;
    boxes.forEach((b, i) => {
        b.classList.toggle('filled', b.value.length === 1);
        b.classList.remove('error');
    });
}

boxes.forEach((box, i) => {
    box.addEventListener('input', function () {
        // Strip non-digits and keep only first char
        this.value = this.value.replace(/\D/g, '').slice(0, 1);
        syncHidden();
        if (this.value && i < 5) boxes[i + 1].focus();
    });

    box.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && !this.value && i > 0) {
            boxes[i - 1].value = '';
            boxes[i - 1].focus();
            syncHidden();
        }
        if (e.key === 'ArrowLeft' && i > 0) boxes[i - 1].focus();
        if (e.key === 'ArrowRight' && i < 5) boxes[i + 1].focus();
    });

    box.addEventListener('paste', function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
        pasted.split('').forEach((ch, j) => {
            if (boxes[j]) boxes[j].value = ch;
        });
        syncHidden();
        const next = Math.min(pasted.length, 5);
        boxes[next].focus();
    });
});

document.getElementById('verifyForm').addEventListener('submit', function () {
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
});

// Pre-fill code from magic link fallback (?code=XXXXXX) and auto-submit
const urlCode = new URLSearchParams(window.location.search).get('code');
if (urlCode && /^\d{6}$/.test(urlCode)) {
    urlCode.split('').forEach((ch, i) => { if (boxes[i]) boxes[i].value = ch; });
    syncHidden();
    submitBtn.disabled = false;
    submitBtn.querySelector('.btn-label').innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= $fr ? "Vérification…" : "Verifying…" ?>';
    setTimeout(() => document.getElementById('verifyForm').submit(), 800);
} else {
    boxes[0]?.focus();
}
</script>
</body>
</html>
