<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Login - OCSAPP</title>
    <?= csrfMeta() ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
            padding: 50px 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-size: 32px;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #6b7280;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #00b207;
            box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember-me input {
            width: auto;
            margin-right: 8px;
        }

        .remember-me label {
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 178, 7, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .flash-message {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #dc2626;
        }

        .flash-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .login-footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }

        .login-footer a {
            color: #00b207;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .login-footer a:hover {
            color: #009206;
        }

        @media (max-width: 500px) {
            .login-container {
                padding: 30px 25px;
            }

            .login-header h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🏢 Vendor Login</h1>
            <p>Access your vendor dashboard</p>
        </div>

        <?php if (hasFlash('error')): ?>
            <div class="flash-message flash-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= getFlash('error') ?></span>
            </div>
        <?php endif; ?>

        <?php if (hasFlash('success')): ?>
            <div class="flash-message flash-success">
                <i class="fas fa-check-circle"></i>
                <span><?= getFlash('success') ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= url('vendor/login') ?>" method="POST">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="vendor@example.com"
                    required
                    autocomplete="email"
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Remember me for 30 days</label>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-sign-in-alt"></i> Login to Dashboard
            </button>
        </form>

        <div class="login-footer">
            <p><a href="<?= url('vendor-central') ?>">← Back to Vendor Central</a></p>
            <p style="margin-top: 10px;">
                <small>Need help? <a href="mailto:vendors@ocsapp.ca">vendors@ocsapp.ca</a></small>
            </p>
        </div>
    </div>
</body>
</html>
