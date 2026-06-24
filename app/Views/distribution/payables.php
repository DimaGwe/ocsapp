<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = ([
    'en' => [
        'page_title'        => 'Payables',
        'stat_orders'       => 'Total Orders',
        'stat_ordered'      => 'Total Ordered',
        'stat_paid'         => 'Total Paid',
        'stat_outstanding'  => 'Outstanding',
        'stat_pending_one'  => 'pending payment',
        'stat_pending_many' => 'pending payments',
        'stat_refunds'      => 'Refunds',
        'stat_refunds_sub'  => 'returned to you',
        'card_title'        => 'Distribution Request Payables',
        'filter_all'        => 'All',
        'filter_pending'    => 'Pending Payment',
        'filter_paid'       => 'Paid',
        'filter_refunded'   => 'Refunded',
        'btn_filter'        => 'Filter',
        'btn_clear'         => 'Clear',
        'empty_title'       => 'No payables yet',
        'empty_sub'         => 'Submitted distribution requests will appear here with their payment status.',
        'th_request'        => 'Request #',
        'th_dr_status'      => 'DR Status',
        'th_order_total'    => 'Order Total',
        'th_amount_paid'    => 'Amount Paid',
        'th_refunded'       => 'Refunded',
        'th_balance'        => 'Balance',
        'th_payment'        => 'Payment',
        'th_date'           => 'Date',
        'th_actions'        => 'Actions',
        'lnk_view'          => 'View',
        'lnk_pay'           => 'Pay Now',
    ],
    'fr' => [
        'page_title'        => 'Paiements dus',
        'stat_orders'       => 'Commandes totales',
        'stat_ordered'      => 'Total command&eacute;',
        'stat_paid'         => 'Total pay&eacute;',
        'stat_outstanding'  => 'Solde d&ucirc;',
        'stat_pending_one'  => 'paiement en attente',
        'stat_pending_many' => 'paiements en attente',
        'stat_refunds'      => 'Remboursements',
        'stat_refunds_sub'  => 'rembours&eacute;',
        'card_title'        => 'Paiements dus - Demandes de distribution',
        'filter_all'        => 'Tous',
        'filter_pending'    => 'Paiement en attente',
        'filter_paid'       => 'Pay&eacute;',
        'filter_refunded'   => 'Rembours&eacute;',
        'btn_filter'        => 'Filtrer',
        'btn_clear'         => 'Effacer',
        'empty_title'       => 'Aucun paiement d&ucirc;',
        'empty_sub'         => 'Les demandes de distribution soumises appara&icirc;tront ici avec leur statut de paiement.',
        'th_request'        => 'Demande #',
        'th_dr_status'      => 'Statut DR',
        'th_order_total'    => 'Total commande',
        'th_amount_paid'    => 'Montant pay&eacute;',
        'th_refunded'       => 'Rembours&eacute;',
        'th_balance'        => 'Solde',
        'th_payment'        => 'Paiement',
        'th_date'           => 'Date',
        'th_actions'        => 'Actions',
        'lnk_view'          => 'Voir',
        'lnk_pay'           => 'Payer maintenant',
    ],
])[$currentLang] ?? [];
require __DIR__ . '/layout-header.php';
?>

<style>
  .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
  .page-title  { font-size:24px; font-weight:700; color:var(--gray-700, #374151); }

  .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:24px; }
  .stat-card  { background:white; border-radius:12px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.08); border-left:4px solid var(--primary,#00b207); }
  .stat-card.green { border-left-color:#10b981; }
  .stat-card.amber { border-left-color:#f59e0b; }
  .stat-card.red   { border-left-color:#ef4444; }
  .stat-label { font-size:12px; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:.4px; margin-bottom:4px; }
  .stat-value { font-size:22px; font-weight:700; color:#374151; font-family:'SF Mono',monospace; }
  .stat-sub   { font-size:11px; color:#9ca3af; margin-top:3px; }

  .card { background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,.08); margin-bottom:24px; }
  .card-title { font-size:15px; font-weight:700; color:#374151; margin-bottom:16px; display:flex; align-items:center; gap:8px; }

  .filters-row { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:20px; }
  .form-select { padding:8px 12px; border:2px solid #e5e7eb; border-radius:8px; font-size:13px; color:#374151; background:white; }
  .form-select:focus { outline:none; border-color:var(--primary,#00b207); }
  .btn-filter { padding:8px 18px; background:var(--primary,#00b207); color:white; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-size:13px; }

  table { width:100%; border-collapse:collapse; }
  th  { padding:12px 14px; text-align:left; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; background:#f9fafb; border-bottom:2px solid #e5e7eb; }
  td  { padding:12px 14px; font-size:13px; color:#374151; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
  tr:last-child td { border-bottom:none; }
  tr:hover td { background:#f9fafb; }

  .dr-link { font-weight:700; color:var(--primary,#00b207); text-decoration:none; font-family:'SF Mono',monospace; }
  .amount  { font-weight:600; font-family:'SF Mono',monospace; }
  .amount.green { color:#059669; }
  .amount.red   { color:#dc2626; }

  .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.3px; }
  .badge-paid     { background:#d1fae5; color:#065f46; }
  .badge-pending  { background:#fef3c7; color:#92400e; }
  .badge-failed   { background:#fee2e2; color:#991b1b; }
  .badge-refunded { background:#ede9fe; color:#5b21b6; }
  .amount.purple  { color:#7c3aed; }

  .dr-status { display:inline-block; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; text-transform:uppercase; background:#f3f4f6; color:#6b7280; }

  .empty-state { padding:48px; text-align:center; color:#9ca3af; }
  .empty-state i { font-size:40px; margin-bottom:12px; display:block; }

  .pagination { display:flex; justify-content:center; gap:8px; padding:16px; }
  .page-btn { padding:7px 12px; border:1px solid #e5e7eb; border-radius:6px; color:#374151; text-decoration:none; font-size:13px; }
  .page-btn.active { background:var(--primary,#00b207); color:white; border-color:var(--primary,#00b207); font-weight:600; }
  .page-btn:hover:not(.active) { border-color:var(--primary,#00b207); color:var(--primary,#00b207); }
</style>

<div class="page-header">
  <h2 class="page-title"><i class="fas fa-file-invoice-dollar" style="color:var(--primary,#00b207);margin-right:8px;"></i> <?= $t['page_title'] ?></h2>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label"><?= $t['stat_orders'] ?></div>
    <div class="stat-value"><?= number_format($stats['total_requests'] ?? 0) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label"><?= $t['stat_ordered'] ?></div>
    <div class="stat-value">$<?= number_format($stats['total_ordered'] ?? 0, 2) ?></div>
  </div>
  <div class="stat-card green">
    <div class="stat-label"><?= $t['stat_paid'] ?></div>
    <div class="stat-value">$<?= number_format($stats['total_paid'] ?? 0, 2) ?></div>
  </div>
  <div class="stat-card <?= ($stats['outstanding'] ?? 0) > 0 ? 'amber' : 'green' ?>">
    <div class="stat-label"><?= $t['stat_outstanding'] ?></div>
    <div class="stat-value">$<?= number_format($stats['outstanding'] ?? 0, 2) ?></div>
    <div class="stat-sub"><?= $stats['pending_count'] ?? 0 ?> <?= ($stats['pending_count'] ?? 0) != 1 ? $t['stat_pending_many'] : $t['stat_pending_one'] ?></div>
  </div>
  <?php if (($stats['total_refunded'] ?? 0) > 0): ?>
  <div class="stat-card" style="border-left-color:#7c3aed;">
    <div class="stat-label"><?= $t['stat_refunds'] ?></div>
    <div class="stat-value" style="color:#7c3aed;">$<?= number_format($stats['total_refunded'], 2) ?></div>
    <div class="stat-sub"><?= $t['stat_refunds_sub'] ?></div>
  </div>
  <?php endif; ?>
</div>

<!-- Table -->
<div class="card">
  <h3 class="card-title"><i class="fas fa-list-alt" style="color:var(--primary,#00b207);"></i> <?= $t['card_title'] ?></h3>

  <form method="GET" action="<?= url('distribution/payables') ?>">
    <div class="filters-row">
      <select name="status" class="form-select">
        <option value=""><?= $t['filter_all'] ?></option>
        <option value="pending"  <?= $statusFilter === 'pending'  ? 'selected' : '' ?>><?= $t['filter_pending'] ?></option>
        <option value="paid"     <?= $statusFilter === 'paid'     ? 'selected' : '' ?>><?= $t['filter_paid'] ?></option>
        <option value="refunded" <?= $statusFilter === 'refunded' ? 'selected' : '' ?>><?= $t['filter_refunded'] ?></option>
      </select>
      <button type="submit" class="btn-filter"><i class="fas fa-search"></i> <?= $t['btn_filter'] ?></button>
      <?php if ($statusFilter): ?>
        <a href="<?= url('distribution/payables') ?>" style="font-size:13px;color:#6b7280;text-decoration:none;"><?= $t['btn_clear'] ?></a>
      <?php endif; ?>
    </div>
  </form>

  <?php if (empty($payables)): ?>
    <div class="empty-state">
      <i class="fas fa-file-invoice-dollar"></i>
      <p style="font-size:15px;font-weight:600;margin-bottom:4px;"><?= $t['empty_title'] ?></p>
      <p style="font-size:13px;"><?= $t['empty_sub'] ?></p>
    </div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th><?= $t['th_request'] ?></th>
        <th><?= $t['th_dr_status'] ?></th>
        <th><?= $t['th_order_total'] ?></th>
        <th><?= $t['th_amount_paid'] ?></th>
        <th><?= $t['th_refunded'] ?></th>
        <th><?= $t['th_balance'] ?></th>
        <th><?= $t['th_payment'] ?></th>
        <th><?= $t['th_date'] ?></th>
        <th><?= $t['th_actions'] ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($payables as $row): ?>
      <?php
        $amtPaid      = (float)($row['amount_paid']    ?? 0);
        $amtRefunded  = (float)($row['amount_refunded'] ?? 0);
        $total        = (float)($row['total_amount']    ?? 0);
        $balance      = max(0, $total - $amtPaid);
        $payStatus    = $row['payment_status'] ?? 'pending';
      ?>
      <tr>
        <td>
          <a href="<?= url('distribution/requests/show?id=' . $row['id']) ?>" class="dr-link">
            <?= htmlspecialchars($row['request_number']) ?>
          </a>
        </td>
        <td>
          <span class="dr-status"><?= ucfirst(str_replace('_', ' ', $row['status'])) ?></span>
        </td>
        <td class="amount">$<?= number_format($total, 2) ?></td>
        <td class="amount green">$<?= number_format($amtPaid, 2) ?></td>
        <td class="amount <?= $amtRefunded > 0 ? 'purple' : '' ?>">
          <?= $amtRefunded > 0 ? '+$' . number_format($amtRefunded, 2) : '—' ?>
        </td>
        <td class="amount <?= $balance > 0 ? 'red' : '' ?>">$<?= number_format($balance, 2) ?></td>
        <td>
          <span class="badge badge-<?= htmlspecialchars($payStatus) ?>"><?= ucfirst($payStatus) ?></span>
          <?php if (!empty($row['payment_method'])): ?>
            <div style="font-size:11px;color:#9ca3af;margin-top:2px;"><?= ucfirst(str_replace('_',' ',$row['payment_method'])) ?></div>
          <?php endif; ?>
        </td>
        <td style="font-size:12px;color:#6b7280;"><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
        <td>
          <a href="<?= url('distribution/requests/show?id=' . $row['id']) ?>" style="font-size:12px;color:var(--primary,#00b207);font-weight:600;text-decoration:none;"><?= $t['lnk_view'] ?></a>
          <?php if ($payStatus === 'pending' && in_array($row['status'], ['awaiting_payment','paid'], true) && $balance > 0): ?>
            &nbsp;·&nbsp;
            <a href="<?= url('distribution/pay?id=' . $row['id']) ?>" style="font-size:12px;color:#dc2626;font-weight:600;text-decoration:none;"><?= $t['lnk_pay'] ?></a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>&status=<?= urlencode($statusFilter) ?>" class="page-btn">&laquo; Prev</a>
    <?php endif; ?>
    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
      <a href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
      <a href="?page=<?= $page + 1 ?>&status=<?= urlencode($statusFilter) ?>" class="page-btn">Next &raquo;</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/layout-footer.php'; ?>
