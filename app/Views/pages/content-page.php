<?php
/**
 * Generic Content Page View
 * Displays About Us, Contact Us, and other CMS pages
 */
$currentLang = $_SESSION['language'] ?? 'fr';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['title']) ?> - OCS Marketplace</title>
    <?php if (!empty($page['meta_description'])): ?>
        <meta name="description" content="<?= htmlspecialchars($page['meta_description']) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .content-page {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .content-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 48px 64px;
        }

        .content-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 24px;
            border-bottom: 2px solid var(--border, #e5e7eb);
        }

        .content-header h1 {
            font-size: 36px;
            font-weight: 700;
            color: var(--dark, #1a1a1a);
            margin-bottom: 12px;
        }

        .content-body {
            font-size: 15px;
            line-height: 1.8;
            color: #374151;
        }

        .content-body h1,
        .content-body h2,
        .content-body h3,
        .content-body h4 {
            margin-top: 32px;
            margin-bottom: 16px;
            font-weight: 600;
            color: var(--dark, #1a1a1a);
        }

        .content-body h2 {
            font-size: 24px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border, #e5e7eb);
        }

        .content-body h3 {
            font-size: 20px;
        }

        .content-body p {
            margin-bottom: 16px;
        }

        .content-body ul,
        .content-body ol {
            margin-bottom: 16px;
            padding-left: 28px;
        }

        .content-body li {
            margin-bottom: 8px;
        }

        .content-body a {
            color: var(--primary, #00b207);
            text-decoration: none;
            font-weight: 500;
        }

        .content-body a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary, #00b207);
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .content-container {
                padding: 32px 24px;
            }

            .content-header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="page">
        <div class="content-page">
            <div class="content-container">
                <a href="<?= url('/') ?>" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>

                <!-- Header -->
                <div class="content-header">
                    <h1><?= htmlspecialchars($page['title']) ?></h1>
                </div>

                <!-- Content -->
                <div class="content-body">
                    <?= $page['content'] ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
