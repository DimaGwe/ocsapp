<?php
$pageTitle = (($_SESSION['language'] ?? 'fr') === 'fr') ? 'Bons de commande' : 'Purchase Orders';
require dirname(__DIR__) . '/layout-header.php';
// $t is loaded by layout-header.php
?>

<style>
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-700);
  }

  .filters-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }

  .filters-grid {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 16px;
  }

  .form-select {
    padding: 10px 16px;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    font-size: 14px;
    width: 100%;
  }

  .btn-filter {
    padding: 10px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
  }

  .table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: var(--gray-50);
  }

  th {
    padding: 12px 20px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
  }

  td {
    padding: 16px 20px;
    border-top: 1px solid var(--gray-200);
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  .po-number {
    font-weight: 600;
    color: var(--primary);
    text-decoration: none;
  }

  .po-number:hover {
    text-decoration: underline;
  }

  .badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge.draft { background: #e5e7eb; color: #374151; }
  .badge.sent { background: #dbeafe; color: #1e40af; }
  .badge.accepted { background: #fef3c7; color: #92400e; }
  .badge.preparing { background: #ede9fe; color: #5b21b6; }
  .badge.ready_for_pickup { background: #fff7ed; color: #c2410c; }
  .badge.picked_up { background: #ecfdf5; color: #065f46; }
  .badge.completed { background: #dcfce7; color: #166534; }
  .badge.cancelled { background: #fee2e2; color: #991b1b; }

  .empty-state {
    text-align: center;
    padding: 80px 20px;
  }

  .empty-state i {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 20px;
  }

  .empty-state h3 {
    font-size: 20px;
    color: var(--gray-700);
    margin-bottom: 8px;
  }

  .empty-state p {
    color: var(--gray-600);
  }
</style>

<div class="page-header">
  <h1 class="page-title"><?= $fr ? 'Bons de commande' : 'Purchase Orders' ?></h1>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET">
    <div class="filters-grid">
      <select name="status" class="form-select">
        <option value=""><?= $fr ? 'Tous les statuts' : 'All Statuses' ?></option>
        <option value="sent" <?= ($status ?? '') === 'sent' ? 'selected' : '' ?>><?= $fr ? 'Envoyé' : 'Sent' ?></option>
        <option value="accepted" <?= ($status ?? '') === 'accepted' ? 'selected' : '' ?>><?= $fr ? 'Accepté' : 'Accepted' ?></option>
        <option value="preparing" <?= ($status ?? '') === 'preparing' ? 'selected' : '' ?>><?= $fr ? 'En préparation' : 'Preparing' ?></option>
        <option value="ready_for_pickup" <?= ($status ?? '') === 'ready_for_pickup' ? 'selected' : '' ?>><?= $fr ? 'Prêt pour le ramassage' : 'Ready for Pickup' ?></option>
        <option value="picked_up" <?= ($status ?? '') === 'picked_up' ? 'selected' : '' ?>><?= $fr ? 'Ramassé' : 'Picked Up' ?></option>
        <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>><?= $fr ? 'Complété' : 'Completed' ?></option>
        <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>><?= $fr ? 'Annulé' : 'Cancelled' ?></option>
      </select>
      <button type="submit" class="btn-filter">
        <i class="fas fa-filter"></i> <?= $fr ? 'Filtrer' : 'Filter' ?>
      </button>
    </div>
  </form>
</div>

<!-- Orders Table -->
<div class="table-card">
  <?php if (!empty($orders)): ?>
    <table>
      <thead>
        <tr>
          <th><?= $fr ? 'No BC' : 'PO Number' ?></th>
          <th><?= $fr ? 'Date de commande' : 'Order Date' ?></th>
          <th><?= $fr ? 'Livraison prévue' : 'Expected Delivery' ?></th>
          <th><?= $fr ? 'Articles' : 'Items' ?></th>
          <th><?= $fr ? 'Montant total' : 'Total Amount' ?></th>
          <th><?= $fr ? 'Statut' : 'Status' ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td>
              <a href="<?= url('supplier/orders/view?id=' . $order['id']) ?>" class="po-number">
                <?= htmlspecialchars($order['po_number']) ?>
              </a>
            </td>
            <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
            <td>
              <?= $order['expected_delivery_date'] ? date('M d, Y', strtotime($order['expected_delivery_date'])) : 'N/A' ?>
            </td>
            <td><?= $order['item_count'] ?> <?= $fr ? 'articles' : 'items' ?> (<?= $order['total_items'] ?> <?= $fr ? 'unités' : 'units' ?>)</td>
            <td>$<?= number_format($order['total_amount'], 2) ?></td>
            <td>
              <span class="badge <?= $order['status'] ?>">
                <?= $fr ? [
                'sent'=>'Envoyé','accepted'=>'Accepté','preparing'=>'En préparation',
                'ready_for_pickup'=>'Prêt ramassage','picked_up'=>'Ramassé',
                'completed'=>'Complété','cancelled'=>'Annulé','draft'=>'Brouillon'
              ][$order['status']] ?? ucfirst($order['status'])
              : ($t['po_status_' . $order['status']] ?? ucfirst($order['status'])) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-file-invoice"></i>
      <h3><?= $fr ? 'Aucun bon de commande' : 'No Purchase Orders' ?></h3>
      <p><?= $fr ? "Vous n'avez pas encore de bons de commande" : "You don't have any purchase orders yet" ?></p>
    </div>
  <?php endif; ?>
</div>

<?php
  $baseUrl = 'supplier/orders';
  $queryParams = ['status' => $status ?? ''];
  require dirname(dirname(__DIR__)) . '/components/pagination.php';
?>

<?php require dirname(__DIR__) . '/layout-footer.php'; ?>
