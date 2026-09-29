<?php
/**
 * Buyer account dashboard (/account)
 * Updated 2026-09-28: moved onto the mc-header/mc-footer ecosystem standard (same chrome as /cart,
 * /shops, /product) and made bilingual EN/FR (was hardcoded English). Sidebar, formatting helpers and
 * styles are shared with the account sub-pages: partials/account-nav.php, partials/account-helpers.php,
 * css/pages/account.css. Data from AccountController::index() is unchanged.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$user = $user ?? [];
$stats = $stats ?? ['total_orders'=>0,'pending_orders'=>0,'completed_orders'=>0,'total_spent'=>0,'store_credit_balance'=>0];
$recentOrders = $recentOrders ?? [];
$cartCount = $cartCount ?? 0;
$founding = $founding ?? ['referral_code' => '', 'founding_buyer' => 0, 'founding_buyer_number' => null];
$referralLink = !empty($founding['referral_code']) ? rtrim(env('APP_URL', 'https://ocsapp.ca'), '/') . '/register?ref=' . $founding['referral_code'] : '';
$accountActive = 'dashboard';
require __DIR__ . '/partials/account-helpers.php';

$firstName = trim((string) ($user['first_name'] ?? ''));
$statCards = [
    ['fa-receipt',        (string) (int) $stats['total_orders'],     $fr ? 'Commandes' : 'Total orders'],
    ['fa-hourglass-half', (string) (int) $stats['pending_orders'],   $fr ? 'En attente' : 'Pending'],
    ['fa-circle-check',   (string) (int) $stats['completed_orders'], $fr ? 'Terminées' : 'Completed'],
    ['fa-wallet',         acct_money($stats['total_spent'], $fr),    $fr ? 'Total dépensé' : 'Total spent'],
    ['fa-gift',           acct_money($stats['store_credit_balance'] ?? 0, $fr), $fr ? 'Crédit en magasin' : 'Store credit'],
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $fr ? 'Mon compte' : 'My account' ?> | OCSAPP</title>
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
</head>
<body>
    <?php $useMarcheHeader = true; ?>
    <?php include __DIR__ . '/../../components/header.php'; ?>

    <div class="mc-shell" id="main-content" tabindex="-1">
        <div class="mc-wrap">
            <nav class="mc-breadcrumb" aria-label="<?= $fr ? "Fil d'Ariane" : 'Breadcrumb' ?>">
                <a href="<?= url('marketplace-central') ?>"><i class="fas fa-store"></i><span><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></span></a>
                <span class="mc-sep">/</span>
                <span aria-current="page"><i class="fas fa-user"></i> <?= $fr ? 'Mon compte' : 'My account' ?></span>
            </nav>

            <div class="acct-head">
                <div>
                    <h1><?= $firstName !== '' ? ($fr ? 'Bonjour, ' : 'Hi, ') . htmlspecialchars($firstName) : ($fr ? 'Mon compte' : 'My account') ?></h1>
                    <p><?= $fr ? 'Suivez vos commandes et gérez vos adresses et vos préférences.' : 'Track your orders and manage your addresses and preferences.' ?></p>
                </div>
                <a href="<?= url('marketplace-central') ?>" class="acct-btn acct-btn-primary"><i class="fas fa-bag-shopping"></i> <?= $fr ? 'Magasiner' : 'Shop now' ?></a>
            </div>

            <div class="acct-layout">
                <?php require __DIR__ . '/partials/account-nav.php'; ?>

                <main class="acct-main">
                    <?php if ($flash = getFlash('success')): ?>
                        <div class="acct-flash acct-flash-ok" data-auto-dismiss><?= htmlspecialchars($flash) ?></div>
                    <?php endif; ?>
                    <?php if ($flash = getFlash('error')): ?>
                        <div class="acct-flash acct-flash-err"><?= htmlspecialchars($flash) ?></div>
                    <?php endif; ?>

                    <div class="acct-stats">
                        <?php foreach ($statCards as [$icon, $value, $label]): ?>
                            <div class="acct-card acct-stat">
                                <i class="fas <?= $icon ?>"></i>
                                <strong><?= htmlspecialchars($value) ?></strong>
                                <span><?= $label ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Founding Buyer Program + referrals (Buyer Terms Sec 12) -->
                    <section class="acct-card acct-refer<?= !empty($founding['founding_buyer']) ? ' is-founder' : '' ?>">
                        <?php if (!empty($founding['founding_buyer'])): ?>
                            <div class="acct-refer-badge"><i class="fa-solid fa-star"></i> <?= $fr ? 'Acheteur fondateur n° ' : 'Founding Buyer #' ?><?= (int) $founding['founding_buyer_number'] ?></div>
                            <p class="acct-muted"><?= $fr
                                ? 'Merci de faire partie de nos premiers acheteurs : les frais de livraison de votre première commande livrée vous ont été offerts.'
                                : "Thank you for being one of our first buyers: your first delivery Order's Delivery Fee was on us." ?></p>
                        <?php endif; ?>
                        <h2><?= $fr ? 'Parrainez un ami, recevez 5 $' : 'Refer a friend, earn $5' ?></h2>
                        <p class="acct-muted"><?= $fr
                            ? "Partagez votre lien. Lorsqu'un ami s'inscrit et que sa première commande est livrée, vous recevez chacun un crédit de 5 $ applicable à de futurs frais de livraison. Aucune limite au nombre d'amis parrainés."
                            : 'Share your link. When a friend signs up and their first Order is delivered, you both get a $5 credit toward a future Delivery Fee. No limit on how many friends you refer.' ?></p>
                        <div class="acct-copy">
                            <input type="text" readonly value="<?= htmlspecialchars($referralLink) ?>" id="referralLinkInput" aria-label="<?= $fr ? 'Votre lien de parrainage' : 'Your referral link' ?>">
                            <button type="button" class="acct-btn acct-btn-primary" id="referralCopyBtn"><i class="fas fa-copy"></i> <span><?= $fr ? 'Copier le lien' : 'Copy link' ?></span></button>
                        </div>
                    </section>

                    <section class="acct-card acct-orders">
                        <div class="acct-section-head">
                            <h2><?= $fr ? 'Commandes récentes' : 'Recent orders' ?></h2>
                            <a href="<?= url('account/orders') ?>"><?= $fr ? 'Tout voir' : 'View all' ?> <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <?php if (empty($recentOrders)): ?>
                            <div class="acct-empty">
                                <i class="fas fa-bag-shopping"></i>
                                <p><?= $fr ? "Vous n'avez pas encore de commande." : 'No orders yet.' ?></p>
                                <a href="<?= url('marketplace-central') ?>" class="acct-btn acct-btn-primary"><?= $fr ? 'Commencer à magasiner' : 'Start shopping' ?></a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <?php $items = (int) ($order['item_count'] ?? 0); ?>
                                <a class="acct-order" href="<?= url('account/orders/detail') ?>?id=<?= (int) $order['id'] ?>">
                                    <div class="acct-order-main">
                                        <strong><?= $fr ? 'Commande ' : 'Order ' ?>#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></strong>
                                        <span><?= acct_date($order['created_at'], $fr) ?> · <?= $items ?> <?= $fr ? ($items > 1 ? 'articles' : 'article') : ($items === 1 ? 'item' : 'items') ?></span>
                                    </div>
                                    <span class="acct-badge acct-badge-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(acct_status((string) $order['status'], $fr)) ?></span>
                                    <span class="acct-order-total"><?= acct_money($order['total'], $fr) ?></span>
                                    <i class="fas fa-chevron-right acct-order-go"></i>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </section>
                </main>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../../pages/partials/eco-page-footer.php'; ?>

    <script>
    // Success flashes fade out (the old components/footer.php did this; the mc-footer partial does not)
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
        setTimeout(function () {
            el.style.opacity = '0';
            setTimeout(function () { el.style.display = 'none'; }, 600);
        }, 4000);
    });
    (function () {
        var btn = document.getElementById('referralCopyBtn');
        if (!btn) return;
        var label = btn.querySelector('span');
        var copied = <?= json_encode($fr ? 'Lien copié !' : 'Copied!') ?>, orig = label.textContent;
        btn.addEventListener('click', function () {
            var input = document.getElementById('referralLinkInput');
            var done = function () { label.textContent = copied; setTimeout(function () { label.textContent = orig; }, 1500); };
            if (navigator.clipboard) { navigator.clipboard.writeText(input.value).then(done); }
            else { input.select(); document.execCommand('copy'); done(); }
        });
    })();
    </script>
</body>
</html>
