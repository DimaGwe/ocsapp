<?php
/**
 * Seller orders (/seller/orders, OrderController::sellerOrders)
 * Updated 2026-09-28: seller-portal.css kit, bilingual EN/FR, fr-CA money/dates. Fixes: tabs used a
 * nonexistent "completed" status; the status dropdown offered moves the server rejects and had no
 * way to mark a ready pickup order collected (actions now come from sellerNextStatuses()); "today"
 * revenue now = paid product sales like the dashboard; each order opens the new detail page.
 */
$orders = $orders ?? [];
$currentPage = (int) ($currentPage ?? 1);
$totalPages = (int) ($totalPages ?? 1);
$totalOrders = (int) ($totalOrders ?? 0);
$todayStats = $todayStats ?? [];
$status = $status ?? '';
$date = $date ?? '';
$pageTitle = 'Orders';
require __DIR__ . '/../layout-header.php';

$byStatus = [];
foreach ($todayStats as $row) { $byStatus[$row['status']] = $row; }
$cnt = fn(array $ss) => array_sum(array_map(fn($s) => (int) ($byStatus[$s]['count'] ?? 0), $ss));
$todayCards = [
    ['fa-hourglass-half', (string) $cnt(['pending']), $fr ? "En attente aujourd'hui" : 'Pending today', $cnt(['pending']) > 0 ? 'is-warn' : ''],
    ['fa-person-digging', (string) $cnt(['confirmed', 'processing', 'ready']), $fr ? 'En cours' : 'In progress', ''],
    ['fa-circle-check',   (string) $cnt(['delivered']), $fr ? 'Livrées ou ramassées' : 'Delivered or collected', ''],
    ['fa-sack-dollar',    acct_money(array_sum(array_map(fn($r) => (float) ($r['paid_sales'] ?? 0), $todayStats)), $fr), $fr ? "Ventes payées aujourd'hui" : 'Paid sales today', ''],
];
$tabs = [
    ''                 => $fr ? 'Toutes' : 'All',
    'pending'          => $fr ? 'En attente' : 'Pending',
    'confirmed'        => $fr ? 'Confirmées' : 'Confirmed',
    'processing'       => $fr ? 'En préparation' : 'Processing',
    'ready'            => $fr ? 'Prêtes' : 'Ready',
    'out_for_delivery' => $fr ? 'En livraison' : 'Out for delivery',
    'delivered'        => $fr ? 'Livrées' : 'Delivered',
    'cancelled'        => $fr ? 'Annulées' : 'Cancelled',
];
$actionLabels = [
    'confirmed'  => $fr ? 'Confirmer' : 'Confirm',
    'processing' => $fr ? 'Commencer la préparation' : 'Start preparing',
    'ready'      => $fr ? 'Marquer prête' : 'Mark ready',
    'delivered'  => $fr ? 'Marquer ramassée' : 'Mark collected',
    'cancelled'  => $fr ? 'Annuler la commande' : 'Cancel order',
];
$qs = function (array $p) use ($status, $date): string {
    $p += ['status' => $status, 'date' => $date];
    $p = array_filter($p, fn($v) => $v !== '' && $v !== null);
    return $p ? '?' . http_build_query($p) : '';
};
?>
<div class="sp-stack">
  <div class="sp-stats">
    <?php foreach ($todayCards as [$icon, $value, $label, $cls]): ?>
      <div class="sp-stat <?= $cls ?>"><i class="fa-solid <?= $icon ?>"></i><strong><?= htmlspecialchars($value) ?></strong><span><?= $label ?></span></div>
    <?php endforeach; ?>
  </div>

  <section class="sp-card">
    <div class="sp-card-head">
      <h2><?= $fr ? 'Commandes' : 'Orders' ?> <span class="sp-dim">(<?= $totalOrders ?>)</span></h2>
      <form method="GET" action="<?= url('seller/orders') ?>" class="sp-row">
        <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>"><?php endif; ?>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="sp-input" style="width:auto" aria-label="<?= $fr ? 'Date' : 'Date' ?>">
        <button type="submit" class="sp-btn sp-btn-ghost sp-btn-sm"><i class="fa-solid fa-filter"></i> <?= $fr ? 'Filtrer' : 'Filter' ?></button>
        <?php if ($date !== ''): ?><a class="sp-dim" href="<?= url('seller/orders') . $qs(['date' => null]) ?>"><?= $fr ? 'Effacer' : 'Clear' ?></a><?php endif; ?>
      </form>
    </div>
    <div class="sp-tabs" style="margin-bottom:14px">
      <?php foreach ($tabs as $v => $label): ?>
        <a href="<?= url('seller/orders') . $qs(['status' => $v, 'page' => null]) ?>" class="<?= $status === $v ? 'active' : '' ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($orders)): ?>
      <div class="sp-empty"><i class="fa-solid fa-inbox"></i>
        <p><?= ($status !== '' || $date !== '') ? ($fr ? 'Aucune commande ne correspond à ces filtres.' : 'No orders match these filters.') : ($fr ? 'Les commandes de vos clients apparaîtront ici.' : 'Orders from your customers will appear here.') ?></p></div>
    <?php else: ?>
      <div class="sp-table-wrap">
        <table class="sp-table">
          <thead><tr>
            <th><?= $fr ? 'Commande' : 'Order' ?></th><th><?= $fr ? 'Client' : 'Customer' ?></th><th><?= $fr ? 'Articles' : 'Items' ?></th>
            <th class="num"><?= $fr ? 'Total' : 'Total' ?></th><th><?= $fr ? 'Statut' : 'Status' ?></th><th><?= $fr ? 'Date' : 'Date' ?></th><th><?= $fr ? 'Action' : 'Action' ?></th>
          </tr></thead>
          <tbody>
          <?php foreach ($orders as $order): ?>
            <?php
            $ft = (string) ($order['fulfillment_type'] ?? 'delivery');
            $next = \App\Controllers\OrderController::sellerNextStatuses((string) $order['status'], $ft);
            $items = (int) ($order['items_count'] ?? 0);
            $cust = trim(html_entity_decode(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            ?>
            <tr>
              <td><a class="sp-strong" href="<?= url('seller/orders/detail') ?>?id=<?= (int) $order['id'] ?>" style="text-decoration:none">#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></a>
                <div class="sp-dim"><?= $ft === 'pickup' ? '<i class="fa-solid fa-store"></i> ' . ($fr ? 'Ramassage' : 'Pickup') : '<i class="fa-solid fa-truck"></i> ' . ($fr ? 'Livraison' : 'Delivery') ?></div></td>
              <td><?= htmlspecialchars($cust) ?></td>
              <td><?= $items ?> <?= $fr ? ($items > 1 ? 'articles' : 'article') : ($items === 1 ? 'item' : 'items') ?></td>
              <td class="num sp-strong"><?= acct_money($order['total'], $fr) ?></td>
              <td><span class="sp-badge sp-badge-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(acct_status((string) $order['status'], $fr)) ?></span>
                <?php if (($order['payment_status'] ?? '') === 'pending'): ?><div class="sp-dim" style="margin-top:4px"><?= $fr ? 'Paiement en attente' : 'Payment pending' ?></div><?php endif; ?></td>
              <?php $ts = strtotime($order['created_at']); ?>
              <td class="sp-dim" style="white-space:nowrap"><?= acct_date($order['created_at'], $fr) ?><br><?= $fr ? date('G', $ts) . ' h ' . date('i', $ts) : date('g:i A', $ts) ?></td>
              <td>
                <?php if ($next): ?>
                  <form method="POST" action="<?= url('seller/orders/update-status') ?>" class="sp-row js-status-form" style="flex-wrap:nowrap">
                    <?= csrfField() ?>
                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                    <select name="status" class="sp-select-sm" aria-label="<?= $fr ? 'Nouveau statut' : 'New status' ?>">
                      <?php foreach ($next as $n): ?><option value="<?= $n ?>"><?= $actionLabels[$n] ?></option><?php endforeach; ?>
                    </select>
                    <button type="submit" class="sp-btn sp-btn-primary sp-btn-sm"><?= $fr ? 'Appliquer' : 'Apply' ?></button>
                  </form>
                <?php else: ?>
                  <a class="sp-btn sp-btn-ghost sp-btn-sm" href="<?= url('seller/orders/detail') ?>?id=<?= (int) $order['id'] ?>"><?= $fr ? 'Voir' : 'View' ?></a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php if ($totalPages > 1): ?>
        <nav class="sp-pager" aria-label="Pagination">
          <?php if ($currentPage > 1): ?><a href="<?= $qs(['page' => $currentPage - 1]) ?>" aria-label="<?= $fr ? 'Page précédente' : 'Previous page' ?>"><i class="fa-solid fa-chevron-left"></i></a><?php endif; ?>
          <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
            <?php if ($p === $currentPage): ?><span aria-current="page"><?= $p ?></span><?php else: ?><a href="<?= $qs(['page' => $p]) ?>"><?= $p ?></a><?php endif; ?>
          <?php endfor; ?>
          <?php if ($currentPage < $totalPages): ?><a href="<?= $qs(['page' => $currentPage + 1]) ?>" aria-label="<?= $fr ? 'Page suivante' : 'Next page' ?>"><i class="fa-solid fa-chevron-right"></i></a><?php endif; ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</div>
<?php require __DIR__ . '/partials/status-script.php'; ?>
<?php require __DIR__ . '/../layout-footer.php'; ?>
