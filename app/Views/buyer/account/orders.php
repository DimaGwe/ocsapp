<?php
/**
 * Buyer order history (/account/orders, OrderController::myOrders)
 * Updated 2026-09-28: Marché Central theme via the shared account partials, bilingual EN/FR (was
 * English). Status tabs now use real orders.status values ("completed" never existed, so that tab
 * was always empty). Query and pagination logic unchanged.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$orders = $orders ?? [];
$currentPage = (int) ($currentPage ?? 1);
$totalPages = (int) ($totalPages ?? 1);
$totalOrders = (int) ($totalOrders ?? 0);
$status = $status ?? '';
$search = $search ?? '';
$user = user() ?? [];
$accountActive = 'orders';

$tabs = [
    ''                 => $fr ? 'Toutes' : 'All',
    'pending'          => $fr ? 'En attente' : 'Pending',
    'confirmed'        => $fr ? 'Confirmées' : 'Confirmed',
    'processing'       => $fr ? 'En préparation' : 'Processing',
    'out_for_delivery' => $fr ? 'En livraison' : 'Out for delivery',
    'delivered'        => $fr ? 'Livrées' : 'Delivered',
    'cancelled'        => $fr ? 'Annulées' : 'Cancelled',
];
$qs = function (array $params) use ($status, $search): string {
    $params += ['status' => $status, 'search' => $search];
    $params = array_filter($params, fn($v) => $v !== '' && $v !== null);
    return $params ? '?' . http_build_query($params) : '';
};

$acctTitle   = $fr ? 'Mes commandes' : 'My orders';
$acctHeading = $acctTitle;
$acctCrumb   = [$acctTitle, 'fa-box'];
$acctSub     = $fr
    ? $totalOrders . ' ' . ($totalOrders > 1 ? 'commandes au total' : 'commande au total')
    : $totalOrders . ' ' . ($totalOrders === 1 ? 'order in total' : 'orders in total');
require __DIR__ . '/partials/account-top.php';
?>
                    <section class="acct-card acct-panel">
                        <div class="acct-tabs" role="tablist" aria-label="<?= $fr ? 'Filtrer par statut' : 'Filter by status' ?>">
                            <?php foreach ($tabs as $value => $label): ?>
                                <a href="<?= url('account/orders') . $qs(['status' => $value, 'page' => null]) ?>" class="<?= $status === $value ? 'active' : '' ?>"<?= $status === $value ? ' aria-current="true"' : '' ?>><?= $label ?></a>
                            <?php endforeach; ?>
                        </div>

                        <form class="acct-search" method="GET" action="<?= url('account/orders') ?>" role="search">
                            <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>"><?php endif; ?>
                            <i class="fas fa-magnifying-glass"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="<?= $fr ? 'Rechercher par n° de commande ou commerce' : 'Search by order # or shop name' ?>" aria-label="<?= $fr ? 'Rechercher une commande' : 'Search orders' ?>">
                            <button type="submit" class="acct-btn acct-btn-primary"><?= $fr ? 'Rechercher' : 'Search' ?></button>
                            <?php if ($search !== ''): ?><a class="acct-search-clear" href="<?= url('account/orders') . $qs(['search' => null, 'page' => null]) ?>"><?= $fr ? 'Effacer' : 'Clear' ?></a><?php endif; ?>
                        </form>

                        <?php if (empty($orders)): ?>
                            <div class="acct-empty">
                                <i class="fas fa-bag-shopping"></i>
                                <p><?= ($status !== '' || $search !== '')
                                    ? ($fr ? 'Aucune commande ne correspond à ces critères.' : 'No orders match these filters.')
                                    : ($fr ? "Vous n'avez pas encore de commande. Vos commandes apparaîtront ici." : 'No orders yet. Your orders will appear here.') ?></p>
                                <a href="<?= url('marketplace-central') ?>" class="acct-btn acct-btn-primary"><?= $fr ? 'Commencer à magasiner' : 'Start shopping' ?></a>
                            </div>
                        <?php else: ?>
                            <div class="acct-order-list">
                                <?php foreach ($orders as $order): ?>
                                    <?php $items = (int) ($order['items_count'] ?? 0); ?>
                                    <a class="acct-order" href="<?= url('account/orders/detail') ?>?id=<?= (int) $order['id'] ?>">
                                        <div class="acct-order-main">
                                            <strong><?= $fr ? 'Commande ' : 'Order ' ?>#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></strong>
                                            <span><?= htmlspecialchars($order['shop_name'] ?? ($fr ? 'Commerce' : 'Shop')) ?> · <?= acct_date($order['created_at'], $fr) ?> · <?= $items ?> <?= $fr ? ($items > 1 ? 'articles' : 'article') : ($items === 1 ? 'item' : 'items') ?></span>
                                        </div>
                                        <span class="acct-badge acct-badge-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(acct_status((string) $order['status'], $fr)) ?></span>
                                        <span class="acct-order-total"><?= acct_money($order['total'], $fr) ?></span>
                                        <i class="fas fa-chevron-right acct-order-go"></i>
                                    </a>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($totalPages > 1): ?>
                                <nav class="acct-pager" aria-label="<?= $fr ? 'Pagination' : 'Pagination' ?>">
                                    <?php if ($currentPage > 1): ?>
                                        <a href="<?= $qs(['page' => $currentPage - 1]) ?>" aria-label="<?= $fr ? 'Page précédente' : 'Previous page' ?>"><i class="fas fa-chevron-left"></i></a>
                                    <?php endif; ?>
                                    <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
                                        <?php if ($p === $currentPage): ?>
                                            <span aria-current="page"><?= $p ?></span>
                                        <?php else: ?>
                                            <a href="<?= $qs(['page' => $p]) ?>"><?= $p ?></a>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <?php if ($currentPage < $totalPages): ?>
                                        <a href="<?= $qs(['page' => $currentPage + 1]) ?>" aria-label="<?= $fr ? 'Page suivante' : 'Next page' ?>"><i class="fas fa-chevron-right"></i></a>
                                    <?php endif; ?>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </section>
<?php require __DIR__ . '/partials/account-bottom.php'; ?>
</body>
</html>
