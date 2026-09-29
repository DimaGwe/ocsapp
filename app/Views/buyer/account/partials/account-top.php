<?php
/**
 * Opening shell for the buyer account pages: <head>, mc-header, breadcrumb, page heading, sidebar,
 * flash messages, and the opening <main class="acct-main">. Close with partials/account-bottom.php,
 * then the page's own scripts and </body></html>.
 * Needs: $fr, $currentLang, $user, $accountActive, $acctTitle (browser title), $acctHeading.
 * Optional: $acctCrumb ([label, icon] for the current page; the "Mon compte" crumb links back when
 * this is a sub-page), $acctSub (subheading), $acctAction (HTML for the button on the right),
 * $founding (founder badge in the sidebar), $acctHeadExtra (extra <head> HTML).
 */
require __DIR__ . '/account-helpers.php';
$acctIsDashboard = ($accountActive ?? '') === 'dashboard';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($acctTitle) ?> | OCSAPP</title>
    <meta name="robots" content="noindex">
    <?= csrfMeta() ?>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/pages/account.css') ?>">
    <?= $acctHeadExtra ?? '' ?>
</head>
<body>
    <?php $useMarcheHeader = true; ?>
    <?php include __DIR__ . '/../../../components/header.php'; ?>

    <div class="mc-shell" id="main-content" tabindex="-1">
        <div class="mc-wrap">
            <nav class="mc-breadcrumb" aria-label="<?= $fr ? "Fil d'Ariane" : 'Breadcrumb' ?>">
                <a href="<?= url('marketplace-central') ?>"><i class="fas fa-store"></i><span><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></span></a>
                <span class="mc-sep">/</span>
                <?php if ($acctIsDashboard): ?>
                    <span aria-current="page"><i class="fas fa-user"></i> <?= $fr ? 'Mon compte' : 'My account' ?></span>
                <?php else: ?>
                    <a href="<?= url('account') ?>"><i class="fas fa-user"></i><span><?= $fr ? 'Mon compte' : 'My account' ?></span></a>
                    <span class="mc-sep">/</span>
                    <span aria-current="page"><i class="fas <?= $acctCrumb[1] ?? 'fa-circle' ?>"></i> <?= htmlspecialchars($acctCrumb[0] ?? $acctHeading) ?></span>
                <?php endif; ?>
            </nav>

            <div class="acct-head">
                <div>
                    <h1><?= htmlspecialchars($acctHeading) ?></h1>
                    <?php if (!empty($acctSub)): ?><p><?= $acctSub ?></p><?php endif; ?>
                </div>
                <?= $acctAction ?? '' ?>
            </div>

            <div class="acct-layout">
                <?php require __DIR__ . '/account-nav.php'; ?>

                <main class="acct-main">
                    <?php if ($flash = getFlash('success')): ?>
                        <div class="acct-flash acct-flash-ok" data-auto-dismiss><?= htmlspecialchars($flash) ?></div>
                    <?php endif; ?>
                    <?php if ($flash = getFlash('error')): ?>
                        <div class="acct-flash acct-flash-err"><?= htmlspecialchars($flash) ?></div>
                    <?php endif; ?>
