<?php
$pageTitle = 'Purchase Orders';
$currentPage = 'purchase-orders';
ob_start();
?>

<style>
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
  }

  .btn-primary {
    background: var(--primary);
    color: white;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: all 0.2s;
  }

  .btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
  }

  .filters-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
  }

  .filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr auto;
    gap: 16px;
  }

  .form-input, .form-select {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
  }

  .btn-filter {
    padding: 10px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    font-weight: 600;
  }

  .table-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
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
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
  }

  td {
    padding: 16px 20px;
    border-top: 1px solid var(--border);
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  .badge {
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .badge.draft            { background: #e5e7eb; color: #374151; }
  .badge.sent             { background: #dbeafe; color: #1e40af; }
  .badge.accepted         { background: #fef3c7; color: #92400e; }
  .badge.preparing        { background: #ede9fe; color: #5b21b6; }
  .badge.ready_for_pickup { background: #fff7ed; color: #c2410c; font-weight: 700; }
  .badge.driver_assigned  { background: #f5f3ff; color: #5b21b6; font-weight: 700; }
  .badge.picked_up        { background: #ecfdf5; color: #065f46; }
  .badge.completed        { background: #dcfce7; color: #166534; }
  .badge.cancelled        { background: #fee2e2; color: #991b1b; }

  .action-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--gray-600);
    padding: 6px;
    margin: 0 4px;
    transition: color 0.2s;
    text-decoration: none;
  }

  .action-btn:hover { color: var(--primary); }

  .po-number {
    font-weight: 600;
    color: var(--primary);
    text-decoration: none;
  }

  .po-number:hover {
    text-decoration: underline;
  }
</style>

<div class="page-header">
  <h1 class="page-title">Purchase Orders</h1>
  <a href="<?= url('admin/purchase-orders/create') ?>" class="btn-primary">
    <i class="fas fa-plus"></i> Create Purchase Order
  </a>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET">
    <div class="filters-grid">
      <input
        type="text"
        name="search"
        placeholder="Search by PO number or supplier..."
        value="<?= htmlspecialchars($search ?? '') ?>"
        class="form-input"
      >
      <select name="status" class="form-select">
        <option value="">All Statuses</option>
        <option value="draft"            <?= ($status ?? '') === 'draft'            ? 'selected' : '' ?>>Draft</option>
        <option value="sent"             <?= ($status ?? '') === 'sent'             ? 'selected' : '' ?>>Sent</option>
        <option value="accepted"         <?= ($status ?? '') === 'accepted'         ? 'selected' : '' ?>>Accepted</option>
        <option value="preparing"        <?= ($status ?? '') === 'preparing'        ? 'selected' : '' ?>>Preparing</option>
        <option value="ready_for_pickup" <?= ($status ?? '') === 'ready_for_pickup' ? 'selected' : '' ?>>Ready for Pickup</option>
        <option value="picked_up"        <?= ($status ?? '') === 'picked_up'        ? 'selected' : '' ?>>Picked Up</option>
        <option value="completed"        <?= ($status ?? '') === 'completed'        ? 'selected' : '' ?>>Completed</option>
        <option value="cancelled"        <?= ($status ?? '') === 'cancelled'        ? 'selected' : '' ?>>Cancelled</option>
      </select>
      <button type="submit" class="btn-filter">
        <i class="fas fa-filter"></i> Filter
      </button>
    </div>
  </form>
</div>

<!-- Table -->
<div class="table-card">
  <table>
    <thead>
      <tr>
        <th>PO Number</th>
        <th>Supplier</th>
        <th>Order Date</th>
        <th>Expected Delivery</th>
        <th>Items</th>
        <th>Total Amount</th>
        <th>Status</th>
        <th style="text-align: right;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($purchaseOrders)): ?>
        <?php foreach ($purchaseOrders as $po): ?>
          <tr>
            <td>
              <a href="<?= url('admin/purchase-orders/view?id=' . $po['id']) ?>" class="po-number">
                <?= htmlspecialchars($po['po_number']) ?>
              </a>
            </td>
            <td>
              <strong><?= htmlspecialchars($po['supplier_company_name'] ?? $po['supplier_name'] ?? 'N/A') ?></strong>
            </td>
            <td><?= date('M d, Y', strtotime($po['order_date'])) ?></td>
            <td>
              <?= $po['expected_delivery_date'] ? date('M d, Y', strtotime($po['expected_delivery_date'])) : 'N/A' ?>
            </td>
            <td><?= $po['total_items'] ?? 0 ?> items</td>
            <td><?= currencySymbol() ?><?= number_format($po['total_amount'], 2) ?></td>
            <td>
              <?php
                $effStatus = ($po['status'] === 'ready_for_pickup' && !empty($po['assigned_driver_id']))
                    ? 'driver_assigned' : $po['status'];
                $statusLabels = [
                  'draft'           => 'Draft',
                  'sent'            => 'Sent',
                  'accepted'        => 'Accepted',
                  'preparing'       => 'Preparing',
                  'ready_for_pickup'=> '🚚 Ready for Pickup',
                  'driver_assigned' => '🚛 Driver Assigned',
                  'picked_up'       => 'Picked Up',
                  'completed'       => 'Completed',
                  'cancelled'       => 'Cancelled',
                ];
              ?>
              <span class="badge <?= $effStatus ?>">
                <?= $statusLabels[$effStatus] ?? ucfirst($effStatus) ?>
              </span>
            </td>
            <td style="text-align: right;">
              <a href="<?= url('admin/purchase-orders/view?id=' . $po['id']) ?>" class="action-btn" title="View">
                <i class="fas fa-eye"></i>
              </a>
              <?php if ($po['status'] !== 'completed' && $po['status'] !== 'cancelled'): ?>
                <a href="<?= url('admin/purchase-orders/receive?id=' . $po['id']) ?>" class="action-btn" title="Receive">
                  <i class="fas fa-box-open"></i>
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="8" style="text-align: center; padding: 40px; color: var(--gray-500);">
            <i class="fas fa-shopping-cart" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
            No purchase orders found
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
