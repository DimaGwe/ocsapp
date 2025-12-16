<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['title'] ?? 'Preview') ?> - Preview</title>
    <link rel="stylesheet" href="<?= url('assets/css/styles.css') ?>">
    <style>
        :root {
            --primary: #00b207;
            --dark: #1a1a1a;
            --gray-600: #4b5563;
            --gray-100: #f3f4f6;
            --border: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f9fafb;
        }

        .preview-banner {
            background: #fef3c7;
            border-bottom: 2px solid #f59e0b;
            padding: 12px 20px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .preview-banner-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }

        .preview-text {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #92400e;
        }

        .preview-text i {
            font-size: 18px;
        }

        .preview-actions {
            display: flex;
            gap: 12px;
        }

        .preview-btn {
            padding: 6px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .preview-btn.edit {
            background: var(--primary);
            color: white;
        }

        .preview-btn.edit:hover {
            background: #009606;
        }

        .preview-btn.close {
            background: #ef4444;
            color: white;
        }

        .preview-btn.close:hover {
            background: #dc2626;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .legal-content {
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
        }

        .legal-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .legal-meta-item i {
            color: var(--primary);
        }

        .legal-body {
            font-size: 15px;
            line-height: 1.8;
            color: #374151;
        }

        .legal-body h1,
        .legal-body h2,
        .legal-body h3,
        .legal-body h4,
        .legal-body h5,
        .legal-body h6 {
            margin-top: 32px;
            margin-bottom: 16px;
            font-weight: 600;
            color: var(--dark);
        }

        .legal-body h2 {
            font-size: 24px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border);
        }

        .legal-body h3 {
            font-size: 20px;
        }

        .legal-body h4 {
            font-size: 18px;
        }

        .legal-body p {
            margin-bottom: 16px;
        }

        .legal-body ul,
        .legal-body ol {
            margin-bottom: 16px;
            padding-left: 28px;
        }

        .legal-body li {
            margin-bottom: 8px;
        }

        .legal-body a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .legal-body a:hover {
            text-decoration: underline;
        }

        .legal-body strong {
            font-weight: 600;
            color: var(--dark);
        }

        .legal-body em {
            font-style: italic;
        }

        .legal-body blockquote {
            margin: 24px 0;
            padding: 16px 24px;
            background: var(--gray-100);
            border-left: 4px solid var(--primary);
            font-style: italic;
        }

        .legal-body table {
            width: 100%;
            margin: 24px 0;
            border-collapse: collapse;
        }

        .legal-body table th,
        .legal-body table td {
            padding: 12px;
            border: 1px solid var(--border);
            text-align: left;
        }

        .legal-body table th {
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

        @media (max-width: 768px) {
            .legal-content {
                padding: 32px 24px;
            }

            .legal-header h1 {
                font-size: 28px;
            }

            .legal-meta {
                flex-direction: column;
                gap: 8px;
            }

            .preview-banner-content {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Preview Banner -->
    <div class="preview-banner">
        <div class="preview-banner-content">
            <div class="preview-text">
                <i class="fas fa-eye"></i>
                <span>PREVIEW MODE - This is how the page will appear to visitors</span>
            </div>
            <div class="preview-actions">
                <a href="<?= url('admin/legal/edit?id=' . $page['id']) ?>" class="preview-btn edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button onclick="window.close()" class="preview-btn close">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>

    <!-- Legal Content -->
    <div class="container">
        <div class="legal-content">
            <!-- Header -->
            <div class="legal-header">
                <h1><?= htmlspecialchars($page['title']) ?></h1>
                <div class="legal-meta">
                    <div class="legal-meta-item">
                        <i class="fas fa-code-branch"></i>
                        <span>Version <?= $page['version'] ?></span>
                    </div>
                    <div class="legal-meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Last Updated: <?= formatDate($page['updated_at'], 'F d, Y') ?></span>
                    </div>
                    <div class="legal-meta-item">
                        <i class="fas fa-language"></i>
                        <span><?= strtoupper($page['language']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Body Content -->
            <div class="legal-body">
                <?= $page['content'] ?>
            </div>

            <!-- Footer -->
            <div class="legal-footer">
                <p>
                    <i class="fas fa-shield-alt"></i>
                    This document is legally binding and effective as of <?= formatDate($page['updated_at'], 'F d, Y') ?>
                </p>
                <p style="margin-top: 8px;">
                    &copy; <?= date('Y') ?> OCS Marketplace. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
