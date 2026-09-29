<?php
/**
 * Seller order detail (/seller/orders/detail?id=, OrderController::sellerOrderDetail). New 2026-09-28:
 * sellers could not see which products an order contains. Shows items to prepare, customer, delivery
 * address or pickup, buyer notes, payment state, totals, history and the allowed status actions.
 */
$order = $order ?? [];
$items = $items ?? [];
$history = $history ?? [];
$nextStatuses = $nextStatuses ?? [];
$pageTitle = 'Order Detail';
require __DIR__ . '/../layout-header.php';

$status   = (string) ($order['status'] ?? '');
$isPickup = ($order['fulfillment_type'] ?? '') === 'pickup';
$addr     = acct_plain_row(json_decode($order['delivery_address'] ?? '', true));
$cust     = trim(html_entity_decode(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$notes    = trim(html_entity_decode((string) ($order['notes'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$pay      = (string) ($order['payment_status'] ?? '');
$payLabel = ['pending' => ['Paiement en attente', 'Payment pending'], 'paid' => ['Payée', 'Paid'], 'failed' => ['Paiement refusé', 'Payment failed'], 'refunded' => ['Remboursée', 'Refunded']];
$actionLabels = [
    'confirmed'  => [$fr ? 'Confirmer la commande' : 'Confirm order', 'fa-check', 'sp-btn-primary'],
    'processing' => [$fr ? 'Commencer la préparation' : 'Start preparing', 'fa-person-digging', 'sp-btn-primary'],
    'ready'      => [$fr ? 'Marquer prête' : 'Mark ready', 'fa-box', 'sp-btn-primary'],
    'delivered'  => [$fr ? 'Marquer ramassée par le client' : 'Mark collected by customer', 'fa-hand-holding', 'sp-btn-primary'],
    'cancelled'  => [$fr ? 'Annuler la commande' : 'Cancel order', 'fa-xmark', 'sp-btn-danger'],
];
$m = fn($k) => (float) ($order[$k] ?? 0);
?>
<div class="sp-stack">
  <div class="sp-between">
    <div>
      <h2 style="margin:0 0 4px;font-size:22px">#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></h2>
      <div class="sp-dim"><?= ($fr ? 'Passée le ' : 'Placed on ') . acct_datetime($order['created_at'] ?? '', $fr) ?></div>
    </div>
    <a class="sp-btn sp-btn-ghost" href="<?= url('seller/orders') ?>"><i class="fa-solid fa-arrow-left"></i> <?= $fr ? 'Toutes les commandes' : 'All orders' ?></a>
  </div>

  <section class="sp-card">
    <div class="sp-row" style="margin-bottom:16px">
      <span class="sp-badge sp-badge-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars(acct_status($status, $fr)) ?></span>
      <?php if (isset($payLabel[$pay])): ?><span class="sp-badge sp-badge-<?= $pay === 'paid' ? 'paid' : ($pay === 'pending' ? 'warn' : 'danger') ?>"><i class="fa-solid fa-credit-card"></i> <?= $payLabel[$pay][$fr ? 0 : 1] ?></span><?php endif; ?>
      <span class="sp-badge"><i class="fa-solid <?= $isPickup ? 'fa-store' : 'fa-truck' ?>"></i> <?= $isPickup ? ($fr ? 'Ramassage en boutique' : 'Store pickup') : ($fr ? 'Livraison ODA' : 'ODA delivery') ?></span>
    </div>
    <?php if ($pay !== 'paid' && !in_array($status, ['cancelled', 'refunded', 'delivered'], true)): ?>
      <div class="sp-alert sp-alert-warn"><i class="fa-solid fa-circle-info"></i><div><?= $fr
        ? "Cette commande n'est pas encore payée (par exemple un virement Interac en attente). Ne la préparez pas : vous pourrez la traiter dès que le paiement sera confirmé."
        : "This order isn't paid yet (for example an Interac transfer on its way). Don't prepare it: you can process it as soon as the payment is confirmed." ?></div></div>
    <?php endif; ?>
    <?php if ($nextStatuses): ?>
      <div class="sp-row">
        <?php foreach ($nextStatuses as $n): [$label, $icon, $cls] = $actionLabels[$n]; ?>
          <form method="POST" action="<?= url('seller/orders/update-status') ?>" class="js-status-form">
            <?= csrfField() ?>
            <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
            <input type="hidden" name="status" value="<?= $n ?>">
            <button type="submit" class="sp-btn <?= $cls ?>"><i class="fa-solid <?= $icon ?>"></i> <?= $label ?></button>
          </form>
        <?php endforeach; ?>
      </div>
    <?php elseif ($status === 'ready' && !$isPickup): ?>
      <p class="sp-muted" style="margin:0"><?= $fr ? 'Prête : un livreur ODA viendra la chercher.' : 'Ready: an ODA driver will pick it up.' ?></p>
    <?php endif; ?>
  </section>

  <div class="sp-grid-2">
    <section class="sp-card">
      <div class="sp-card-head"><h2><?= $fr ? 'Client' : 'Customer' ?></h2></div>
      <p class="sp-strong" style="margin:0 0 4px"><?= htmlspecialchars($cust !== '' ? $cust : ($addr['name'] ?? '')) ?></p>
      <?php $phone = $addr['phone'] ?? ($order['customer_phone'] ?? ''); ?>
      <?php if ($phone): ?><p style="margin:0 0 4px"><a class="sp-green" href="tel:<?= htmlspecialchars($phone) ?>"><?= htmlspecialchars($phone) ?></a></p><?php endif; ?>
      <?php if (!empty($order['customer_email'])): ?><p class="sp-dim" style="margin:0"><?= htmlspecialchars($order['customer_email']) ?></p><?php endif; ?>
    </section>
    <section class="sp-card">
      <div class="sp-card-head"><h2><?= $isPickup ? ($fr ? 'Ramassage' : 'Pickup') : ($fr ? 'Adresse de livraison' : 'Delivery address') ?></h2></div>
      <?php if ($isPickup): ?>
        <p class="sp-muted" style="margin:0"><?= $fr ? 'Le client vient chercher la commande à votre commerce.' : 'The customer collects the order at your shop.' ?></p>
      <?php elseif (!empty($addr['address_line_1'])): ?>
        <p style="margin:0"><?= htmlspecialchars($addr['address_line_1']) ?><?= !empty($addr['address_line_2']) ? '<br>' . htmlspecialchars($addr['address_line_2']) : '' ?><br>
          <?= htmlspecialchars(trim(($addr['city'] ?? '') . ', ' . ($addr['state'] ?? '') . ' ' . ($addr['postal_code'] ?? ''), ', ')) ?></p>
      <?php else: ?>
        <p class="sp-dim" style="margin:0"><?= $fr ? 'Non disponible' : 'Not available' ?></p>
      <?php endif; ?>
      <?php if ($notes !== ''): ?>
        <div class="sp-alert sp-alert-info" style="margin:14px 0 0"><i class="fa-solid fa-note-sticky"></i><div><strong><?= $fr ? 'Note du client : ' : 'Customer note: ' ?></strong><?= htmlspecialchars($notes) ?></div></div>
      <?php endif; ?>
    </section>
  </div>

  <section class="sp-card">
    <div class="sp-card-head"><h2><?= $fr ? 'Articles à préparer' : 'Items to prepare' ?></h2></div>
    <div class="sp-table-wrap">
      <table class="sp-table">
        <thead><tr><th><?= $fr ? 'Produit' : 'Product' ?></th><th><?= $fr ? 'SKU' : 'SKU' ?></th><th class="num"><?= $fr ? 'Qté' : 'Qty' ?></th><th class="num"><?= $fr ? 'Prix' : 'Price' ?></th><th class="num"><?= $fr ? 'Total' : 'Total' ?></th></tr></thead>
        <tbody>
        <?php foreach ($items as $it): $q = (int) $it['quantity']; $p = (float) $it['price']; ?>
          <tr>
            <td><div class="sp-row" style="flex-wrap:nowrap">
              <?php if (!empty($it['image_path'])): ?><img class="sp-thumb" src="<?= htmlspecialchars(url(ltrim($it['image_path'], '/'))) ?>" alt="" loading="lazy"><?php endif; ?>
              <span class="sp-strong"><?= htmlspecialchars(html_entity_decode((string) ($it['product_name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></span></div></td>
            <td class="sp-dim"><?= htmlspecialchars($it['sku'] ?: ($it['product_sku'] ?? '')) ?></td>
            <td class="num sp-strong"><?= $q ?></td>
            <td class="num"><?= acct_money($p, $fr) ?></td>
            <td class="num sp-strong"><?= acct_money($p * $q, $fr) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div style="max-width:340px;margin:14px 0 0 auto;display:grid;gap:6px">
      <div class="sp-between"><span class="sp-muted"><?= $fr ? 'Sous-total (vos produits)' : 'Subtotal (your products)' ?></span><span class="sp-strong"><?= acct_money($m('subtotal'), $fr) ?></span></div>
      <div class="sp-between"><span class="sp-muted"><?= $fr ? 'Taxes' : 'Taxes' ?></span><span><?= acct_money($m('tax'), $fr) ?></span></div>
      <?php if (!$isPickup): ?><div class="sp-between"><span class="sp-muted"><?= $fr ? 'Livraison (payée par le client)' : 'Delivery (paid by customer)' ?></span><span><?= acct_money($m('delivery_fee') + $m('additional_stop_fee'), $fr) ?></span></div><?php endif; ?>
      <div class="sp-between" style="border-top:1px solid var(--sp-border);padding-top:8px"><span class="sp-strong"><?= $fr ? 'Total payé par le client' : 'Total paid by customer' ?></span><span class="sp-strong"><?= acct_money($m('total'), $fr) ?></span></div>
    </div>
  </section>

  <?php if ($history): ?>
    <section class="sp-card">
      <div class="sp-card-head"><h2><?= $fr ? 'Historique' : 'History' ?></h2></div>
      <?php foreach ($history as $h): $hs = (string) ($h['new_status'] ?? $h['status'] ?? ''); ?>
        <div class="sp-list-row">
          <div><span class="sp-strong"><?= htmlspecialchars($hs !== '' ? acct_status($hs, $fr) : ($fr ? 'Mise à jour' : 'Update')) ?></span>
            <?php if (!empty($h['notes'])): ?><div class="sp-dim"><?= htmlspecialchars(html_entity_decode((string) $h['notes'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></div><?php endif; ?></div>
          <span class="sp-dim"><?= acct_datetime($h['created_at'], $fr) ?></span>
        </div>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/status-script.php'; ?>
<?php require __DIR__ . '/../layout-footer.php'; ?>
