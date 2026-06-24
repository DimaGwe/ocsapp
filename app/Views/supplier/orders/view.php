<?php
$pageTitle = 'Order #' . $order['po_number'];
require dirname(__DIR__) . '/layout-header.php';

$status = $order['status'];
$driverAssigned = !empty($order['assigned_driver_id']);
$driverAccepted = ($order['driver_acceptance_status'] ?? null) === 'accepted';

// Compute effective stage
if ($status === 'ready_for_pickup' && $driverAssigned) {
    $effectiveStatus = $driverAccepted ? 'driver_accepted' : 'driver_assigned';
} else {
    $effectiveStatus = $status;
}

$stages = $fr ? [
    'sent'            => ['label' => 'Envoyé',           'icon' => 'paper-plane'],
    'accepted'        => ['label' => 'Accepté',          'icon' => 'check-circle'],
    'preparing'       => ['label' => 'En préparation',   'icon' => 'box-open'],
    'ready_for_pickup'=> ['label' => 'Prêt ramassage',   'icon' => 'truck'],
    'driver_assigned' => ['label' => 'Livreur notifié',  'icon' => 'user-clock'],
    'driver_accepted' => ['label' => 'Livreur en route', 'icon' => 'truck-moving'],
    'picked_up'       => ['label' => 'Ramassé',          'icon' => 'shipping-fast'],
    'completed'       => ['label' => 'Complété',         'icon' => 'check-double'],
] : [
    'sent'            => ['label' => 'Sent',             'icon' => 'paper-plane'],
    'accepted'        => ['label' => 'Accepted',         'icon' => 'check-circle'],
    'preparing'       => ['label' => 'Preparing',        'icon' => 'box-open'],
    'ready_for_pickup'=> ['label' => 'Ready for Pickup', 'icon' => 'truck'],
    'driver_assigned' => ['label' => 'Driver Notified',  'icon' => 'user-clock'],
    'driver_accepted' => ['label' => 'Driver En Route',  'icon' => 'truck-moving'],
    'picked_up'       => ['label' => 'Picked Up',        'icon' => 'shipping-fast'],
    'completed'       => ['label' => 'Completed',        'icon' => 'check-double'],
];
$stageKeys = array_keys($stages);
$activeIdx = array_search($effectiveStatus, $stageKeys);
if ($activeIdx === false) $activeIdx = -1;
?>

<style>
  .breadcrumb { display: flex; gap: 8px; font-size: 14px; color: var(--gray-600); margin-bottom: 24px; }
  .breadcrumb a { color: var(--primary); text-decoration: none; }

  .order-header { background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
  .order-header h1 { font-size: 24px; color: var(--gray-700); margin-bottom: 20px; }

  /* ── Progress bar ── */
  .progress-track {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    margin-bottom: 28px;
  }
  .progress-track::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--gray-200);
    z-index: 0;
  }
  .stage-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    flex: 1;
    position: relative;
    z-index: 1;
  }
  .stage-dot {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    background: var(--gray-200);
    color: var(--gray-500);
    border: 3px solid white;
    box-shadow: 0 0 0 2px var(--gray-200);
    transition: all 0.3s;
  }
  .stage-dot.done   { background: var(--primary); color: white; box-shadow: 0 0 0 2px var(--primary); }
  .stage-dot.active { background: #3b82f6; color: white; box-shadow: 0 0 0 2px #3b82f6; }
  .stage-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-500);
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    line-height: 1.2;
    max-width: 70px;
  }
  .stage-label.done   { color: var(--primary); }
  .stage-label.active { color: #3b82f6; }

  /* Progress line fill */
  .progress-fill {
    position: absolute;
    top: 20px;
    left: 0;
    height: 3px;
    background: var(--primary);
    z-index: 0;
    transition: width 0.4s ease;
  }

  .order-meta { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
  .meta-item { display: flex; flex-direction: column; gap: 4px; }
  .meta-label { font-size: 12px; color: var(--gray-600); text-transform: uppercase; font-weight: 600; }
  .meta-value { font-size: 16px; color: var(--gray-700); font-weight: 600; }

  .badge { padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; align-self: flex-start; }
  .badge.draft          { background: #e5e7eb; color: #374151; }
  .badge.sent           { background: #dbeafe; color: #1e40af; }
  .badge.accepted       { background: #fef3c7; color: #92400e; }
  .badge.preparing      { background: #ede9fe; color: #5b21b6; }
  .badge.ready_for_pickup { background: #fff7ed; color: #c2410c; }
  .badge.driver_assigned  { background: #f5f3ff; color: #5b21b6; }
  .badge.driver_accepted  { background: #eff6ff; color: #1d4ed8; }

  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
  }
  .badge.picked_up        { background: #ecfdf5; color: #065f46; }
  .badge.completed      { background: #dcfce7; color: #166534; }
  .badge.cancelled      { background: #fee2e2; color: #991b1b; }

  .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; }
  .card-title { font-size: 18px; font-weight: 700; color: var(--gray-700); margin-bottom: 20px; }

  table { width: 100%; border-collapse: collapse; }
  thead { background: var(--gray-50); }
  th { padding: 12px; text-align: left; font-size: 12px; font-weight: 700; color: var(--gray-600); text-transform: uppercase; }
  td { padding: 16px 12px; border-top: 1px solid var(--gray-200); }

  .totals-section { background: var(--gray-50); padding: 20px; border-radius: 8px; margin-top: 20px; }
  .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
  .total-row.final { border-top: 2px solid var(--gray-700); margin-top: 8px; padding-top: 12px; font-size: 18px; font-weight: 700; }

  /* ── Action cards ── */
  .action-card { border-radius: 12px; padding: 24px; margin-bottom: 24px; }
  .action-card.blue   { background: #eff6ff; border: 1px solid #bfdbfe; }
  .action-card.purple { background: #f5f3ff; border: 1px solid #ddd6fe; }
  .action-card.orange { background: #fff7ed; border: 1px solid #fed7aa; }
  .action-card.green  { background: #f0fdf4; border: 1px solid #bbf7d0; }
  .action-card.gray   { background: #f9fafb; border: 1px solid #e5e7eb; }

  .action-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
  .action-card p  { font-size: 14px; color: var(--gray-600); margin-bottom: 16px; line-height: 1.5; }
  .action-card.blue   h3 { color: #1e40af; }
  .action-card.purple h3 { color: #5b21b6; }
  .action-card.orange h3 { color: #c2410c; }
  .action-card.green  h3 { color: #065f46; }
  .action-card.gray   h3 { color: #374151; }

  .order-actions { display: flex; gap: 12px; flex-wrap: wrap; }

  .btn { padding: 12px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
  .btn-accept  { background: linear-gradient(135deg, #00b207 0%, #008505 100%); color: white; }
  .btn-accept:hover  { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,178,7,0.3); }
  .btn-decline { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }
  .btn-decline:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239,68,68,0.3); }
  .btn-purple  { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; }
  .btn-purple:hover  { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(139,92,246,0.3); }
  .btn-orange  { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; }
  .btn-orange:hover  { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(249,115,22,0.3); }
  .btn-cancel  { background: var(--gray-200); color: var(--gray-700); }
  .btn-cancel:hover  { background: var(--gray-300); }

  .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
  .modal-overlay.active { display: flex; }
  .modal { background: white; border-radius: 12px; padding: 32px; max-width: 500px; width: 90%; }
  .modal h3 { margin-bottom: 16px; color: var(--gray-700); }
  .modal textarea { width: 100%; padding: 12px; border: 2px solid var(--gray-200); border-radius: 8px; font-family: inherit; font-size: 14px; resize: vertical; min-height: 100px; }
  .modal-actions { display: flex; gap: 12px; margin-top: 20px; justify-content: flex-end; }

  /* Table scroll wrapper */
  .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }

  /* ── Responsive ── */
  @media (max-width: 640px) {
    .order-header { padding: 16px; }
    .order-header h1 { font-size: 18px; margin-bottom: 14px; }

    /* Progress track: allow horizontal scroll on mobile */
    .progress-track {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 8px;
      justify-content: flex-start;
      gap: 0;
    }
    .stage-step { min-width: 64px; }
    .stage-dot { width: 32px; height: 32px; font-size: 12px; }
    .stage-label { font-size: 9px; max-width: 60px; }
    .progress-track::before { top: 16px; }
    .progress-fill { top: 16px; }

    .card { padding: 16px; }
    .card-title { font-size: 16px; }

    th { font-size: 11px; padding: 10px 8px; }
    td { padding: 12px 8px; font-size: 13px; }

    .totals-section { padding: 14px; }
    .total-row.final { font-size: 16px; }

    .action-card { padding: 16px; }
    .action-card h3 { font-size: 15px; }

    .btn { padding: 11px 18px; font-size: 13px; }
    .order-actions { flex-direction: column; }
    .order-actions .btn { width: 100%; justify-content: center; }

    /* Timer banner wrap on small screens */
    #timerBanner { flex-wrap: wrap; }

    /* Modal full-width on mobile */
    .modal { padding: 20px; width: 95%; }
    .modal-actions { flex-direction: column-reverse; }
    .modal-actions .btn { width: 100%; justify-content: center; }

    /* Meta grid single column */
    .order-meta { grid-template-columns: 1fr 1fr; }
  }

  @media (max-width: 400px) {
    .order-meta { grid-template-columns: 1fr; }
    .stage-step { min-width: 52px; }
    .stage-label { font-size: 8px; }
  }
</style>

<div class="breadcrumb">
  <a href="<?= url('supplier/orders') ?>"><?= $fr ? 'Bons de commande' : 'Purchase Orders' ?></a>
  <span>/</span>
  <span><?= htmlspecialchars($order['po_number']) ?></span>
</div>

<div class="order-header">
  <h1><?= $fr ? 'Bon de commande #' : 'Purchase Order #' ?><?= htmlspecialchars($order['po_number']) ?></h1>

  <?php if ($status !== 'draft' && $status !== 'cancelled'): ?>
  <div class="progress-track" id="progressTrack">
    <?php foreach ($stages as $key => $stage):
      $idx = array_search($key, $stageKeys);
      $cls = $idx < $activeIdx ? 'done' : ($idx === $activeIdx ? 'active' : '');
    ?>
      <div class="stage-step">
        <div class="stage-dot <?= $cls ?>">
          <i class="fas fa-<?= $stage['icon'] ?>"></i>
        </div>
        <span class="stage-label <?= $cls ?>"><?= $stage['label'] ?></span>
      </div>
    <?php endforeach; ?>
    <div class="progress-fill" id="progressFill"></div>
  </div>
  <?php endif; ?>

  <div class="order-meta">
    <div class="meta-item">
      <span class="meta-label"><?= $fr ? 'Statut' : 'Status' ?></span>
      <?php
        $badgeClass = $effectiveStatus;
        $badgeLabel = $fr ? match($effectiveStatus) {
          'accepted'         => 'Accepté',
          'preparing'        => 'En préparation',
          'ready_for_pickup' => 'Prêt pour ramassage',
          'driver_assigned'  => '🔔 Livreur notifié',
          'driver_accepted'  => '🚛 Livreur en route',
          'picked_up'        => 'Ramassé',
          'completed'        => 'Complété',
          'cancelled'        => 'Annulé',
          default            => ucfirst(str_replace('_', ' ', $effectiveStatus)),
        } : match($effectiveStatus) {
          'accepted'         => 'Accepted',
          'preparing'        => 'Preparing',
          'ready_for_pickup' => 'Ready for Pickup',
          'driver_assigned'  => '🔔 Driver Notified',
          'driver_accepted'  => '🚛 Driver En Route',
          'picked_up'        => 'Picked Up',
          'completed'        => 'Completed',
          'cancelled'        => 'Cancelled',
          default            => ucfirst(str_replace('_', ' ', $effectiveStatus)),
        };
      ?>
      <span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
    </div>

    <div class="meta-item">
      <span class="meta-label"><?= $fr ? 'Date de commande' : 'Order Date' ?></span>
      <span class="meta-value"><?= date('F d, Y', strtotime($order['order_date'])) ?></span>
    </div>

    <?php if ($order['expected_delivery_date']): ?>
      <div class="meta-item">
        <span class="meta-label"><?= $fr ? 'Livraison prévue' : 'Expected Delivery' ?></span>
        <span class="meta-value"><?= date('F d, Y', strtotime($order['expected_delivery_date'])) ?></span>
      </div>
    <?php endif; ?>

    <?php if (!empty($order['actual_delivery_date'])): ?>
      <div class="meta-item">
        <span class="meta-label"><?= $fr ? 'Date de réception' : 'Received Date' ?></span>
        <span class="meta-value"><?= date('F d, Y', strtotime($order['actual_delivery_date'])) ?></span>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Order Items -->
<div class="card">
  <h3 class="card-title"><?= $fr ? 'Articles commandés' : 'Order Items' ?></h3>
  <div class="table-scroll">
  <table>
    <thead>
      <tr>
        <th><?= $fr ? 'Produit' : 'Product' ?></th>
        <th>SKU</th>
        <th style="text-align: center;"><?= $fr ? 'Commandé' : 'Ordered' ?></th>
        <th style="text-align: center;"><?= $fr ? 'Reçu' : 'Received' ?></th>
        <th style="text-align: right;"><?= $fr ? 'Coût unitaire' : 'Unit Cost' ?></th>
        <th style="text-align: right;"><?= $fr ? 'Total' : 'Total' ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td><strong><?= htmlspecialchars($item['product_name'] ?? ($fr ? 'Produit' : 'Product')) ?></strong></td>
          <td><?= htmlspecialchars($item['sku'] ?? 'N/A') ?></td>
          <td style="text-align: center;"><?= $item['quantity_ordered'] ?></td>
          <td style="text-align: center;">
            <?= $item['quantity_received'] ?>
            <?php if ($item['quantity_received'] < $item['quantity_ordered']): ?>
              <span style="color: var(--warning); font-size: 12px;">
                (<?= $item['quantity_ordered'] - $item['quantity_received'] ?> <?= $fr ? 'en attente' : 'pending' ?>)
              </span>
            <?php endif; ?>
          </td>
          <td style="text-align: right;">$<?= number_format($item['unit_cost'], 2) ?></td>
          <td style="text-align: right;">$<?= number_format($item['total_cost'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>

  <div class="totals-section">
    <div class="total-row"><span><?= $fr ? 'Sous-total :' : 'Subtotal:' ?></span><span>$<?= number_format($order['subtotal'], 2) ?></span></div>
    <?php if ($order['shipping_cost'] > 0): ?>
      <div class="total-row"><span><?= $fr ? 'Livraison :' : 'Shipping:' ?></span><span>$<?= number_format($order['shipping_cost'], 2) ?></span></div>
    <?php endif; ?>
    <?php if (($order['tax_gst'] ?? 0) > 0): ?>
      <div class="total-row"><span>TPS (5%) :</span><span>$<?= number_format($order['tax_gst'], 2) ?></span></div>
    <?php endif; ?>
    <?php if (($order['tax_qst'] ?? 0) > 0): ?>
      <div class="total-row"><span>TVQ (9,975%) :</span><span>$<?= number_format($order['tax_qst'], 2) ?></span></div>
    <?php endif; ?>
    <div class="total-row final"><span><?= $fr ? 'Total :' : 'Total:' ?></span><span>$<?= number_format($order['total_amount'], 2) ?></span></div>
  </div>
</div>

<?php if ($order['notes']): ?>
  <div class="card">
    <h3 class="card-title"><?= $fr ? 'Notes' : 'Notes' ?></h3>
    <p style="color: var(--gray-700); line-height: 1.6;"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
  </div>
<?php endif; ?>

<?php
// ── Order Completion Timeline (distribution orders only) ──
$tlDeadline  = $order['order_deadline'] ?? null;
$tlSubmitted = $order['dr_submitted_at'] ?? null;
$tlType      = $order['delivery_type'] ?? null;
$tlStatus    = $order['status'];
$tlDrLinked  = !empty($order['distribution_request_id']);
$tlActive    = $tlDrLinked && $tlDeadline && !in_array($tlStatus, ['draft','cancelled','completed']);

if ($tlDrLinked && $tlDeadline && ($tlActive || in_array($tlStatus, ['completed']))):
    $deadlineTs  = strtotime($tlDeadline);
    $submittedTs = $tlSubmitted ? strtotime($tlSubmitted) : null;
    $totalSecs   = ($submittedTs && $deadlineTs) ? max(1, $deadlineTs - $submittedTs) : 0;
    $nowTs       = time();
    $done        = $tlStatus === 'completed';

    $typeConfig = match($tlType) {
        'express'  => ['label' => '⚡ ' . ($fr ? 'Express ASAP' : 'Express ASAP'), 'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca', 'promise' => $fr ? 'Livré dans les 2 heures suivant la soumission' : 'Delivered within 2 hours of submission'],
        'same_day' => ['label' => '☀️ ' . ($fr ? 'Même jour'    : 'Same Day'),     'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a', 'promise' => $fr ? 'Livré aujourd\'hui pendant les heures d\'affaires' : 'Delivered today during business hours'],
        default    => ['label' => '📅 ' . ($fr ? 'Planifié'     : 'Scheduled'),    'color' => '#4f46e5', 'bg' => '#eef2ff', 'border' => '#c7d2fe', 'promise' => $fr ? 'Livré à la date prévue' : 'Delivered on scheduled date'],
    };
    $tlC = $typeConfig;

    // Distance / ETA info
    $distKm = $order['delivery_distance'] ?? null;
    $distLabel = '';
    $etaLabel  = '';
    if ($distKm && is_numeric($distKm) && $distKm > 0) {
        $distLabel = number_format((float)$distKm, 1) . ' km';
        // Rough ETA: ~30 km/h average city speed
        $etaMins = (int)ceil($distKm / 30 * 60);
        $etaLabel = $etaMins >= 60 ? floor($etaMins/60).'h '.($etaMins%60).'m' : $etaMins.'min';
    }
    $delivAddr = trim(($order['delivery_street'] ?? '') . ', ' . ($order['delivery_city'] ?? '') . ($order['delivery_province'] ? ', '.$order['delivery_province'] : ''), ', ');
?>
<div style="background:<?= $tlC['bg'] ?>;border:1.5px solid <?= $tlC['border'] ?>;border-radius:12px;padding:20px 24px;margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:20px;"><?= $tlType === 'express' ? '⚡' : ($tlType === 'same_day' ? '☀️' : '📅') ?></span>
            <div>
                <div style="font-weight:700;font-size:15px;color:<?= $tlC['color'] ?>;"><?= $tlC['label'] ?> — Order Timeline</div>
                <div style="font-size:12px;color:#6b7280;margin-top:1px;"><?= $tlC['promise'] ?></div>
            </div>
        </div>
        <?php if ($done): ?>
            <span style="background:#d1fae5;color:#059669;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">✓ <?= $fr ? 'Complété' : 'Completed' ?></span>
        <?php else: ?>
            <span id="tlBadge" style="background:<?= $tlC['color'] ?>;color:white;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;"><?= $fr ? 'En cours' : 'In Progress' ?></span>
        <?php endif; ?>
    </div>

    <!-- Milestones row -->
    <div style="display:flex;align-items:center;gap:0;margin-bottom:14px;">
        <div style="display:flex;flex-direction:column;align-items:center;flex:0 0 auto;">
            <div style="width:32px;height:32px;border-radius:50%;background:<?= $tlC['color'] ?>;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-flag-checkered" style="color:white;font-size:13px;"></i>
            </div>
            <div style="font-size:10px;color:#6b7280;margin-top:4px;text-align:center;max-width:70px;"><?= $fr ? 'Soumis' : 'Submitted' ?><br><?= $submittedTs ? date('g:i A', $submittedTs) : '—' ?></div>
        </div>
        <div style="flex:1;height:6px;background:#e5e7eb;border-radius:3px;margin:0 4px;overflow:hidden;">
            <div id="tlBar" style="height:100%;border-radius:3px;background:<?= $tlC['color'] ?>;width:<?= $done ? '100' : min(100, round(max(0, $nowTs - ($submittedTs ?? $nowTs)) / $totalSecs * 100)) ?>%;"></div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:center;flex:0 0 auto;">
            <div style="width:32px;height:32px;border-radius:50%;background:<?= $done ? '#059669' : '#e5e7eb' ?>;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-<?= $done ? 'check' : 'map-marker-alt' ?>" style="color:<?= $done ? 'white' : '#9ca3af' ?>;font-size:13px;"></i>
            </div>
            <div style="font-size:10px;color:#6b7280;margin-top:4px;text-align:center;max-width:70px;"><?= $fr ? 'Limite' : 'Deadline' ?><br><?= date('g:i A', $deadlineTs) ?></div>
        </div>
    </div>

    <!-- Countdown or completed -->
    <?php if (!$done): ?>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:<?= ($distLabel || $delivAddr) ? '12px' : '0' ?>;">
        <i class="fas fa-hourglass-half" style="color:<?= $tlC['color'] ?>;"></i>
        <span id="tlCountdown" style="font-size:14px;font-weight:700;color:<?= $tlC['color'] ?>;"></span>
        <span style="font-size:13px;color:#6b7280;">· <?= $fr ? 'Limite :' : 'Deadline:' ?> <?= date('M j, Y \a\t g:i A', $deadlineTs) ?></span>
    </div>
    <script>
    (function(){
        const deadline  = <?= $deadlineTs * 1000 ?>;
        const submitted = <?= ($submittedTs ?? $nowTs) * 1000 ?>;
        const total     = <?= $totalSecs * 1000 ?>;
        const el  = document.getElementById('tlCountdown');
        const bar = document.getElementById('tlBar');
        const bdg = document.getElementById('tlBadge');
        if (!el) return;
        function fmt(s) {
            if (s < 0) return '00:00';
            const h = Math.floor(s/3600), m = Math.floor((s%3600)/60), sec = s%60;
            return h > 0 ? h+'h '+String(m).padStart(2,'0')+'m' : String(m).padStart(2,'0')+':'+String(sec).padStart(2,'0');
        }
        function tick() {
            const now = Date.now(), secsLeft = Math.floor((deadline-now)/1000);
            const pct = total > 0 ? Math.min(100,Math.round((now-submitted)/total*100)) : 100;
            if (bar) bar.style.width = pct+'%';
            if (secsLeft <= 0) {
                el.textContent = '<?= $fr ? 'Délai dépassé' : 'Deadline reached' ?>';
                if (bdg) { bdg.textContent='<?= $fr ? 'En retard' : 'Overdue' ?>'; bdg.style.background='#dc2626'; }
                if (bar) bar.style.background='#dc2626';
                return;
            }
            el.textContent = fmt(secsLeft)+'<?= $fr ? ' restant' : ' remaining' ?>';
            if (pct >= 80) {
                el.style.color='#dc2626';
                if (bar) bar.style.background='#dc2626';
                if (bdg) bdg.style.background='#dc2626';
            }
            setTimeout(tick, 1000);
        }
        tick();
    })();
    </script>
    <?php else: ?>
    <div style="font-size:13px;color:#059669;font-weight:600;margin-bottom:<?= ($distLabel || $delivAddr) ? '12px' : '0' ?>;">
        <i class="fas fa-check-circle"></i> <?= $fr ? 'Complété · Limite était' : 'Completed · Deadline was' ?> <?= date('M j, Y \a\t g:i A', $deadlineTs) ?>
    </div>
    <?php endif; ?>

    <!-- Distance & Delivery Address: hidden from supplier view -->
</div>
<?php endif; ?>

<!-- ── Stage Action Cards ── -->

<?php if ($status === 'sent'): ?>
  <?php
    $isExpress  = ($order['delivery_type'] ?? 'scheduled') === 'express';
    $deadline   = $order['confirmation_deadline'] ?? null;
    $deadlineTs = $deadline ? strtotime($deadline) : null;
  ?>
  <div class="action-card blue" id="reviewAcceptCard" style="<?= $isExpress ? 'border-color:#dc2626;background:#fff5f5;' : '' ?>">
    <?php if ($isExpress): ?>
      <h3 style="color:#dc2626;"><i class="fas fa-bolt"></i> <?= $fr ? 'Commande express — Confirmez maintenant' : 'Express Order — Confirm Now' ?></h3>
    <?php else: ?>
      <h3><i class="fas fa-clipboard-check"></i> <?= $fr ? 'Examiner et accepter — Fenêtre de 10 minutes' : 'Review &amp; Accept — 10 Minute Window' ?></h3>
    <?php endif; ?>

    <?php if ($deadlineTs): ?>
      <div id="timerBanner" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 14px;margin-bottom:14px;display:flex;align-items:center;gap:10px;transition:background .5s,border-color .5s;">
        <i class="fas fa-hourglass-half" id="timerIcon" style="color:#1d4ed8;font-size:18px;"></i>
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;color:#1e40af;">
            <?= $fr ? 'Répondez avant' : 'Respond by' ?> <?= date('g:i A', $deadlineTs) ?> — <?= $fr ? 'le système réassignera si aucune réponse' : 'system will reassign if no response' ?>
          </div>
          <div id="countdownTimer" style="font-size:16px;font-weight:800;color:#1d4ed8;margin-top:3px;letter-spacing:1px;"></div>
        </div>
      </div>
    <?php endif; ?>

    <p><?= $fr ? 'Examinez ce bon de commande et confirmez que vous pouvez l\'exécuter. Une fois accepté, vous commencerez à préparer les articles.' : "Review this purchase order and confirm you can fulfill it. Once accepted, you'll begin packing the items." ?></p>

    <?php if ($isExpress): ?>
    <div style="margin-bottom:14px;">
      <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;">
        <i class="fas fa-clock"></i> <?= $fr ? 'Quand serez-vous prêt pour le ramassage ?' : 'When will you be ready for pickup?' ?>
      </label>
      <div style="display:flex;gap:10px;">
        <button type="button" id="readyOpt1" onclick="selectReadyWindow(this, 15, 30)"
                style="flex:1;padding:12px 8px;border:2px solid #e5e7eb;border-radius:10px;background:#fff;font-size:14px;font-weight:600;color:#374151;cursor:pointer;transition:all 0.15s;text-align:center;">
          <div style="font-size:22px;margin-bottom:4px;">⚡</div>
          15 – 30 min
        </button>
        <button type="button" id="readyOpt2" onclick="selectReadyWindow(this, 30, 45)"
                style="flex:1;padding:12px 8px;border:2px solid #e5e7eb;border-radius:10px;background:#fff;font-size:14px;font-weight:600;color:#374151;cursor:pointer;transition:all 0.15s;text-align:center;">
          <div style="font-size:22px;margin-bottom:4px;">🕐</div>
          30 – 45 min
        </button>
      </div>
      <input type="hidden" id="readyByTime" name="ready_by_time" value="">
      <input type="hidden" id="readyByLabel" value="">
      <p id="readyByHint" style="font-size:12px;color:#6b7280;margin:6px 0 0;"><?= $fr ? 'Sélectionnez une fenêtre — cela nous aide à dépêcher le livreur au bon moment.' : 'Select a window — this helps us dispatch the driver at the right time.' ?></p>
    </div>
    <?php endif; ?>

    <div class="order-actions">
      <button type="button" class="btn btn-accept" id="acceptBtn" onclick="<?= $isExpress ? 'acceptWithReadyBy()' : "submitAction('" . url('supplier/orders/accept') . "', 'Accept this purchase order?')" ?>">
        <i class="fas fa-check"></i> <?= $fr ? 'Accepter la commande' : 'Accept Order' ?>
      </button>
      <button type="button" class="btn btn-decline" onclick="openDeclineModal()">
        <i class="fas fa-times"></i> <?= $fr ? 'Refuser la commande' : 'Decline Order' ?>
      </button>
    </div>
  </div>

  <?php if ($deadlineTs): ?>
  <script>
  (function() {
    const deadline = <?= $deadlineTs * 1000 ?>;
    const el      = document.getElementById('countdownTimer');
    const banner  = document.getElementById('timerBanner');
    const icon    = document.getElementById('timerIcon');
    const card    = document.getElementById('reviewAcceptCard');
    if (!el) return;

    function update() {
      const diff = Math.floor((deadline - Date.now()) / 1000);

      if (diff <= 0) {
        el.textContent = '<?= $fr ? 'Délai expiré — commande en cours de réassignation' : 'Time expired — order being reassigned' ?>';
        el.style.color = '#dc2626';
        if (banner) { banner.style.background = '#fef2f2'; banner.style.borderColor = '#fecaca'; }
        if (icon)   icon.className = 'fas fa-times-circle';
        return;
      }

      const m = Math.floor(diff / 60);
      const s = diff % 60;
      el.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')} remaining`;

      // 8 min warning (≤120s left before 8-min mark = diff <= 120)
      if (diff <= 120) {
        // under 2 min — critical red
        el.style.color = '#dc2626';
        if (banner) { banner.style.background = '#fef2f2'; banner.style.borderColor = '#fecaca'; }
        if (icon)   { icon.style.color = '#dc2626'; icon.className = 'fas fa-exclamation-circle'; }
        if (card)   card.style.borderColor = '#dc2626';
      } else if (diff <= 300) {
        // under 5 min — amber warning
        el.style.color = '#d97706';
        if (banner) { banner.style.background = '#fffbeb'; banner.style.borderColor = '#fde68a'; }
        if (icon)   { icon.style.color = '#d97706'; icon.className = 'fas fa-hourglass-end'; }
        if (card)   card.style.borderColor = '#f59e0b';
      } else {
        el.style.color = '#1d4ed8';
      }

      setTimeout(update, 1000);
    }
    update();
  })();
  </script>
  <?php endif; ?>

<?php elseif ($status === 'accepted'): ?>
  <?php $ocsappPaid = empty($order['distribution_request_id']) || !empty($order['admin_paid_at']); ?>
  <div class="action-card <?= $ocsappPaid ? 'purple' : 'gray' ?>">
    <h3><i class="fas fa-box-open"></i> <?= $fr ? 'Commencer la préparation' : 'Start Preparing' ?></h3>
    <?php if (!$ocsappPaid): ?>
    <div style="background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;gap:10px;">
      <i class="fas fa-clock" style="color:#d97706;font-size:20px;"></i>
      <div>
        <div style="font-size:13px;font-weight:700;color:#92400e;">⏳ <?= $fr ? 'En attente du paiement d\'OCSApp' : 'Awaiting Payment from OCSApp' ?></div>
        <div style="font-size:12px;color:#b45309;margin-top:2px;"><?= $fr ? 'Vous serez notifié dès qu\'OCSApp envoie votre paiement et vous pourrez commencer à préparer cette commande.' : 'You will be notified once OCSApp sends your payment and you can begin preparing this order.' ?></div>
      </div>
    </div>
    <div class="order-actions">
      <button type="button" disabled style="opacity:0.5;cursor:not-allowed;padding:12px 24px;border-radius:8px;background:#e5e7eb;color:#6b7280;font-weight:600;border:none;">
        <i class="fas fa-box-open"></i> <?= $fr ? 'Commencer la préparation' : 'Start Preparing' ?>
      </button>
    </div>
    <?php else: ?>
    <?php if (!empty($order['ready_by_time'])): ?>
    <div style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:8px;padding:10px 14px;margin-bottom:14px;display:flex;align-items:center;gap:10px;">
      <i class="fas fa-calendar-check" style="color:#7c3aed;"></i>
      <div>
        <div style="font-size:13px;font-weight:700;color:#5b21b6;">📦 <?= $fr ? 'Prêt pour ramassage avant :' : 'Ready for pickup by:' ?> <?= date('M j, Y \a\t g:i A', strtotime($order['ready_by_time'])) ?></div>
        <div style="font-size:12px;color:#7c3aed;margin-top:2px;"><?= $fr ? 'Veuillez avoir tous les articles emballés et prêts avant cette heure.' : 'Please have all items packed and ready before this time.' ?></div>
      </div>
    </div>
    <?php endif; ?>
    <p><?= $fr ? 'Paiement reçu d\'OCSApp. Quand vous êtes prêt à commencer la préparation, cliquez ci-dessous.' : "Payment received from OCSApp. When you're ready to start picking and packing, click below." ?></p>
    <div class="order-actions">
      <button type="button" class="btn btn-purple" onclick="submitAction('<?= url('supplier/orders/start-preparing') ?>', 'Start preparing this order?')">
        <i class="fas fa-box-open"></i> <?= $fr ? 'Commencer la préparation' : 'Start Preparing' ?>
      </button>
      <button type="button" class="btn btn-cancel" onclick="openIssueModal()" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;">
        <i class="fas fa-exclamation-triangle"></i> <?= $fr ? 'Signaler un problème' : 'Report Issue' ?>
      </button>
    </div>
    <?php endif; ?>
  </div>

<?php elseif ($status === 'preparing'): ?>
  <div class="action-card orange">
    <h3><i class="fas fa-truck"></i> <?= $fr ? 'Prêt pour le ramassage ?' : 'Ready for Pickup?' ?></h3>
    <?php if (!empty($order['ready_by_time'])): ?>
    <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:10px 14px;margin-bottom:14px;display:flex;align-items:center;gap:10px;">
      <i class="fas fa-clock" style="color:#ea580c;"></i>
      <div>
        <div style="font-size:13px;font-weight:700;color:#c2410c;">⏰ <?= $fr ? 'Doit être prêt avant :' : 'Must be ready by:' ?> <?= date('M j, Y \a\t g:i A', strtotime($order['ready_by_time'])) ?></div>
        <div style="font-size:12px;color:#ea580c;margin-top:2px;"><?= $fr ? 'Marquez prêt avant cette heure pour que le livreur puisse être dépêché selon le planning.' : 'Mark ready before this time so the driver can be dispatched on schedule.' ?></div>
      </div>
    </div>
    <?php endif; ?>
    <p><?= $fr ? 'Une fois tous les articles emballés et prêts, cliquez sur le bouton ci-dessous. L\'administrateur sera immédiatement notifié pour assigner un livreur.' : 'Once all items are packed and ready to go, click the button below. Admin will be notified immediately to assign a driver for pickup.' ?></p>
    <div class="order-actions">
      <button type="button" class="btn btn-orange" onclick="submitAction('<?= url('supplier/orders/ready-for-pickup') ?>', '<?= $fr ? 'Marquer cette commande comme prête pour le ramassage ? L\'administrateur sera alerté.' : 'Mark this order as ready for pickup? Admin will be alerted to assign a driver.' ?>')">
        <i class="fas fa-truck"></i> <?= $fr ? 'Marquer prêt pour ramassage' : 'Mark Ready for Pickup' ?>
      </button>
      <button type="button" class="btn btn-cancel" onclick="openIssueModal()" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;">
        <i class="fas fa-exclamation-triangle"></i> <?= $fr ? 'Signaler un problème' : 'Report Issue' ?>
      </button>
    </div>
  </div>

<?php elseif ($status === 'ready_for_pickup' && !$driverAssigned): ?>
  <div class="action-card orange">
    <h3><i class="fas fa-clock"></i> <?= $fr ? 'En attente d\'un livreur' : 'Awaiting Driver Assignment' ?></h3>
    <p><?= $fr ? 'Votre commande est emballée et en attente. L\'administrateur a été notifié et un livreur sera assigné sous peu. Vous recevrez une notification quand un livreur est en route.' : "Your order is packed and waiting. Admin has been notified and a driver will be assigned shortly. You'll receive a notification when a driver is on the way." ?></p>
  </div>

<?php elseif ($status === 'ready_for_pickup' && $driverAssigned): ?>
  <?php if ($driverAccepted): ?>
  <div class="action-card" style="background:#eff6ff; border:1px solid #93c5fd;">
    <h3 style="color:#1d4ed8;"><i class="fas fa-truck-moving"></i> <?= $fr ? 'Livreur en route' : 'Driver En Route' ?></h3>
    <p><?= $fr ? 'Votre livreur a accepté ce ramassage et se dirige vers votre emplacement. Veuillez avoir tous les articles emballés et prêts à votre porte.' : 'Your driver has accepted this pickup and is heading to your location. Please have all items packed and ready at your door.' ?></p>
    <?php if (!empty($order['driver_name'])): ?>
    <div style="display:flex; align-items:center; gap:14px; background:white; border-radius:10px; padding:14px 18px; margin-top:8px; border:1px solid #bfdbfe;">
      <div style="width:42px; height:42px; border-radius:50%; background:#dbeafe; display:flex; align-items:center; justify-content:center; font-size:18px; color:#1d4ed8;">
        <i class="fas fa-user"></i>
      </div>
      <div>
        <div style="font-weight:700; color:#1f2937; font-size:15px;"><?= htmlspecialchars($order['driver_name']) ?></div>
        <?php if (!empty($order['driver_phone'])): ?>
          <div style="color:#6b7280; font-size:13px; margin-top:2px;"><i class="fas fa-phone" style="margin-right:5px;"></i><?= htmlspecialchars($order['driver_phone']) ?></div>
        <?php endif; ?>
      </div>
      <div style="margin-left:auto; display:flex; align-items:center; gap:5px; background:#dbeafe; color:#1d4ed8; font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:0.4px;">
        <span style="width:7px;height:7px;background:#2563eb;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite;"></span> <?= $fr ? 'En route' : 'En Route' ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php else: ?>
  <div class="action-card purple">
    <h3><i class="fas fa-user-clock"></i> <?= $fr ? 'Livreur notifié — En attente d\'acceptation' : 'Driver Notified — Awaiting Acceptance' ?></h3>
    <p><?= $fr ? 'Un livreur a été notifié de cette mission de ramassage. Il confirmera sous peu. Veuillez vous assurer que les articles sont emballés et prêts.' : 'A driver has been notified of this pickup assignment. They will confirm shortly. Please ensure items are packed and ready.' ?></p>
    <?php if (!empty($order['driver_name'])): ?>
    <div style="display:flex; align-items:center; gap:14px; background:white; border-radius:10px; padding:14px 18px; margin-top:8px; border:1px solid #ddd6fe;">
      <div style="width:42px; height:42px; border-radius:50%; background:#ede9fe; display:flex; align-items:center; justify-content:center; font-size:18px; color:#6d28d9;">
        <i class="fas fa-user"></i>
      </div>
      <div>
        <div style="font-weight:700; color:#1f2937; font-size:15px;"><?= htmlspecialchars($order['driver_name']) ?></div>
        <?php if (!empty($order['driver_phone'])): ?>
          <div style="color:#6b7280; font-size:13px; margin-top:2px;"><i class="fas fa-phone" style="margin-right:5px;"></i><?= htmlspecialchars($order['driver_phone']) ?></div>
        <?php endif; ?>
      </div>
      <div style="margin-left:auto; background:#fef3c7; color:#d97706; font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:0.4px;">
        <i class="fas fa-hourglass-half" style="font-size:9px;"></i> <?= $fr ? 'En attente' : 'Awaiting' ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

<?php elseif ($status === 'picked_up'): ?>
  <div class="action-card green">
    <h3><i class="fas fa-shipping-fast"></i> <?= $fr ? 'Commande ramassée' : 'Order Picked Up' ?></h3>
    <p><?= $fr ? 'Un livreur a récupéré cette commande. Elle est maintenant en route vers la destination.' : 'A driver has collected this order. It is now on its way to the destination.' ?></p>
  </div>

<?php elseif ($status === 'completed'): ?>
  <div class="action-card green">
    <h3><i class="fas fa-check-double"></i> <?= $fr ? 'Commande complétée' : 'Order Completed' ?></h3>
    <p><?= $fr ? 'Ce bon de commande a été entièrement reçu et complété. Merci !' : 'This purchase order has been fully received and completed. Thank you!' ?></p>
  </div>

<?php elseif ($status === 'cancelled'): ?>
  <div class="action-card gray">
    <h3><i class="fas fa-ban"></i> <?= $fr ? 'Commande annulée' : 'Order Cancelled' ?></h3>
    <?php if ($order['decline_reason']): ?>
      <p><?= $fr ? 'Motif :' : 'Reason:' ?> <?= htmlspecialchars($order['decline_reason']) ?></p>
    <?php else: ?>
      <p><?= $fr ? 'Cette commande a été annulée.' : 'This order has been cancelled.' ?></p>
    <?php endif; ?>
  </div>
<?php endif; ?>

<!-- Document Downloads -->
<?php if (!in_array($status, ['sent', 'declined'])): ?>
<div style="background:white;border-radius:12px;padding:20px 24px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:24px;">
  <h3 style="font-size:15px;font-weight:700;color:var(--gray-700);margin:0 0 14px;display:flex;align-items:center;gap:8px;">
    <i class="fas fa-file-pdf" style="color:var(--primary);"></i> Documents
  </h3>
  <div style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="<?= url('supplier/orders/download-pdf?id=' . $order['id'] . '&type=po') ?>"
       target="_blank"
       style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;color:#374151;font-size:13px;font-weight:600;text-decoration:none;transition:all 0.2s;"
       onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
       onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
      <i class="fas fa-file-alt"></i> <?= $fr ? 'Télécharger BC' : 'Download PO' ?>
    </a>
    <?php if (!empty($order['so_number'])): ?>
    <a href="<?= url('supplier/orders/download-pdf?id=' . $order['id'] . '&type=so') ?>"
       target="_blank"
       style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;color:#15803d;font-size:13px;font-weight:600;text-decoration:none;transition:all 0.2s;"
       onmouseover="this.style.borderColor='var(--primary)';this.style.background='#dcfce7'"
       onmouseout="this.style.borderColor='#bbf7d0';this.style.background='#f0fdf4'">
      <i class="fas fa-receipt"></i> <?= $fr ? 'Télécharger BV' : 'Download SO' ?> <span style="font-family:'SF Mono',monospace;font-size:11px;margin-left:4px;opacity:0.8;"><?= htmlspecialchars($order['so_number']) ?></span>
    </a>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- Report Issue Modal -->
<div class="modal-overlay" id="issueModal">
  <div class="modal">
    <h3><i class="fas fa-exclamation-triangle" style="color:#f97316;margin-right:8px;"></i><?= $fr ? 'Signaler un problème' : 'Report an Issue' ?></h3>
    <p style="color:var(--gray-600);margin-bottom:20px;"><?= $fr ? 'Notre équipe sera notifiée immédiatement et vous contactera.' : 'Our team will be notified immediately and will follow up with you.' ?></p>
    <form method="POST" action="<?= url('supplier/orders/report-issue') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
      <div style="margin-bottom:14px;">
        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;"><?= $fr ? 'Type de problème' : 'Issue Type' ?></label>
        <select name="issue_type" required style="width:100%;padding:10px 12px;border:2px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;">
          <option value="">— <?= $fr ? 'Sélectionner' : 'Select' ?> —</option>
          <option value="partial_stock"><?= $fr ? 'Stock partiel — Impossible de tout exécuter' : 'Partial Stock — Cannot fully fulfill' ?></option>
          <option value="delay"><?= $fr ? 'Retard — Ne sera pas prêt à temps' : 'Delay — Will not be ready on time' ?></option>
          <option value="out_of_stock"><?= $fr ? 'Rupture de stock — Impossible à exécuter' : 'Out of Stock — Cannot fulfill at all' ?></option>
          <option value="damaged_goods"><?= $fr ? 'Marchandises endommagées' : 'Damaged Goods' ?></option>
          <option value="other"><?= $fr ? 'Autre problème' : 'Other Issue' ?></option>
        </select>
      </div>
      <div style="margin-bottom:4px;">
        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;"><?= $fr ? 'Détails' : 'Details' ?></label>
        <textarea name="message" placeholder="<?= $fr ? 'Décrivez le problème, les articles affectés, ou ce que vous pouvez partiellement exécuter...' : 'Describe the issue, affected items, or what you can partially fulfill...' ?>" required style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:14px;resize:vertical;min-height:100px;box-sizing:border-box;"></textarea>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel" onclick="closeIssueModal()"><?= $fr ? 'Annuler' : 'Cancel' ?></button>
        <button type="submit" class="btn" style="background:#f97316;color:white;"><i class="fas fa-paper-plane"></i> <?= $fr ? 'Envoyer le rapport' : 'Send Report' ?></button>
      </div>
    </form>
  </div>
</div>

<!-- Decline Reason Modal -->
<div class="modal-overlay" id="declineModal">
  <div class="modal">
    <h3><?= $fr ? 'Refuser le bon de commande' : 'Decline Purchase Order' ?></h3>
    <p style="color: var(--gray-600); margin-bottom: 20px;"><?= $fr ? 'Veuillez indiquer la raison du refus de cette commande :' : 'Please provide a reason for declining this order:' ?></p>
    <form id="declineForm" method="POST" action="<?= url('supplier/orders/decline') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
      <textarea name="decline_reason" id="declineReason" placeholder="<?= $fr ? 'Entrez la raison du refus de cette commande...' : 'Enter reason for declining this order...' ?>" required></textarea>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel" onclick="closeDeclineModal()"><?= $fr ? 'Annuler' : 'Cancel' ?></button>
        <button type="submit" class="btn btn-decline"><i class="fas fa-times"></i> <?= $fr ? 'Confirmer le refus' : 'Confirm Decline' ?></button>
      </div>
    </form>
  </div>
</div>

<script>
// Express ready-window selection
function selectReadyWindow(btn, minMinutes, maxMinutes) {
  // Highlight selected button, reset the other
  ['readyOpt1','readyOpt2'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) {
      el.style.border      = '2px solid #e5e7eb';
      el.style.background  = '#fff';
      el.style.color       = '#374151';
    }
  });
  btn.style.border     = '2px solid #00b207';
  btn.style.background = '#f0fdf4';
  btn.style.color      = '#15803d';

  // Compute ready_by_time = now + maxMinutes (upper bound of window)
  var t = new Date(Date.now() + maxMinutes * 60 * 1000);
  var pad = function(n) { return String(n).padStart(2, '0'); };
  var iso = t.getFullYear() + '-' + pad(t.getMonth()+1) + '-' + pad(t.getDate())
          + 'T' + pad(t.getHours()) + ':' + pad(t.getMinutes());
  document.getElementById('readyByTime').value  = iso;
  document.getElementById('readyByLabel').value = minMinutes + '–' + maxMinutes + ' min';

  var hint = document.getElementById('readyByHint');
  if (hint) hint.textContent = '<?= $fr ? '✓ Le livreur sera dépêché dans ' : '✓ Driver will be dispatched within ' ?>' + minMinutes + '–' + maxMinutes + '<?= $fr ? ' minutes.' : ' minutes.' ?>';
}

// Express accept — includes ready_by_time computed from window selection
function acceptWithReadyBy() {
  const readyBy = document.getElementById('readyByTime');
  const label   = document.getElementById('readyByLabel');
  if (!readyBy || !readyBy.value) {
    alert('<?= $fr ? 'Veuillez sélectionner quand vous serez prêt pour le ramassage avant d\'accepter.' : 'Please select when you will be ready for pickup before accepting.' ?>');
    return;
  }
  if (!confirm('<?= $fr ? 'Accepter cette commande express ? Vous vous engagez à être prêt dans ' : 'Accept this express order? You are committing to be ready in ' ?>' + (label ? label.value : readyBy.value) + '<?= $fr ? '.' : '.' ?>')) return;
  sessionStorage.setItem('sup_order_scroll_<?= $order['id'] ?>', window.scrollY);
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '<?= url('supplier/orders/accept') ?>';
  const csrf = document.querySelector('#declineForm input[name="<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>"]');
  if (csrf) {
    const c = document.createElement('input');
    c.type = 'hidden'; c.name = csrf.name; c.value = csrf.value;
    form.appendChild(c);
  }
  const id = document.createElement('input');
  id.type = 'hidden'; id.name = 'order_id'; id.value = '<?= $order['id'] ?>';
  form.appendChild(id);
  const rbt = document.createElement('input');
  rbt.type = 'hidden'; rbt.name = 'ready_by_time'; rbt.value = readyBy.value;
  form.appendChild(rbt);
  document.body.appendChild(form);
  var btn = document.getElementById('acceptBtn');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= $fr ? 'Traitement...' : 'Processing...' ?>';
    btn.style.opacity = '0.7';
    btn.style.cursor = 'not-allowed';
  }
  form.submit();
}

// Restore scroll position after page reload
(function() {
  var key = 'sup_order_scroll_<?= $order['id'] ?>';
  var saved = sessionStorage.getItem(key);
  if (saved) {
    sessionStorage.removeItem(key);
    window.scrollTo({ top: parseInt(saved), behavior: 'instant' });
  }
})();

// Generic POST helper — reuses CSRF from the decline form
function submitAction(url, confirmMsg) {
  if (!confirm(confirmMsg)) return;
  // Save scroll position before reload
  sessionStorage.setItem('sup_order_scroll_<?= $order['id'] ?>', window.scrollY);
  // Show spinner on accept button if this is an accept action
  var btn = document.getElementById('acceptBtn');
  if (btn && url.indexOf('accept') !== -1) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= $fr ? 'Traitement...' : 'Processing...' ?>';
    btn.style.opacity = '0.7';
    btn.style.cursor = 'not-allowed';
  }
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = url;
  const csrf = document.querySelector('#declineForm input[name="<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>"]');
  if (csrf) {
    const c = document.createElement('input');
    c.type = 'hidden'; c.name = csrf.name; c.value = csrf.value;
    form.appendChild(c);
  }
  const id = document.createElement('input');
  id.type = 'hidden'; id.name = 'order_id'; id.value = '<?= $order['id'] ?>';
  form.appendChild(id);
  document.body.appendChild(form);
  form.submit();
}

function openDeclineModal()  { document.getElementById('declineModal').classList.add('active'); document.getElementById('declineReason').focus(); }
function closeDeclineModal() { document.getElementById('declineModal').classList.remove('active'); document.getElementById('declineReason').value = ''; }
document.getElementById('declineModal').addEventListener('click', function(e) { if (e.target === this) closeDeclineModal(); });

function openIssueModal()  { document.getElementById('issueModal').classList.add('active'); }
function closeIssueModal() { document.getElementById('issueModal').classList.remove('active'); }
document.getElementById('issueModal').addEventListener('click', function(e) { if (e.target === this) closeIssueModal(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && document.getElementById('declineModal').classList.contains('active')) closeDeclineModal(); });

// Progress bar fill width
(function() {
  const track = document.getElementById('progressTrack');
  const fill  = document.getElementById('progressFill');
  if (!track || !fill) return;
  const dots = track.querySelectorAll('.stage-dot');
  const total = dots.length;
  const activeIdx = <?= $activeIdx >= 0 ? $activeIdx : 0 ?>;
  if (total <= 1) return;
  const pct = (activeIdx / (total - 1)) * 100;
  fill.style.width = pct + '%';
})();
</script>

<?php if (!in_array($status, ['completed', 'cancelled'])): ?>
<style>
#poStatusToast {
    position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px);
    background: #1e293b; color: #fff; padding: 14px 24px; border-radius: 10px;
    font-size: 14px; font-weight: 600; box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    display: flex; align-items: center; gap: 10px; z-index: 9999;
    transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), opacity 0.3s;
    opacity: 0; pointer-events: none;
}
#poStatusToast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
</style>
<div id="poStatusToast"><span>🔄</span> <span id="poToastMsg"><?= $fr ? 'Commande mise à jour — rechargement…' : 'Order updated — reloading…' ?></span></div>
<script>
(function() {
    var orderId       = <?= (int)$order['id'] ?>;
    var curStatus     = <?= json_encode($status) ?>;
    var curDriver     = <?= json_encode($order['driver_acceptance_status'] ?? '') ?>;
    var curAdminPaid  = <?= json_encode(!empty($order['admin_paid_at']) ? $order['admin_paid_at'] : '') ?>;
    var endpoint      = '<?= url('api/supplier/order/status') ?>?id=' + orderId;
    var reloading     = false;

    function showToast(msg) {
        var t = document.getElementById('poStatusToast');
        document.getElementById('poToastMsg').textContent = msg;
        t.classList.add('show');
    }

    function poll() {
        if (reloading) return;
        fetch(endpoint)
            .then(function(r) { return r.ok ? r.json() : null; })
            .then(function(data) {
                if (!data || !data.success) return;
                var changed = data.status !== curStatus ||
                              (data.driver_acceptance_status || '') !== curDriver ||
                              (data.admin_paid_at || '') !== curAdminPaid;
                if (changed) {
                    reloading = true;
                    var label = data.status.replace(/_/g, ' ').replace(/\b\w/g, function(c) { return c.toUpperCase(); });
                    showToast('<?= $fr ? 'Commande mise à jour : ' : 'Order updated: ' ?>' + label + '<?= $fr ? ' — rechargement…' : ' — reloading…' ?>');
                    sessionStorage.setItem('sup_order_scroll_<?= $order['id'] ?>', window.scrollY);
                    setTimeout(function() { location.reload(); }, 1800);
                }
            })
            .catch(function() {});
    }

    setInterval(poll, 4000);
})();
</script>
<?php endif; ?>

<?php require dirname(__DIR__) . '/layout-footer.php'; ?>
