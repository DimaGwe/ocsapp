<?php
/**
 * Seller dashboard (/seller/dashboard, ShopController::dashboard)
 * Updated 2026-09-28: seller-portal.css kit, bilingual EN/FR (was English), fr-CA money/dates,
 * translated statuses; recent orders link to the new order detail. "Ventes payées" = product
 * subtotal of paid, non-cancelled orders (controller).
 */
$shop = $shop ?? null;
$stats = $stats ?? [];
$recentOrders = $recentOrders ?? [];
$pageTitle = 'Seller Dashboard';
require __DIR__ . '/layout-header.php';
?>
<?php if (!$shop): ?>
  <section class="sp-card">
    <div class="sp-empty">
      <i class="fa-solid fa-store"></i>
      <h2><?= $fr ? "Vous n'avez pas encore de commerce" : "You don't have a shop yet" ?></h2>
      <p><?= $fr ? 'Créez votre commerce pour commencer à vendre sur OCSAPP.' : 'Create your shop to start selling on OCSAPP.' ?></p>
      <a href="<?= url('seller/shop/create') ?>" class="sp-btn sp-btn-primary"><i class="fa-solid fa-plus"></i> <?= $fr ? 'Créer mon commerce' : 'Create my shop' ?></a>
    </div>
  </section>
<?php else: ?>
  <?php
  $pending = (int) ($stats['pending_orders'] ?? 0);
  $cards = [
      ['fa-receipt',        (string) (int) ($stats['total_orders'] ?? 0), $fr ? 'Commandes' : 'Total orders', ''],
      ['fa-hourglass-half', (string) $pending,                            $fr ? 'En attente' : 'Pending', $pending > 0 ? 'is-warn' : ''],
      ['fa-calendar-day',   (string) (int) ($stats['today_orders'] ?? 0), $fr ? "Commandes aujourd'hui" : "Today's orders", ''],
      ['fa-sack-dollar',    acct_money($stats['total_revenue'] ?? 0, $fr), $fr ? 'Ventes payées' : 'Paid sales', ''],
      ['fa-cubes',          (string) (int) ($stats['products_count'] ?? 0), $fr ? 'Produits' : 'Products', ''],
  ];
  $pkg = $shop['subscription_package'] ?? 'Essential';
  $isFounding = !empty($shop['founding_partner']);
  $freeLeft = (int) ($shop['founding_free_deliveries_remaining'] ?? 0);
  ?>
  <div class="sp-stack">
    <div class="sp-stats">
      <?php foreach ($cards as [$icon, $value, $label, $cls]): ?>
        <div class="sp-stat <?= $cls ?>"><i class="fa-solid <?= $icon ?>"></i><strong><?= htmlspecialchars($value) ?></strong><span><?= $label ?></span></div>
      <?php endforeach; ?>
    </div>

    <div class="sp-grid-2">
      <section class="sp-card">
        <div class="sp-card-head"><h2><?= $fr ? 'Mon forfait' : 'My plan' ?></h2><a href="<?= url('seller/shop/settings') ?>"><?= $fr ? 'Paramètres' : 'Settings' ?> <i class="fa-solid fa-arrow-right"></i></a></div>
        <div class="sp-row">
          <span class="sp-badge sp-badge-ok"><i class="fa-solid fa-star"></i> <?= htmlspecialchars($pkg) ?></span>
          <span class="sp-muted">
            <?= $fr ? 'Commission : ' : 'Commission: ' ?><?= number_format((float) ($shop['commission_rate'] ?? 15), 2, $fr ? ',' : '.', '') ?>&nbsp;% <?= $fr ? 'livraison' : 'delivery' ?>
            · <?= number_format((float) ($shop['pickup_commission_rate'] ?? 8), 2, $fr ? ',' : '.', '') ?>&nbsp;% <?= $fr ? 'ramassage' : 'pickup' ?>
          </span>
        </div>
        <?php if ($isFounding): ?>
          <div class="sp-row" style="margin-top:14px">
            <span class="sp-badge sp-badge-gold"><i class="fa-solid fa-star"></i>
              <?= $fr ? 'Partenaire fondateur n° ' : 'Founding Partner #' ?><?= (int) $shop['founding_partner_number'] ?><?= $fr ? ' sur 20' : ' of 20' ?></span>
            <?php if (!empty($shop['founding_partner_expires_at'])): ?>
              <span class="sp-dim"><?= $fr ? 'Taux verrouillé jusqu\'au ' : 'Rate locked until ' ?><?= acct_date($shop['founding_partner_expires_at'], $fr) ?></span>
            <?php endif; ?>
          </div>
          <?php if ($freeLeft > 0): ?>
            <p class="sp-muted" style="margin:10px 0 0"><?= $fr
              ? $freeLeft . ' ' . ($freeLeft > 1 ? 'commandes de livraison sans commission restantes' : 'commande de livraison sans commission restante')
              : $freeLeft . ' commission-free delivery order' . ($freeLeft === 1 ? '' : 's') . ' remaining' ?></p>
          <?php endif; ?>
        <?php endif; ?>
      </section>

      <section class="sp-card">
        <div class="sp-card-head"><h2><?= $fr ? 'Actions rapides' : 'Quick actions' ?></h2></div>
        <div class="sp-row">
          <a href="<?= url('seller/orders') ?>?status=pending" class="sp-btn sp-btn-primary"><i class="fa-solid fa-hourglass-half"></i> <?= $fr ? 'Commandes en attente' : 'Pending orders' ?><?= $pending > 0 ? ' (' . $pending . ')' : '' ?></a>
          <a href="<?= url('seller/inventory/add') ?>" class="sp-btn sp-btn-ghost"><i class="fa-solid fa-plus"></i> <?= $fr ? 'Ajouter un produit' : 'Add product' ?></a>
          <a href="<?= url('seller/shop/settings') ?>" class="sp-btn sp-btn-ghost"><i class="fa-solid fa-gear"></i> <?= $fr ? 'Paramètres du commerce' : 'Shop settings' ?></a>
        </div>
      </section>
    </div>

    <section class="sp-card">
      <div class="sp-card-head"><h2><?= $fr ? 'Commandes récentes' : 'Recent orders' ?></h2><a href="<?= url('seller/orders') ?>"><?= $fr ? 'Tout voir' : 'View all' ?> <i class="fa-solid fa-arrow-right"></i></a></div>
      <?php if (empty($recentOrders)): ?>
        <div class="sp-empty"><i class="fa-solid fa-inbox"></i><p><?= $fr ? "Aucune commande pour l'instant." : 'No orders yet.' ?></p></div>
      <?php else: ?>
        <?php foreach ($recentOrders as $order): ?>
          <?php $cust = trim(html_entity_decode(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>
          <a class="sp-list-row" href="<?= url('seller/orders/detail') ?>?id=<?= (int) $order['id'] ?>">
            <div>
              <div class="sp-strong">#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></div>
              <div class="sp-dim"><?= htmlspecialchars($cust) ?> · <?= acct_date($order['created_at'], $fr) ?><?= ($order['fulfillment_type'] ?? '') === 'pickup' ? ' · ' . ($fr ? 'Ramassage' : 'Pickup') : '' ?></div>
            </div>
            <div class="sp-row">
              <span class="sp-badge sp-badge-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(acct_status((string) $order['status'], $fr)) ?></span>
              <span class="sp-strong"><?= acct_money($order['total'], $fr) ?></span>
              <i class="fa-solid fa-chevron-right sp-dim"></i>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/layout-footer.php'; ?>
