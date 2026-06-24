<?php require __DIR__ . '/layout-header.php'; ?>

<style>
  .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
  .page-title  { font-size:24px; font-weight:700; color:var(--gray-700); }

  .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:16px; margin-bottom:24px; }
  .stat-card  { background:white; border-radius:12px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.08); border-left:4px solid var(--primary); }
  .stat-card.amber { border-left-color:#f59e0b; }
  .stat-card.blue  { border-left-color:#3b82f6; }
  .stat-card.green { border-left-color:#10b981; }
  .stat-label { font-size:12px; color:var(--gray-400); font-weight:600; text-transform:uppercase; letter-spacing:.4px; margin-bottom:4px; }
  .stat-value { font-size:22px; font-weight:700; color:var(--gray-700); font-family:'SF Mono',monospace; }

  .filters-card { background:white; border-radius:12px; padding:16px 20px; box-shadow:0 1px 3px rgba(0,0,0,.08); margin-bottom:20px; }
  .filters-row  { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
  .form-select, .form-input { padding:8px 12px; border:2px solid #e5e7eb; border-radius:8px; font-size:13px; color:#374151; background:white; }
  .form-select:focus, .form-input:focus { outline:none; border-color:var(--primary); }
  .btn-filter { padding:8px 18px; background:var(--primary); color:white; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-size:13px; }

  .table-card { background:white; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.08); overflow:hidden; }
  table { width:100%; border-collapse:collapse; }
  th  { padding:12px 14px; text-align:left; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; background:#f9fafb; border-bottom:2px solid #e5e7eb; }
  td  { padding:12px 14px; font-size:13px; color:#374151; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
  tr:last-child td { border-bottom:none; }
  tr:hover td { background:#f9fafb; }

  .so-number  { font-weight:700; color:var(--primary); text-decoration:none; font-family:'SF Mono',monospace; }
  .po-number  { font-size:12px; color:#6b7280; font-family:'SF Mono',monospace; }
  .amount     { font-weight:600; font-family:'SF Mono',monospace; }

  .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.3px; }
  .badge-accepted       { background:#dbeafe; color:#1d4ed8; }
  .badge-preparing      { background:#fef3c7; color:#92400e; }
  .badge-ready_for_pickup { background:#d1fae5; color:#065f46; }
  .badge-picked_up      { background:#ede9fe; color:#5b21b6; }
  .badge-completed      { background:#d1fae5; color:#064e3b; }
  .badge-declined       { background:#fee2e2; color:#991b1b; }

  .empty-state { padding:48px; text-align:center; color:#9ca3af; }
  .empty-state i { font-size:40px; margin-bottom:12px; display:block; }

  .pagination { display:flex; justify-content:center; align-items:center; gap:8px; padding:20px; }
  .page-btn { padding:7px 12px; border:1px solid #e5e7eb; border-radius:6px; color:#374151; text-decoration:none; font-size:13px; }
  .page-btn.active { background:var(--primary); color:white; border-color:var(--primary); font-weight:600; }
  .page-btn:hover:not(.active) { border-color:var(--primary); color:var(--primary); }
</style>

<div class="page-header">
  <h2 class="page-title"><i class="fas fa-receipt" style="color:var(--primary);margin-right:8px;"></i> <?= $fr ? 'Bons de vente' : 'Sales Orders' ?></h2>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label"><?= $fr ? 'Total BV' : 'Total SOs' ?></div>
    <div class="stat-value"><?= number_format($stats['total_so'] ?? 0) ?></div>
  </div>
  <div class="stat-card amber">
    <div class="stat-label"><?= $fr ? 'En cours' : 'In Progress' ?></div>
    <div class="stat-value"><?= number_format($stats['in_progress'] ?? 0) ?></div>
  </div>
  <div class="stat-card blue">
    <div class="stat-label"><?= $fr ? 'En exécution' : 'Fulfilling' ?></div>
    <div class="stat-value"><?= number_format($stats['fulfilling'] ?? 0) ?></div>
  </div>
  <div class="stat-card green">
    <div class="stat-label"><?= $fr ? 'Complétés' : 'Completed' ?></div>
    <div class="stat-value"><?= number_format($stats['completed'] ?? 0) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label"><?= $fr ? 'Valeur totale' : 'Total Value' ?></div>
    <div class="stat-value">$<?= number_format($stats['total_value'] ?? 0, 2) ?></div>
  </div>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET" action="<?= url('supplier/sales-orders') ?>">
    <div class="filters-row">
      <input type="text" name="search" class="form-input" placeholder="<?= $fr ? 'Rechercher BV # ou BC #' : 'Search SO # or PO #' ?>" value="<?= htmlspecialchars($search) ?>">
      <select name="status" class="form-select">
        <option value=""><?= $fr ? 'Tous les statuts' : 'All Statuses' ?></option>
        <?php
        $soStatuses = $fr
          ? ['accepted'=>'Accepté','preparing'=>'En préparation','ready_for_pickup'=>'Prêt pour ramassage','picked_up'=>'Ramassé','completed'=>'Complété','declined'=>'Refusé']
          : ['accepted'=>'Accepted','preparing'=>'Preparing','ready_for_pickup'=>'Ready for Pickup','picked_up'=>'Picked Up','completed'=>'Completed','declined'=>'Declined'];
        foreach ($soStatuses as $val => $label): ?>
        <option value="<?= $val ?>" <?= $status === $val ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn-filter"><i class="fas fa-search"></i> <?= $fr ? 'Filtrer' : 'Filter' ?></button>
      <?php if ($search || $status): ?>
        <a href="<?= url('supplier/sales-orders') ?>" style="font-size:13px;color:#6b7280;text-decoration:none;"><?= $fr ? 'Effacer' : 'Clear' ?></a>
      <?php endif; ?>
    </div>
  </form>
</div>

<!-- Table -->
<div class="table-card">
  <?php if (empty($salesOrders)): ?>
    <div class="empty-state">
      <i class="fas fa-receipt"></i>
      <p style="font-size:15px;font-weight:600;margin-bottom:4px;"><?= $fr ? 'Aucun bon de vente pour l\'instant' : 'No Sales Orders yet' ?></p>
      <p style="font-size:13px;"><?= $fr ? 'Lorsque vous acceptez un bon de commande, il devient un bon de vente et apparaît ici.' : 'When you accept a Purchase Order it becomes a Sales Order and will appear here.' ?></p>
    </div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th><?= $fr ? 'BV #' : 'SO #' ?></th>
        <th><?= $fr ? 'BC #' : 'PO #' ?></th>
        <th><?= $fr ? 'Client' : 'Customer' ?></th>
        <th><?= $fr ? 'Articles' : 'Items' ?></th>
        <th><?= $fr ? 'Total' : 'Total' ?></th>
        <th><?= $fr ? 'Statut' : 'Status' ?></th>
        <th><?= $fr ? 'Accepté le' : 'Accepted' ?></th>
        <th style="text-align:center;"><?= $fr ? 'Docs' : 'Docs' ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($salesOrders as $so): ?>
      <tr>
        <td>
          <a href="<?= url('supplier/orders/view?id=' . $so['id']) ?>" class="so-number">
            <?= htmlspecialchars($so['so_number']) ?>
          </a>
        </td>
        <td>
          <span class="po-number"><?= htmlspecialchars($so['po_number']) ?></span>
        </td>
        <td>
          <?php if (!empty($so['request_number'])): ?>
            <span style="font-family:'SF Mono',monospace;font-size:12px;font-weight:600;color:#374151;"><?= htmlspecialchars($so['request_number']) ?></span>
          <?php else: ?>
            <span style="color:#9ca3af;">—</span>
          <?php endif; ?>
        </td>
        <td><?= number_format($so['item_count']) ?> <?= $fr ? ($so['item_count'] != 1 ? 'articles' : 'article') : ($so['item_count'] != 1 ? 'items' : 'item') ?></td>
        <td class="amount">$<?= number_format($so['total_amount'], 2) ?></td>
        <td>
          <?php
            $statusLabels = $fr ? [
              'accepted'         => 'Accepté',
              'preparing'        => 'En préparation',
              'ready_for_pickup' => 'Prêt ramassage',
              'picked_up'        => 'Ramassé',
              'completed'        => 'Complété',
              'declined'         => 'Refusé',
            ] : [
              'accepted'         => 'Accepted',
              'preparing'        => 'Preparing',
              'ready_for_pickup' => 'Ready for Pickup',
              'picked_up'        => 'Picked Up',
              'completed'        => 'Completed',
              'declined'         => 'Declined',
            ];
            $s = $so['status'];
          ?>
          <span class="badge badge-<?= htmlspecialchars($s) ?>"><?= $statusLabels[$s] ?? ucfirst($s) ?></span>
        </td>
        <td style="color:#6b7280;font-size:12px;">
          <?= $so['supplier_accepted_at'] ? date('M j, Y', strtotime($so['supplier_accepted_at'])) : '—' ?>
        </td>
        <td style="text-align:center;white-space:nowrap;">
          <a href="<?= url('supplier/orders/download-pdf?id=' . $so['id'] . '&type=po') ?>" target="_blank" title="Download PO" style="color:#6b7280;font-size:15px;margin-right:8px;text-decoration:none;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#6b7280'"><i class="fas fa-file-alt"></i></a>
          <a href="<?= url('supplier/orders/download-pdf?id=' . $so['id'] . '&type=so') ?>" target="_blank" title="Download SO" style="color:#15803d;font-size:15px;text-decoration:none;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#15803d'"><i class="fas fa-receipt"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="page-btn">&laquo; <?= $fr ? 'Préc.' : 'Prev' ?></a>
    <?php endif; ?>
    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
      <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
      <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="page-btn"><?= $fr ? 'Suiv.' : 'Next' ?> &raquo;</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/layout-footer.php'; ?>
