<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generateCsrfToken() ?>">
    <title><?= $fr ? 'Créer votre mot de passe - OCSAPP' : 'Create your password - OCSAPP' ?></title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e9 50%, #c8e6c9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            padding: 48px 40px;
            width: 100%;
            max-width: 440px;
        }
        .logo { text-align: center; margin-bottom: 28px; }
        .logo img { width: 52px; height: 52px; border-radius: 50%; border: 3px solid #00b207; }
        .logo-name { font-size: 26px; font-weight: 800; color: #00b207; margin-top: 8px; }
        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #d1fae5; color: #065f46;
            padding: 6px 14px; border-radius: 999px;
            font-size: 12px; font-weight: 600; margin-top: 8px;
            border: 1px solid #6ee7b7;
        }
        h1 { font-size: 22px; font-weight: 700; color: #1a1a1a; text-align: center; margin: 20px 0 6px; }
        .subtitle { font-size: 13px; color: #6b7280; text-align: center; margin-bottom: 28px; line-height: 1.5; }
        .flash {
            padding: 12px 16px; border-radius: 8px; font-size: 14px;
            margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
        }
        .flash.error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .flash.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .input-wrap { position: relative; }
        .input-wrap input {
            width: 100%; padding: 12px 42px 12px 16px;
            border: 2px solid #e5e7eb; border-radius: 10px;
            font-size: 14px; font-family: inherit;
            transition: border-color .2s;
        }
        .input-wrap input:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,.1); }
        .toggle-btn {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: #9ca3af; padding: 4px;
        }
        .toggle-btn:hover { color: #6b7280; }
        .rules { margin-top: 8px; }
        .rule {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: #9ca3af; margin-bottom: 3px;
            transition: color .2s;
        }
        .rule.ok { color: #00b207; }
        .rule i { width: 14px; }
        .btn {
            display: block; width: 100%; padding: 13px;
            background: #00b207; color: #fff; border: none; border-radius: 10px;
            font-size: 15px; font-weight: 700; font-family: inherit;
            cursor: pointer; margin-top: 8px; transition: background .2s;
        }
        .btn:hover { background: #009206; }
        .btn:disabled { background: #9ca3af; cursor: not-allowed; }
        @media (max-width: 480px) {
            .card { padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <img src="<?= asset('images/logo.png') ?>" alt="OCSAPP">
            <div class="logo-name">OCSAPP</div>
            <div class="badge"><i class="fas fa-truck"></i> <?= $fr ? 'Espace Livreur' : 'Driver Central' ?></div>
        </div>

        <h1><?= $fr ? 'Créez votre mot de passe' : 'Create your password' ?></h1>
        <p class="subtitle">
            <?= $fr
                ? 'Pour des raisons de sécurité, veuillez créer un nouveau mot de passe personnel avant d\'accéder à votre tableau de bord.'
                : 'For security, please set a personal password before accessing your dashboard.' ?>
        </p>

        <?php if ($flash = getFlash('error')): ?>
            <div class="flash error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = getFlash('success')): ?>
            <div class="flash success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('delivery/change-password') ?>" id="cpForm">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="new_password"><?= $fr ? 'Nouveau mot de passe' : 'New password' ?></label>
                <div class="input-wrap">
                    <input type="password" id="new_password" name="new_password"
                           autocomplete="new-password" required autofocus
                           oninput="checkRules(this.value)">
                    <button type="button" class="toggle-btn" onclick="togglePw('new_password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="rules" id="rules">
                    <div class="rule" id="r-len"><i class="fas fa-circle"></i> <?= $fr ? '10 caractères minimum' : 'At least 10 characters' ?></div>
                    <div class="rule" id="r-up"><i class="fas fa-circle"></i> <?= $fr ? 'Une lettre majuscule' : 'One uppercase letter' ?></div>
                    <div class="rule" id="r-lo"><i class="fas fa-circle"></i> <?= $fr ? 'Une lettre minuscule' : 'One lowercase letter' ?></div>
                    <div class="rule" id="r-num"><i class="fas fa-circle"></i> <?= $fr ? 'Un chiffre' : 'One number' ?></div>
                    <div class="rule" id="r-sp"><i class="fas fa-circle"></i> <?= $fr ? 'Un caractère spécial (!@#$%^&*)' : 'One special character (!@#$%^&*)' ?></div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password"><?= $fr ? 'Confirmer le mot de passe' : 'Confirm password' ?></label>
                <div class="input-wrap">
                    <input type="password" id="confirm_password" name="confirm_password"
                           autocomplete="new-password" required>
                    <button type="button" class="toggle-btn" onclick="togglePw('confirm_password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn" id="submitBtn" disabled>
                <?= $fr ? 'Enregistrer et accéder au tableau de bord' : 'Save and go to dashboard' ?>
            </button>
        </form>
    </div>

    <script>
        const rules = {
            len: v => v.length >= 10,
            up:  v => /[A-Z]/.test(v),
            lo:  v => /[a-z]/.test(v),
            num: v => /[0-9]/.test(v),
            sp:  v => /[!@#$%^&*]/.test(v),
        };

        function checkRules(val) {
            let allOk = true;
            for (const [key, fn] of Object.entries(rules)) {
                const el = document.getElementById('r-' + key);
                const ok = fn(val);
                el.classList.toggle('ok', ok);
                el.querySelector('i').className = 'fas ' + (ok ? 'fa-check-circle' : 'fa-circle');
                if (!ok) allOk = false;
            }
            document.getElementById('submitBtn').disabled = !allOk;
        }

        function togglePw(id, btn) {
            const f = document.getElementById(id);
            const i = btn.querySelector('i');
            if (f.type === 'password') {
                f.type = 'text';
                i.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                f.type = 'password';
                i.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
