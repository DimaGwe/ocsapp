<?php
$pageTitle = 'Purchase Order #' . $po['po_number'];
$currentPage = 'purchase-orders';
ob_start();
?>

<style>
  .page-header {
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .breadcrumb {
    display: flex;
    gap: 8px;
    font-size: 14px;
    color: var(--gray-600);
  }

  .breadcrumb a {
    color: var(--primary);
    text-decoration: none;
  }

  .header-actions {
    display: flex;
    gap: 12px;
    margin-top: 16px;
  }

  .btn {
    padding: 10px 20px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .btn-success {
    background: #10b981;
    color: white;
  }

  .po-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
  }

  .card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
  }

  .card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 20px;
  }

  .info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
  }

  .info-label {
    font-weight: 600;
    color: var(--gray-600);
  }

  .info-value {
    color: var(--dark);
  }

  .badge {
    padding: 6px 16px;
    border-radius: var(--radius-full);
    font-size: 13px;
    font-weight: 600;
  }

  .badge.draft            { background: #e5e7eb; color: #374151; }
  .badge.sent             { background: #dbeafe; color: #1e40af; }
  .badge.accepted         { background: #fef3c7; color: #92400e; }
  .badge.preparing        { background: #ede9fe; color: #5b21b6; }
  .badge.ready_for_pickup { background: #fff7ed; color: #c2410c; font-weight: 700; }
  .badge.driver_assigned  { background: #f5f3ff; color: #5b21b6; font-weight: 700; }
  .badge.driver_accepted  { background: #eff6ff; color: #1d4ed8; font-weight: 700; }
  .badge.picked_up        { background: #ecfdf5; color: #065f46; }
  .badge.completed        { background: #dcfce7; color: #166534; }
  .badge.cancelled        { background: #fee2e2; color: #991b1b; }

  /* ── PO Stage Progress ── */
  .po-progress { display:flex; align-items:flex-start; justify-content:space-between; position:relative; margin-bottom:28px; }
  .po-progress::before { content:''; position:absolute; top:20px; left:0; right:0; height:3px; background:var(--gray-200); z-index:0; }
  .po-progress-fill { position:absolute; top:20px; left:0; height:3px; background:var(--primary); z-index:0; transition:width 0.4s; }
  .po-stage { display:flex; flex-direction:column; align-items:center; gap:6px; flex:1; position:relative; z-index:1; }
  .po-dot { width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px; background:var(--gray-200); color:var(--gray-500); border:3px solid white; box-shadow:0 0 0 2px var(--gray-200); transition:all 0.3s; }
  .po-dot.done   { background:var(--primary); color:white; box-shadow:0 0 0 2px var(--primary); }
  .po-dot.active { background:#3b82f6; color:white; box-shadow:0 0 0 2px #3b82f6; }
  .po-label { font-size:10px; font-weight:600; color:var(--gray-500); text-align:center; text-transform:uppercase; letter-spacing:0.4px; line-height:1.2; max-width:64px; }
  .po-label.done   { color:var(--primary); }
  .po-label.active { color:#3b82f6; }

  .items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
  }

  .items-table th {
    background: var(--gray-50);
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    border-bottom: 2px solid var(--border);
  }

  .items-table td {
    padding: 12px;
    border-bottom: 1px solid var(--border);
  }

  .progress-bar {
    background: var(--gray-200);
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
  }

  .progress-fill {
    background: #10b981;
    height: 100%;
    transition: width 0.3s;
  }

  .totals-section {
    margin-top: 24px;
    padding: 20px;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
  }

  .total-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
  }

  .total-row.final {
    border-top: 2px solid var(--dark);
    margin-top: 8px;
    padding-top: 12px;
    font-size: 18px;
    font-weight: 700;
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
  }
</style>

<div class="page-header">
  <h1 class="page-title">Purchase Order #<?= htmlspecialchars($po['po_number']) ?></h1>
  <div class="breadcrumb">
    <a href="<?= url('admin/purchase-orders') ?>">Purchase Orders</a>
    <span>/</span>
    <span><?= htmlspecialchars($po['po_number']) ?></span>
  </div>

  <div class="header-actions">
    <?php if (in_array($po['status'], ['accepted', 'preparing', 'ready_for_pickup', 'picked_up'])): ?>
      <a href="<?= url('admin/purchase-orders/receive?id=' . $po['id']) ?>" class="btn btn-success">
        <i class="fas fa-box-open"></i> Receive Shipment
      </a>
    <?php endif; ?>
    <button class="btn btn-secondary" onclick="window.print()">
      <i class="fas fa-print"></i> Print
    </button>
  </div>
</div>

<?php
$poStatus = $po['status'];
$poDriverAssigned = !empty($po['assigned_driver_id']);
$poDriverAccepted = ($po['driver_acceptance_status'] ?? null) === 'accepted';

// Effective status: split driver assignment into two visual stages
if ($poStatus === 'ready_for_pickup' && $poDriverAssigned) {
    $poEffectiveStatus = $poDriverAccepted ? 'driver_accepted' : 'driver_assigned';
} else {
    $poEffectiveStatus = $poStatus;
}

$poStages = [
    'sent'            => ['label' => 'Sent',             'icon' => 'paper-plane'],
    'accepted'        => ['label' => 'Accepted',         'icon' => 'check-circle'],
    'preparing'       => ['label' => 'Preparing',        'icon' => 'box-open'],
    'ready_for_pickup'=> ['label' => 'Ready for Pickup', 'icon' => 'truck'],
    'driver_assigned' => ['label' => 'Driver Notified',  'icon' => 'user-clock'],
    'driver_accepted' => ['label' => 'Driver En Route',  'icon' => 'truck-moving'],
    'picked_up'       => ['label' => 'Picked Up',        'icon' => 'shipping-fast'],
    'completed'       => ['label' => 'Completed',        'icon' => 'check-double'],
];
$poStageKeys = array_keys($poStages);
$poActiveIdx = array_search($poEffectiveStatus, $poStageKeys);
if ($poActiveIdx === false) $poActiveIdx = -1;

$poStatusLabels = [
    'draft'           => 'Draft',
    'sent'            => 'Sent',
    'accepted'        => 'Accepted',
    'preparing'       => 'Preparing',
    'ready_for_pickup'=> '🚚 Ready for Pickup',
    'driver_assigned' => '🔔 Driver Notified',
    'driver_accepted' => '🚛 Driver En Route',
    'picked_up'       => 'Picked Up',
    'completed'       => 'Completed',
    'cancelled'       => 'Cancelled',
];
?>

<?php if (!in_array($poStatus, ['draft', 'cancelled'])): ?>
<div class="card" style="margin-bottom:24px; padding:24px;">
  <h3 class="card-title" style="margin-bottom:20px;">Order Progress</h3>
  <div class="po-progress" id="poProgressTrack">
    <?php foreach ($poStages as $key => $stage):
      $idx = array_search($key, $poStageKeys);
      $cls = $idx < $poActiveIdx ? 'done' : ($idx === $poActiveIdx ? 'active' : '');
    ?>
      <div class="po-stage">
        <div class="po-dot <?= $cls ?>"><i class="fas fa-<?= $stage['icon'] ?>"></i></div>
        <span class="po-label <?= $cls ?>"><?= $stage['label'] ?></span>
      </div>
    <?php endforeach; ?>
    <div class="po-progress-fill" id="poProgressFill"></div>
  </div>
</div>
<?php endif; ?>

<div class="po-grid">
  <!-- Main Content -->
  <div>
    <!-- PO Details -->
    <div class="card">
      <h3 class="card-title">Purchase Order Details</h3>

      <div class="info-row">
        <span class="info-label">PO Number:</span>
        <span class="info-value"><?= htmlspecialchars($po['po_number']) ?></span>
      </div>

      <div class="info-row">
        <span class="info-label">Status:</span>
        <span class="badge <?= $poEffectiveStatus ?>"><?= $poStatusLabels[$poEffectiveStatus] ?? ucfirst($poEffectiveStatus) ?></span>
      </div>

      <div class="info-row">
        <span class="info-label">Order Date:</span>
        <span class="info-value"><?= date('F d, Y', strtotime($po['order_date'])) ?></span>
      </div>

      <?php if ($po['expected_delivery_date']): ?>
        <div class="info-row">
          <span class="info-label">Expected Delivery:</span>
          <span class="info-value"><?= date('F d, Y', strtotime($po['expected_delivery_date'])) ?></span>
        </div>
      <?php endif; ?>

      <?php if (!empty($po['received_date'])): ?>
        <div class="info-row">
          <span class="info-label">Received Date:</span>
          <span class="info-value"><?= date('F d, Y', strtotime($po['received_date'])) ?></span>
        </div>
      <?php endif; ?>

      <?php if ($po['notes']): ?>
        <div class="info-row" style="border: none; flex-direction: column; gap: 8px;">
          <span class="info-label">Notes:</span>
          <span class="info-value"><?= nl2br(htmlspecialchars($po['notes'])) ?></span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Order Items -->
    <div class="card">
      <h3 class="card-title">Order Items</h3>

      <table class="items-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>SKU</th>
            <th style="text-align: center;">Ordered</th>
            <th style="text-align: center;">Received</th>
            <th style="text-align: right;">Unit Cost</th>
            <th style="text-align: right;">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $allReceived = true;
          foreach ($items as $item):
            $percentReceived = $item['quantity_ordered'] > 0
              ? ($item['quantity_received'] / $item['quantity_ordered']) * 100
              : 0;

            if ($item['quantity_received'] < $item['quantity_ordered']) {
              $allReceived = false;
            }
          ?>
            <tr>
              <td><strong><?= htmlspecialchars($item['product_name']) ?></strong></td>
              <td><?= htmlspecialchars($item['product_sku'] ?? 'N/A') ?></td>
              <td style="text-align: center;"><?= $item['quantity_ordered'] ?></td>
              <td style="text-align: center;">
                <div><?= $item['quantity_received'] ?></div>
                <div class="progress-bar" style="margin-top: 4px;">
                  <div class="progress-fill" style="width: <?= $percentReceived ?>%;"></div>
                </div>
              </td>
              <td style="text-align: right;"><?= currencySymbol() ?><?= number_format($item['unit_cost'], 2) ?></td>
              <td style="text-align: right;"><?= currencySymbol() ?><?= number_format($item['total_cost'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="totals-section">
        <div class="total-row">
          <span>Subtotal:</span>
          <span><?= currencySymbol() ?><?= number_format($po['subtotal'], 2) ?></span>
        </div>

        <?php if ($po['shipping_cost'] > 0): ?>
          <div class="total-row">
            <span>Shipping:</span>
            <span><?= currencySymbol() ?><?= number_format($po['shipping_cost'], 2) ?></span>
          </div>
        <?php endif; ?>

        <div class="total-row">
          <span>GST (5%):</span>
          <span><?= currencySymbol() ?><?= number_format($po['tax_gst'] ?? 0, 2) ?></span>
        </div>
        <div class="total-row">
          <span>QST (9.975%):</span>
          <span><?= currencySymbol() ?><?= number_format($po['tax_qst'] ?? 0, 2) ?></span>
        </div>

        <div class="total-row final">
          <span>Total:</span>
          <span><?= currencySymbol() ?><?= number_format($po['total_amount'], 2) ?></span>
        </div>
      </div>

      <?php if (in_array($po['status'], ['draft', 'sent']) && ($po['tax_amount'] ?? 0) == 0 && $po['subtotal'] > 0): ?>
      <form method="POST" action="<?= url('admin/purchase-orders/update-tax') ?>" style="margin-top:10px; display:flex; align-items:center; gap:10px;">
        <input type="hidden" name="<?= htmlspecialchars(env('CSRF_TOKEN_NAME','_csrf_token')) ?>" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="po_id" value="<?= $po['id'] ?>">
        <span style="font-size:12px;color:#d97706;"><i class="fas fa-exclamation-triangle"></i> No taxes applied</span>
        <button type="submit" class="btn btn-sm btn-secondary" style="white-space:nowrap;">Apply GST + QST</button>
      </form>
      <?php endif; ?>
    </div>
  </div>

  <!-- Sidebar -->
  <div>
    <!-- Supplier Info -->
    <div class="card">
      <h3 class="card-title">Supplier Information</h3>

      <div class="info-row">
        <span class="info-label">Company:</span>
        <span class="info-value">
          <strong><?= htmlspecialchars($po['supplier_company_name'] ?? $po['supplier_name']) ?></strong>
        </span>
      </div>

      <?php if ($po['supplier_email']): ?>
        <div class="info-row">
          <span class="info-label">Email:</span>
          <span class="info-value">
            <a href="mailto:<?= htmlspecialchars($po['supplier_email']) ?>">
              <?= htmlspecialchars($po['supplier_email']) ?>
            </a>
          </span>
        </div>
      <?php endif; ?>

      <?php if ($po['supplier_phone']): ?>
        <div class="info-row">
          <span class="info-label">Phone:</span>
          <span class="info-value"><?= htmlspecialchars($po['supplier_phone']) ?></span>
        </div>
      <?php endif; ?>

      <?php if ($po['supplier_address']): ?>
        <div class="info-row" style="border: none; flex-direction: column; gap: 8px;">
          <span class="info-label">Address:</span>
          <span class="info-value">
            <?= htmlspecialchars($po['supplier_address']) ?><br>
            <?= htmlspecialchars($po['supplier_city']) ?>, <?= htmlspecialchars($po['supplier_province']) ?><br>
            <?= htmlspecialchars($po['supplier_postal_code']) ?>
          </span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <div class="card">
      <h3 class="card-title">Actions</h3>

      <?php if ($po['status'] === 'draft'): ?>
        <button class="btn btn-primary" style="width: 100%; margin-bottom: 12px;" onclick="updateStatus('sent')">
          <i class="fas fa-paper-plane"></i> Mark as Sent
        </button>
      <?php endif; ?>

      <?php
        // Check for existing invoice (auto-generated on PO acceptance)
        $existingInvoice = null;
        try {
            $invCheck = \Database::getConnection()->prepare("SELECT id, invoice_number, status, total_amount, balance_due FROM supplier_invoices WHERE po_id = ?");
            $invCheck->execute([$po['id']]);
            $existingInvoice = $invCheck->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
      ?>
      <?php if ($existingInvoice): ?>
        <a href="<?= url('admin/payables/view?id=' . $existingInvoice['id']) ?>" class="btn btn-primary" style="width:100%;margin-bottom:12px;text-decoration:none;justify-content:center;">
          <i class="fas fa-file-invoice-dollar"></i> View Invoice <?= htmlspecialchars($existingInvoice['invoice_number']) ?>
        </a>
        <?php if ($existingInvoice['status'] !== 'paid'): ?>
          <div style="text-align:center;font-size:13px;color:#991b1b;margin-bottom:12px;font-weight:500;">
            <i class="fas fa-exclamation-circle"></i> Balance: $<?= number_format((float)$existingInvoice['balance_due'], 2) ?>
          </div>
        <?php else: ?>
          <div style="text-align:center;font-size:13px;color:#065f46;margin-bottom:12px;font-weight:500;">
            <i class="fas fa-check-circle"></i> Fully Paid
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ($po['status'] === 'ready_for_pickup'): ?>
        <?php if (!empty($assignedDriverName)): ?>
          <?php if ($poDriverAccepted): ?>
          <div style="background:#eff6ff;border:1px solid #93c5fd;border-radius:8px;padding:12px;margin-bottom:12px;">
            <div style="font-size:13px;font-weight:700;color:#1d4ed8;margin-bottom:6px;">
              <i class="fas fa-truck-moving"></i> Driver En Route
            </div>
            <div style="font-size:14px;font-weight:600;color:#1e40af;"><?= htmlspecialchars($assignedDriverName) ?></div>
            <?php if (!empty($po['driver_assigned_at'])): ?>
              <div style="font-size:12px;color:#6b7280;margin-top:2px;">Assigned <?= date('M d, g:i A', strtotime($po['driver_assigned_at'])) ?></div>
            <?php endif; ?>
            <div style="margin-top:8px;display:inline-flex;align-items:center;gap:5px;background:#dbeafe;color:#1d4ed8;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.4px;">
              <span style="width:7px;height:7px;background:#2563eb;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite;"></span> Accepted &amp; Heading Over
            </div>
          </div>
          <?php else: ?>
          <div style="background:#faf5ff;border:1px solid #c4b5fd;border-radius:8px;padding:12px;margin-bottom:12px;">
            <div style="font-size:13px;font-weight:700;color:#5b21b6;margin-bottom:6px;">
              <i class="fas fa-user-clock"></i> Driver Notified
            </div>
            <div style="font-size:14px;font-weight:600;color:#6d28d9;"><?= htmlspecialchars($assignedDriverName) ?></div>
            <?php if (!empty($po['driver_assigned_at'])): ?>
              <div style="font-size:12px;color:#6b7280;margin-top:2px;">Assigned <?= date('M d, g:i A', strtotime($po['driver_assigned_at'])) ?></div>
            <?php endif; ?>
            <div style="margin-top:8px;display:inline-flex;align-items:center;gap:5px;background:#ede9fe;color:#5b21b6;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.4px;">
              <i class="fas fa-hourglass-half" style="font-size:9px;"></i> Awaiting Acceptance
            </div>
          </div>
          <?php endif; ?>
          <button class="btn btn-success" style="width:100%;margin-bottom:12px;" onclick="confirmMarkPickedUp()">
            <i class="fas fa-shipping-fast"></i> Mark as Picked Up
          </button>
        <?php else: ?>
          <button class="btn btn-primary" style="width:100%;margin-bottom:12px;background:#7c3aed;" onclick="openAssignModal()">
            <i class="fas fa-user-tag"></i> Assign Driver for Pickup
          </button>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ($po['status'] !== 'completed' && $po['status'] !== 'cancelled'): ?>
        <button class="btn btn-secondary" style="width: 100%; background: #ef4444; color: white;" onclick="updateStatus('cancelled')">
          <i class="fas fa-times"></i> Cancel Order
        </button>
      <?php endif; ?>
    </div>

    <?php if ($poDriverAccepted): ?>
    <!-- Message Driver Card -->
    <div class="card" style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);color:white;border:none;">
      <h3 class="card-title" style="color:white;margin-bottom:8px;">
        <i class="fas fa-comment-alt" style="margin-right:8px;"></i>Message Driver
      </h3>
      <p style="font-size:13px;opacity:0.85;margin:0 0 14px;">
        Send an in-app notification to <?= htmlspecialchars($assignedDriverName) ?> on their ODA device.
      </p>
      <button onclick="openNotifyDriverModal()" style="width:100%;padding:10px;border-radius:8px;border:none;background:rgba(255,255,255,0.2);color:white;font-size:14px;font-weight:600;cursor:pointer;">
        <i class="fas fa-bell"></i> Send Message
      </button>
    </div>
    <?php endif; ?>

    <?php if (!empty($driverMessages)): ?>
    <!-- Driver Message Log -->
    <div class="card" id="driverMessageLog">
      <h3 class="card-title" style="margin-bottom:14px;">
        <i class="fas fa-history" style="margin-right:8px;color:#6b7280;"></i>Message Log
        <span style="font-size:12px;font-weight:500;color:#6b7280;margin-left:6px;"><?= count($driverMessages) ?> sent</span>
      </h3>
      <div style="display:flex;flex-direction:column;gap:10px;">
        <?php foreach ($driverMessages as $msg): ?>
          <?php
            $typeColor = match($msg['type']) {
              'urgent'  => ['bg' => '#fef2f2', 'border' => '#fca5a5', 'badge' => '#dc2626', 'label' => 'Urgent'],
              'warning' => ['bg' => '#fffbeb', 'border' => '#fcd34d', 'badge' => '#d97706', 'label' => 'Warning'],
              default   => ['bg' => '#eff6ff', 'border' => '#93c5fd', 'badge' => '#2563eb', 'label' => 'Info'],
            };
            $sentBy = trim(($msg['first_name'] ?? '') . ' ' . ($msg['last_name'] ?? '')) ?: 'Admin';
            $sentAt = date('M j, g:i A', strtotime($msg['created_at']));
            $readAt = $msg['read_at'] ? date('M j, g:i A', strtotime($msg['read_at'])) : null;
          ?>
          <div style="background:<?= $typeColor['bg'] ?>;border:1px solid <?= $typeColor['border'] ?>;border-radius:10px;padding:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
              <span style="font-size:11px;font-weight:700;color:<?= $typeColor['badge'] ?>;text-transform:uppercase;letter-spacing:0.4px;">
                <?= $typeColor['label'] ?>
              </span>
              <?php if ($readAt): ?>
                <span style="font-size:11px;color:#6b7280;" title="Read at <?= $readAt ?>">
                  <i class="fas fa-check-double" style="color:#10b981;"></i> Read
                </span>
              <?php else: ?>
                <span style="font-size:11px;color:#9ca3af;">
                  <i class="fas fa-clock"></i> Unread
                </span>
              <?php endif; ?>
            </div>
            <p style="margin:0 0 6px;font-size:13px;color:#1f2937;line-height:1.4;"><?= htmlspecialchars($msg['message']) ?></p>
            <div style="font-size:11px;color:#6b7280;">
              <?= htmlspecialchars($sentBy) ?> &middot; <?= $sentAt ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<?php if ($po['status'] === 'ready_for_pickup' && empty($assignedDriverName)): ?>
<!-- Assign Driver Modal -->
<div id="assignDriverModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:16px;padding:32px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
    <h3 style="margin:0 0 20px;font-size:20px;font-weight:700;">Assign Driver for Pickup</h3>
    <p style="font-size:14px;color:#6b7280;margin-bottom:20px;">
      PO #<?= htmlspecialchars($po['po_number']) ?> is ready at
      <strong><?= htmlspecialchars($po['supplier_company_name'] ?? $po['supplier_name']) ?></strong>.
      Select a driver to collect it.
    </p>

    <div style="margin-bottom:16px;">
      <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Driver</label>
      <select id="driverSelect" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
        <option value="">— Select a driver —</option>
        <?php foreach ($drivers as $drv): ?>
          <?php
            $avail = $drv['availability'];
            $availLabel = match($avail) {
              'available' => '🟢 Available',
              'busy'      => '🟡 Busy',
              'break'     => '🟠 On Break',
              default     => '⚫ Offline',
            };
          ?>
          <option value="<?= $drv['id'] ?>">
            <?= htmlspecialchars($drv['first_name'] . ' ' . $drv['last_name']) ?> — <?= $availLabel ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="margin-bottom:24px;">
      <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Notes for Driver (optional)</label>
      <textarea id="driverNotes" rows="3" placeholder="e.g. Ring buzzer, unit 4B, ask for Maria…" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical;box-sizing:border-box;"></textarea>
    </div>

    <div style="display:flex;gap:12px;justify-content:flex-end;">
      <button onclick="closeAssignModal()" style="padding:10px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:14px;font-weight:600;cursor:pointer;">Cancel</button>
      <button onclick="submitAssignDriver()" id="assignBtn" style="padding:10px 20px;border-radius:8px;border:none;background:#7c3aed;color:white;font-size:14px;font-weight:600;cursor:pointer;">
        <i class="fas fa-user-tag"></i> Assign Driver
      </button>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if ($poDriverAccepted): ?>
<!-- Notify Driver Modal -->
<div id="notifyDriverModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:16px;padding:32px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
    <h3 style="margin:0 0 6px;font-size:20px;font-weight:700;">Message Driver</h3>
    <p style="font-size:13px;color:#6b7280;margin:0 0 20px;">
      PO #<?= htmlspecialchars($po['po_number']) ?> &mdash; <?= htmlspecialchars($assignedDriverName) ?>
    </p>

    <div style="margin-bottom:16px;">
      <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Urgency</label>
      <select id="notifyPoType" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
        <option value="info">Info — general note</option>
        <option value="warning">Warning — something to watch out for</option>
        <option value="urgent">Urgent — requires immediate action</option>
      </select>
    </div>

    <div style="margin-bottom:24px;">
      <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Message</label>
      <textarea id="notifyPoMessage" rows="4" placeholder="e.g. Missing 2x product SKU-1234 — skip or call before leaving…"
        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical;box-sizing:border-box;"></textarea>
    </div>

    <div id="notifyPoError" style="display:none;color:#dc2626;font-size:13px;margin-bottom:12px;"></div>

    <div style="display:flex;gap:12px;justify-content:flex-end;">
      <button onclick="closeNotifyDriverModal()" style="padding:10px 20px;border-radius:8px;border:1px solid #d1d5db;background:white;font-size:14px;font-weight:600;cursor:pointer;">Cancel</button>
      <button onclick="submitNotifyDriver()" id="notifyPoBtn" style="padding:10px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:14px;font-weight:600;cursor:pointer;">
        <i class="fas fa-bell"></i> Send
      </button>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const csrfName  = '_csrf_token';

function updateStatus(newStatus) {
  if (!confirm(`Are you sure you want to change the status to "${newStatus}"?`)) return;

  fetch('<?= url('admin/purchase-orders/update-status') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, id: <?= $po['id'] ?>, status: newStatus })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) { location.reload(); }
    else { alert('Error: ' + data.message); }
  })
  .catch(() => alert('Error updating status'));
}

function openAssignModal() {
  const modal = document.getElementById('assignDriverModal');
  if (modal) { modal.style.display = 'flex'; }
}

function closeAssignModal() {
  const modal = document.getElementById('assignDriverModal');
  if (modal) { modal.style.display = 'none'; }
}

function submitAssignDriver() {
  const driverId = document.getElementById('driverSelect')?.value;
  if (!driverId) { alert('Please select a driver.'); return; }

  const notes = document.getElementById('driverNotes')?.value || '';
  const btn   = document.getElementById('assignBtn');
  btn.disabled = true;
  btn.textContent = 'Assigning…';

  fetch('<?= url('admin/purchase-orders/assign-driver') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, po_id: <?= $po['id'] ?>, driver_id: driverId, notes: notes })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert('✅ ' + data.message);
      location.reload();
    } else {
      alert('Error: ' + data.message);
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-user-tag"></i> Assign Driver';
    }
  })
  .catch(() => {
    alert('Network error — please try again.');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-user-tag"></i> Assign Driver';
  });
}

function confirmMarkPickedUp() {
  if (!confirm('Mark this order as picked up by the driver?')) return;

  fetch('<?= url('admin/purchase-orders/mark-picked-up') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, po_id: <?= $po['id'] ?> })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) { location.reload(); }
    else { alert('Error: ' + data.message); }
  })
  .catch(() => alert('Error updating status'));
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
  const modal = document.getElementById('assignDriverModal');
  if (modal && e.target === modal) { closeAssignModal(); }
});

// Progress bar fill
(function() {
  const track = document.getElementById('poProgressTrack');
  const fill  = document.getElementById('poProgressFill');
  if (!track || !fill) return;
  const dots  = track.querySelectorAll('.po-dot');
  const total = dots.length;
  const activeIdx = <?= $poActiveIdx >= 0 ? $poActiveIdx : 0 ?>;
  if (total <= 1) return;
  fill.style.width = ((activeIdx / (total - 1)) * 100) + '%';
})();

// Message Driver (PO)
function openNotifyDriverModal() {
  const m = document.getElementById('notifyDriverModal');
  if (m) { m.style.display = 'flex'; }
}
function closeNotifyDriverModal() {
  const m = document.getElementById('notifyDriverModal');
  if (m) { m.style.display = 'none'; }
  document.getElementById('notifyPoMessage').value = '';
  document.getElementById('notifyPoError').style.display = 'none';
}
async function submitNotifyDriver() {
  const message = document.getElementById('notifyPoMessage').value.trim();
  const type    = document.getElementById('notifyPoType').value;
  const errEl   = document.getElementById('notifyPoError');
  const btn     = document.getElementById('notifyPoBtn');

  errEl.style.display = 'none';
  if (!message) { errEl.textContent = 'Please enter a message.'; errEl.style.display = 'block'; return; }

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…';

  try {
    const res = await fetch('<?= url('admin/purchase-orders/' . $po['id'] . '/notify-driver') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ [csrfName]: csrfToken, message, type })
    });
    const data = await res.json();
    if (data.success) {
      closeNotifyDriverModal();
      // Reload page so message log updates
      window.location.reload();
    } else {
      errEl.textContent = data.error || 'Failed to send message.';
      errEl.style.display = 'block';
    }
  } catch (e) {
    errEl.textContent = 'Network error — please try again.';
    errEl.style.display = 'block';
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-bell"></i> Send';
}

document.addEventListener('click', function(e) {
  const nd = document.getElementById('notifyDriverModal');
  if (nd && e.target === nd) closeNotifyDriverModal();
});
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
