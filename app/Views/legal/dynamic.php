<?php
/**
 * Dynamic Legal Page View
 * Displays legal content from database
 */
$currentLang = $_SESSION['language'] ?? 'fr';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['title'] ?? 'Legal Page') ?> - OCSAPP</title>
    <?php if (!empty($page['meta_description'])): ?>
        <meta name="description" content="<?= htmlspecialchars($page['meta_description']) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">

<style>
    :root {
        --primary: #00b207;
        --dark: #1a1a1a;
        --gray-600: #4b5563;
        --gray-100: #f3f4f6;
        --border: #e5e7eb;
    }

    .legal-page {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .legal-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 48px 64px;
    }

    .legal-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 24px;
        border-bottom: 2px solid var(--border);
    }

    .legal-header h1 {
        font-size: 36px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 12px;
    }

    .legal-meta {
        display: flex;
        justify-content: center;
        gap: 24px;
        font-size: 14px;
        color: var(--gray-600);
        flex-wrap: wrap;
    }

    .legal-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .legal-meta-item i {
        color: var(--primary);
    }

    .legal-content {
        font-size: 15px;
        line-height: 1.8;
        color: #374151;
    }

    .legal-content h1,
    .legal-content h2,
    .legal-content h3,
    .legal-content h4,
    .legal-content h5,
    .legal-content h6 {
        margin-top: 32px;
        margin-bottom: 16px;
        font-weight: 600;
        color: var(--dark);
    }

    .legal-content h2 {
        font-size: 24px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--border);
    }

    .legal-content h3 {
        font-size: 20px;
    }

    .legal-content h4 {
        font-size: 18px;
    }

    .legal-content p {
        margin-bottom: 16px;
    }

    .legal-content ul,
    .legal-content ol {
        margin-bottom: 16px;
        padding-left: 28px;
    }

    .legal-content li {
        margin-bottom: 8px;
    }

    .legal-content a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }

    .legal-content a:hover {
        text-decoration: underline;
    }

    .legal-content strong {
        font-weight: 600;
        color: var(--dark);
    }

    .legal-content em {
        font-style: italic;
    }

    .legal-content blockquote {
        margin: 24px 0;
        padding: 16px 24px;
        background: var(--gray-100);
        border-left: 4px solid var(--primary);
        font-style: italic;
    }

    .legal-content table {
        width: 100%;
        margin: 24px 0;
        border-collapse: collapse;
    }

    .legal-content table th,
    .legal-content table td {
        padding: 12px;
        border: 1px solid var(--border);
        text-align: left;
    }

    .legal-content table th {
        background: var(--gray-100);
        font-weight: 600;
    }

    .legal-footer {
        margin-top: 48px;
        padding-top: 24px;
        border-top: 2px solid var(--border);
        text-align: center;
        color: var(--gray-600);
        font-size: 14px;
    }

    .back-to-top {
        margin-top: 24px;
    }

    .back-to-top a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--primary);
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .back-to-top a:hover {
        background: #009606;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .legal-container {
            padding: 32px 24px;
        }

        .legal-header h1 {
            font-size: 28px;
        }

        .legal-meta {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="page">
        <div class="legal-page">
            <div class="legal-container">
                <a href="<?= url('/') ?>" style="display: inline-block; margin-bottom: 20px; color: #00b207; text-decoration: none; font-weight: 600;">← Back to Home</a>

                <!-- Header -->
                <div class="legal-header">
                    <h1><?= htmlspecialchars($page['title']) ?></h1>
                    <div class="legal-meta">
                        <?php if (!empty($page['version'])): ?>
                            <div class="legal-meta-item">
                                <i class="fas fa-code-branch"></i>
                                <span>Version <?= $page['version'] ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($page['updated_at'])): ?>
                            <div class="legal-meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>Last Updated: <?= formatDate($page['updated_at'], 'F d, Y') ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($page['language'])): ?>
                            <div class="legal-meta-item">
                                <i class="fas fa-language"></i>
                                <span><?= strtoupper($page['language']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Content -->
                <div class="legal-content">
                    <?= $page['content'] ?>
                </div>

                <!-- Footer -->
                <div class="legal-footer">
                    <p>
                        <i class="fas fa-shield-alt"></i>
                        This document is legally binding and effective as of <?= formatDate($page['updated_at'] ?? date('Y-m-d'), 'F d, Y') ?>
                    </p>
                    <p style="margin-top: 8px;">
                        &copy; <?= date('Y') ?> OCSAPP. All rights reserved.
                    </p>

                    <div class="back-to-top">
                        <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                            <i class="fas fa-arrow-up"></i> Back to Top
                        </a>
                    </div>
                </div>
            </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
