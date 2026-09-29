<?php
/**
 * Seller payouts (/seller/payouts, ShopController::payouts)
 * Updated 2026-09-28: seller-portal.css kit, bilingual EN/FR (was English), fr-CA money/dates.
 * One seller_payouts row per delivered order: subtotal - commission - processing fee - chargeback.
 */
$shop = $shop ?? null;
$payouts = $payouts ?? [];
$pendingBalance = $pendingBalance ?? 0.00;
$pageTitle = 'Payouts';
require __DIR__ . '/layout-header.php';
$statusLabels = ['pending' => ['À verser', 'Pending', 'warn'], 'paid' => ['Versé', 'Paid', 'paid'], 'held' => ['Retenu', 'Held', 'danger']];
$neg = fn($v) => (float) $v > 0 ? '-' . acct_money($v, $fr) : '<span class="sp-dim">' . acct_money(0, $fr) . '</span>';
?>
<div class="sp-stack">
  <div class="sp-stats">
    <div class="sp-stat"><i class="fa-solid fa-wallet"></i><strong><?= acct_money($pendingBalance, $fr) ?></strong><span><?= $fr ? 'Solde à verser' : 'Pending balance' ?></span></div>
    <div class="sp-stat"><i class="fa-solid fa-receipt"></i><strong><?= count($payouts) ?></strong><span><?= $fr ? 'Lignes de versement' : 'Payout lines' ?></span></div>
  </div>

  <section class="sp-card">
    <div class="sp-card-head"><h2><?= $fr ? 'Relevé des versements' : 'Payout statement' ?></h2></div>
    <?php if (empty($payouts)): ?>
      <div class="sp-empty"><i class="fa-solid fa-money-bill-wave"></i>
        <p><?= $fr ? "Aucun versement pour l'instant. Une ligne est créée dès qu'une commande est livrée." : 'No payouts yet. A line is created as soon as an order is delivered.' ?></p></div>
    <?php else: ?>
      <div class="sp-table-wrap">
        <table class="sp-table">
          <thead><tr>
            <th><?= $fr ? 'Commande' : 'Order' ?></th><th><?= $fr ? 'Date' : 'Date' ?></th><th class="num"><?= $fr ? 'Sous-total' : 'Subtotal' ?></th>
            <th class="num"><?= $fr ? 'Commission' : 'Commission' ?></th><th class="num"><?= $fr ? 'Frais de traitement' : 'Processing fee' ?></th>
            <th class="num"><?= $fr ? 'Rétrofacturation' : 'Chargeback' ?></th><th class="num"><?= $fr ? 'Versement net' : 'Net payout' ?></th><th><?= $fr ? 'Statut' : 'Status' ?></th>
          </tr></thead>
          <tbody>
          <?php foreach ($payouts as $p): [$lf, $le, $cls] = $statusLabels[$p['status']] ?? [$p['status'], $p['status'], '']; ?>
            <tr>
              <td class="sp-strong">#<?= htmlspecialchars($p['order_number']) ?></td>
              <td class="sp-dim"><?= acct_date($p['created_at'], $fr) ?></td>
              <td class="num"><?= acct_money($p['subtotal'], $fr) ?></td>
              <td class="num"><?= $neg($p['commission_amount']) ?></td>
              <td class="num"><?= $neg($p['processing_fee_amount']) ?></td>
              <td class="num"><?= $neg($p['chargeback_amount']) ?></td>
              <td class="num sp-strong"><?= acct_money((float) $p['net_payout_amount'] - (float) $p['chargeback_amount'], $fr) ?></td>
              <td><span class="sp-badge sp-badge-<?= $cls ?>"><?= htmlspecialchars($fr ? $lf : $le) ?></span></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</div>
<?php require __DIR__ . '/layout-footer.php'; ?>
