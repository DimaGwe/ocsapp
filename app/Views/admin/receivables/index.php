<?php
$pageTitle   = 'Receivables';
$currentPage = 'receivables';
ob_start();
?>

<style>
  /* ---- Layout ---- */
  .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 14px; }
  .page-title  { font-size: 26px; font-weight: 700; color: var(--dark); }
  .header-actions { display: flex; gap: 10px; }

  /* ---- Stats ---- */
  .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px; }
  .stat-card {
    background: white; border-radius: var(--radius-xl); padding: 22px;
    box-shadow: var(--shadow-sm); border-left: 4px solid #6366f1;
  }
  .stat-card.green  { border-left-color: #10b981; }
  .stat-card.amber  { border-left-color: #f59e0b; }
  .stat-card.red    { border-left-color: #ef4444; }
  .stat-label { font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 6px; }
  .stat-value { font-size: 24px; font-weight: 700; color: #111827; font-family: 'SF Mono', 'Cascadia Code', monospace; }
  .stat-sub   { font-size: 11px; color: #9ca3af; margin-top: 4px; }

  /* ---- Filters ---- */
  .filters-card { background: white; border-radius: var(--radius-xl); padding: 18px 20px; margin-bottom: 22px; box-shadow: var(--shadow-sm); }
  .filters-row  { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
  .form-select, .form-input {
    padding: 8px 12px; border: 2px solid #e5e7eb; border-radius: var(--radius-md);
    font-size: 13px; color: #374151; background: white;
  }
  .form-select:focus, .form-input:focus { outline: none; border-color: var(--primary); }
  .form-select { min-width: 150px; }
  .form-input  { min-width: 140px; }
  .btn-filter {
    padding: 8px 18px; background: var(--primary); color: white; border: none;
    border-radius: var(--radius-md); cursor: pointer; font-weight: 600; font-size: 13px;
  }
  .btn-export {
    padding: 8px 16px; background: white; color: #374151; border: 2px solid #e5e7eb;
    border-radius: var(--radius-md); cursor: pointer; font-weight: 600; font-size: 13px;
    text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
  }
  .btn-export:hover { border-color: var(--primary); color: var(--primary); }

  /* ---- Table ---- */
  .table-card { background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); overflow: hidden; }
  table { width: 100%; border-collapse: collapse; }
  th { padding: 13px 14px; text-align: left; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; background: #f9fafb; border-bottom: 2px solid #e5e7eb; }
  td { padding: 13px 14px; font-size: 13px; color: #374151; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: #fafafa; }

  /* ---- Badges ---- */
  .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
  .badge-distribution { background: #ede9fe; color: #5b21b6; }
  .badge-marketplace  { background: #ccfbf1; color: #065f46; }
  .badge-paid         { background: #d1fae5; color: #065f46; }
  .badge-outstanding  { background: #fef3c7; color: #92400e; }
  .badge-refunded     { background: #fee2e2; color: #991b1b; }

  /* ---- Amounts ---- */
  .amount { font-family: 'SF Mono', 'Cascadia Code', monospace; font-weight: 700; font-size: 14px; }
  .amount-paid        { color: #059669; }
  .amount-outstanding { color: #d97706; }
  .amount-refunded    { color: #dc2626; }

  /* ---- Breakdown tooltip ---- */
  .breakdown-toggle { background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 13px; padding: 0; }
  .breakdown-toggle:hover { color: #374151; }
  .breakdown-detail { display: none; font-size: 11px; color: #6b7280; margin-top: 4px; line-height: 1.6; white-space: nowrap; }
  .breakdown-detail.open { display: block; }

  /* ---- Action buttons ---- */
  .btn-sm {
    padding: 5px 12px; font-size: 12px; font-weight: 600; border-radius: var(--radius-md);
    text-decoration: none; display: inline-flex; align-items: center; gap: 5px; cursor: pointer;
    border: 2px solid transparent; transition: opacity .15s;
  }
  .btn-sm:hover { opacity: .85; }
  .btn-view    { background: #f3f4f6; color: #374151; border-color: #e5e7eb; }
  .btn-pay     { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
  .btn-refund  { background: none; color: #9ca3af; border: none; font-size: 11px; padding: 4px 6px; }
  .btn-refund:hover { color: #dc2626; }

  /* ---- Payer cell ---- */
  .payer-name  { font-weight: 600; color: #1f2937; }
  .payer-email { font-size: 11px; color: #9ca3af; margin-top: 2px; }

  /* ---- Modal ---- */
  .modal-overlay {
    display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5);
    z-index: 9999; align-items: center; justify-content: center;
  }
  .modal-overlay.open { display: flex; }
  .modal-box {
    background: white; border-radius: var(--radius-xl); padding: 28px 32px;
    width: 100%; max-width: 460px; box-shadow: 0 20px 60px rgba(0,0,0,.25);
  }
  .modal-title { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 20px; }
  .modal-field { margin-bottom: 16px; }
  .modal-label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .4px; }
  .modal-input, .modal-select {
    width: 100%; padding: 9px 13px; border: 2px solid #e5e7eb; border-radius: var(--radius-md);
    font-size: 14px; color: #111827; box-sizing: border-box;
  }
  .modal-input:focus, .modal-select:focus { outline: none; border-color: var(--primary); }
  .modal-actions { display: flex; gap: 10px; margin-top: 22px; justify-content: flex-end; }
  .btn-modal-cancel { padding: 9px 20px; background: #f3f4f6; color: #374151; border: none; border-radius: var(--radius-md); cursor: pointer; font-weight: 600; font-size: 13px; }
  .btn-modal-submit { padding: 9px 22px; background: var(--primary); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-weight: 600; font-size: 13px; }
  .modal-warning { background: #fef3c7; border: 1px solid #fcd34d; border-radius: var(--radius-md); padding: 10px 14px; font-size: 12px; color: #92400e; margin-bottom: 16px; }

  /* ---- Empty state ---- */
  .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
  .empty-state i { font-size: 44px; margin-bottom: 14px; display: block; }

  /* ---- Pagination ---- */
  .pagination-wrap { padding: 18px 20px; border-top: 1px solid #f3f4f6; }

  @media (max-width: 900px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 600px) {
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .filters-row { flex-direction: column; align-items: stretch; }
  }
</style>

<?php
// Build export URL preserving filters
$exportParams = array_filter([
    'type'      => $filters['type']     ?? '',
    'method'    => $filters['method']   ?? '',
    'status'    => $filters['status']   ?? '',
    'search'    => $filters['search']   ?? '',
    'date_from' => $filters['dateFrom'] ?? '',
    'date_to'   => $filters['dateTo']   ?? '',
]);
$exportUrl = url('admin/receivables/export') . ($exportParams ? '?' . http_build_query($exportParams) : '');
?>

<!-- Header -->
<div class="page-header">
  <h1 class="page-title">
    <i class="fas fa-hand-holding-usd" style="color:var(--primary);margin-right:8px;"></i>
    Receivables
  </h1>
  <div class="header-actions">
    <a href="<?= htmlspecialchars($exportUrl) ?>" class="btn-export">
      <i class="fas fa-download"></i> Export CSV
    </a>
  </div>
</div>

<!-- Summary Cards -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label">Total Received</div>
    <div class="stat-value">$<?= number_format($stats['totalReceived'] ?? 0, 2) ?></div>
    <div class="stat-sub">All time, all sources</div>
  </div>
  <div class="stat-card green">
    <div class="stat-label">This Month</div>
    <div class="stat-value" style="color:#059669;">$<?= number_format($stats['thisMonth'] ?? 0, 2) ?></div>
    <div class="stat-sub"><?= date('F Y') ?></div>
  </div>
  <div class="stat-card amber">
    <div class="stat-label">Outstanding</div>
    <div class="stat-value" style="color:#d97706;">$<?= number_format($stats['outstanding'] ?? 0, 2) ?></div>
    <div class="stat-sub"><?= (int)($stats['outstandingCount'] ?? 0) ?> pending payment link<?= (int)($stats['outstandingCount'] ?? 0) !== 1 ? 's' : '' ?></div>
  </div>
  <div class="stat-card red">
    <div class="stat-label">Total Refunded</div>
    <div class="stat-value" style="color:#dc2626;">$<?= number_format($stats['totalRefunded'] ?? 0, 2) ?></div>
    <div class="stat-sub">Across all sources</div>
  </div>
</div>

<!-- Flash Messages -->
<?php if ($flash = getFlash('success')): ?>
<div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:8px;padding:12px 16px;margin-bottom:18px;color:#065f46;font-size:13px;font-weight:600;">
  <i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>
<?php if ($flash = getFlash('error')): ?>
<div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin-bottom:18px;color:#991b1b;font-size:13px;font-weight:600;">
  <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>

<!-- Filters -->
<div class="filters-card">
  <form method="GET" action="<?= url('admin/receivables') ?>">
    <div class="filters-row">
      <select name="type" class="form-select">
        <option value="all"          <?= ($filters['type'] ?? '') === 'all'          ? 'selected' : '' ?>>All Types</option>
        <option value="distribution" <?= ($filters['type'] ?? '') === 'distribution' ? 'selected' : '' ?>>Distribution B2B</option>
        <option value="marketplace"  <?= ($filters['type'] ?? '') === 'marketplace'  ? 'selected' : '' ?>>Marketplace</option>
      </select>

      <select name="status" class="form-select">
        <option value=""            <?= ($filters['status'] ?? '') === ''            ? 'selected' : '' ?>>All Statuses</option>
        <option value="paid"        <?= ($filters['status'] ?? '') === 'paid'        ? 'selected' : '' ?>>Paid</option>
        <option value="outstanding" <?= ($filters['status'] ?? '') === 'outstanding' ? 'selected' : '' ?>>Outstanding</option>
        <option value="refunded"    <?= ($filters['status'] ?? '') === 'refunded'    ? 'selected' : '' ?>>Refunded</option>
      </select>

      <select name="method" class="form-select">
        <option value=""              <?= ($filters['method'] ?? '') === ''              ? 'selected' : '' ?>>All Methods</option>
        <option value="stripe"        <?= ($filters['method'] ?? '') === 'stripe'        ? 'selected' : '' ?>>Stripe</option>
        <option value="paypal"        <?= ($filters['method'] ?? '') === 'paypal'        ? 'selected' : '' ?>>PayPal</option>
        <option value="bank_transfer" <?= ($filters['method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
        <option value="interac"       <?= ($filters['method'] ?? '') === 'interac'       ? 'selected' : '' ?>>Interac</option>
        <option value="cash"          <?= ($filters['method'] ?? '') === 'cash'          ? 'selected' : '' ?>>Cash</option>
      </select>

      <input type="date" name="date_from" class="form-input" value="<?= htmlspecialchars($filters['dateFrom'] ?? '') ?>" placeholder="From">
      <input type="date" name="date_to"   class="form-input" value="<?= htmlspecialchars($filters['dateTo']   ?? '') ?>" placeholder="To">

      <input type="text" name="search" class="form-input" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
             placeholder="Business name or ref #" style="min-width:190px;">

      <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filter</button>
      <a href="<?= url('admin/receivables') ?>" class="btn-export"><i class="fas fa-times"></i> Clear</a>
      <a href="<?= htmlspecialchars($exportUrl) ?>" class="btn-export"><i class="fas fa-file-csv"></i> Export CSV</a>
    </div>
  </form>
</div>

<!-- Table -->
<div class="table-card">
  <?php if (empty($rows)): ?>
    <div class="empty-state">
      <i class="fas fa-receipt"></i>
      <p style="font-size:15px;font-weight:600;color:#6b7280;">No receivables found</p>
      <p style="font-size:13px;">Try adjusting your filters.</p>
    </div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Type</th>
        <th>Reference</th>
        <th>Invoice</th>
        <th>Payer</th>
        <th>Method</th>
        <th>Breakdown</th>
        <th>Total</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $i => $row): ?>
      <?php
        $isPaid        = $row['payment_status'] === 'paid';
        $isOutstanding = $row['payment_status'] === 'outstanding';
        $isRefunded    = $row['payment_status'] === 'refunded';
        $isDist        = $row['revenue_type']   === 'distribution';
        $isMkt         = $row['revenue_type']   === 'marketplace';

        // View link
        if ($isDist) {
            $viewLink = url('admin/distribution/view') . '?id=' . (int)$row['distribution_request_id'];
        } else {
            $viewLink = url('admin/orders/view') . '?id=' . (int)$row['order_id'];
        }

        // Payment method label / icon
        $methodMap = [
            'stripe'        => ['<i class="fab fa-stripe-s"></i>', 'Stripe'],
            'paypal'        => ['<i class="fab fa-paypal"></i>', 'PayPal'],
            'bank_transfer' => ['<i class="fas fa-university"></i>', 'Bank Transfer'],
            'interac'       => ['<i class="fas fa-exchange-alt"></i>', 'Interac'],
            'cash'          => ['<i class="fas fa-money-bill-wave"></i>', 'Cash'],
        ];
        $mKey   = strtolower($row['payment_method'] ?? '');
        $mLabel = isset($methodMap[$mKey])
            ? $methodMap[$mKey][0] . ' ' . $methodMap[$mKey][1]
            : '<span style="color:#9ca3af;">—</span>';

        // Amount class
        $amountClass = $isPaid ? 'amount-paid' : ($isOutstanding ? 'amount-outstanding' : 'amount-refunded');
        $amountPrefix = $isRefunded ? '-' : '';
      ?>
      <tr>
        <!-- Date -->
        <td style="white-space:nowrap;">
          <?php if ($row['paid_at']): ?>
            <span style="font-size:13px;"><?= date('M j Y', strtotime($row['paid_at'])) ?></span><br>
            <span style="font-size:11px;color:#9ca3af;"><?= date('g:i A', strtotime($row['paid_at'])) ?></span>
          <?php else: ?>
            <span style="color:#9ca3af;">—</span>
          <?php endif; ?>
        </td>

        <!-- Type -->
        <td>
          <?php if ($isDist): ?>
            <span class="badge badge-distribution"><i class="fas fa-building"></i> Distribution B2B</span>
          <?php else: ?>
            <span class="badge badge-marketplace"><i class="fas fa-shopping-cart"></i> Marketplace</span>
          <?php endif; ?>
        </td>

        <!-- Reference -->
        <td>
          <a href="<?= $viewLink ?>" style="font-weight:600;color:var(--primary);text-decoration:none;">
            <?= htmlspecialchars($row['reference_number'] ?? '—') ?>
          </a>
        </td>

        <!-- Invoice -->
        <td style="color:#6b7280;">
          <?= $row['invoice_number'] ? htmlspecialchars($row['invoice_number']) : '<span style="color:#d1d5db;">—</span>' ?>
        </td>

        <!-- Payer -->
        <td>
          <div class="payer-name"><?= htmlspecialchars($row['payer_name'] ?? '—') ?></div>
          <div class="payer-email"><?= htmlspecialchars($row['payer_email'] ?? '') ?></div>
        </td>

        <!-- Method -->
        <td style="white-space:nowrap;"><?= $mLabel ?></td>

        <!-- Breakdown -->
        <td>
          <button class="breakdown-toggle" onclick="toggleBreakdown(<?= $i ?>)" title="Show breakdown">
            <i class="fas fa-info-circle"></i> Details
          </button>
          <div class="breakdown-detail" id="breakdown-<?= $i ?>">
            Sub: $<?= number_format((float)$row['subtotal'], 2) ?><br>
            GST: $<?= number_format((float)$row['gst_amount'], 2) ?><br>
            <?php if ((float)$row['qst_amount'] > 0): ?>
            QST: $<?= number_format((float)$row['qst_amount'], 2) ?><br>
            <?php endif; ?>
            <?php if ((float)$row['delivery_fee'] > 0): ?>
            Del: $<?= number_format((float)$row['delivery_fee'], 2) ?><br>
            <?php endif; ?>
            <?php if ((float)$row['service_fee'] > 0): ?>
            Svc: $<?= number_format((float)$row['service_fee'], 2) ?><br>
            <?php endif; ?>
            <?php if ((float)$row['handling_fee'] > 0): ?>
            Hdl: $<?= number_format((float)$row['handling_fee'], 2) ?><br>
            <?php endif; ?>
            <?php if ((float)$row['tip_amount'] > 0): ?>
            Tip: $<?= number_format((float)$row['tip_amount'], 2) ?><br>
            <?php endif; ?>
            <?php if ($row['transaction_ref']): ?>
            <span style="color:#6b7280;">Ref: <?= htmlspecialchars(substr($row['transaction_ref'], 0, 28)) ?></span>
            <?php endif; ?>
          </div>
        </td>

        <!-- Total -->
        <td>
          <span class="amount <?= $amountClass ?>">
            <?= $amountPrefix ?>$<?= number_format((float)$row['total_amount'], 2) ?>
          </span>
        </td>

        <!-- Status -->
        <td>
          <span class="badge badge-<?= htmlspecialchars($row['payment_status']) ?>">
            <?php if ($isPaid): ?><i class="fas fa-check-circle"></i><?php endif; ?>
            <?php if ($isOutstanding): ?><i class="fas fa-clock"></i><?php endif; ?>
            <?php if ($isRefunded): ?><i class="fas fa-undo"></i><?php endif; ?>
            <?= ucfirst($row['payment_status']) ?>
          </span>
        </td>

        <!-- Actions -->
        <td style="white-space:nowrap;">
          <a href="<?= $viewLink ?>" class="btn-sm btn-view"><i class="fas fa-eye"></i> View</a>

          <?php if ($isOutstanding && $isDist): ?>
            <button type="button" class="btn-sm btn-pay"
              onclick="openMarkPaidModal(<?= (int)$row['source_id'] ?>, '<?= htmlspecialchars($row['reference_number']) ?>')"
            ><i class="fas fa-check"></i> Mark Paid</button>
          <?php endif; ?>

          <?php if ($isPaid): ?>
            <button type="button" class="btn-sm btn-refund"
              onclick="openMarkRefundedModal('<?= htmlspecialchars($row['revenue_type']) ?>', <?= (int)$row['source_id'] ?>, '<?= htmlspecialchars($row['reference_number']) ?>')"
              title="Mark as refunded"
            ><i class="fas fa-undo"></i> Refund</button>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <div class="pagination-wrap">
    <?php
      $baseUrl     = url('admin/receivables');
      $queryParams = array_filter([
          'type'      => $filters['type']     ?? '',
          'method'    => $filters['method']   ?? '',
          'status'    => $filters['status']   ?? '',
          'search'    => $filters['search']   ?? '',
          'date_from' => $filters['dateFrom'] ?? '',
          'date_to'   => $filters['dateTo']   ?? '',
          'per_page'  => $filters['perPage']  ?? 25,
      ]);
      require __DIR__ . '/../components/pagination.php';
    ?>
  </div>
  <?php endif; ?>

  <?php endif; ?>
</div>

<!-- Results summary -->
<?php if (!empty($rows)): ?>
<p style="font-size:12px;color:#9ca3af;margin-top:10px;text-align:right;">
  Showing <?= count($rows) ?> of <?= number_format($total) ?> records
</p>
<?php endif; ?>


<!-- ====================================================== -->
<!-- Mark Paid Modal -->
<!-- ====================================================== -->
<div class="modal-overlay" id="modal-mark-paid">
  <div class="modal-box">
    <h2 class="modal-title"><i class="fas fa-check-circle" style="color:#059669;"></i> Record Payment</h2>
    <p id="modal-paid-ref" style="font-size:13px;color:#6b7280;margin-bottom:18px;"></p>
    <form method="POST" action="<?= url('admin/receivables/mark-paid') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="source_id" id="modal-paid-source-id">

      <div class="modal-field">
        <label class="modal-label">Payment Method</label>
        <select name="payment_method" class="modal-select" required>
          <option value="">Select method...</option>
          <option value="bank_transfer">Bank Transfer</option>
          <option value="interac">Interac e-Transfer</option>
        </select>
      </div>

      <div class="modal-field">
        <label class="modal-label">Reference / e-Transfer ID</label>
        <input type="text" name="payment_reference" class="modal-input" placeholder="e.g. TXN-2024-001 or confirmation number">
      </div>

      <div class="modal-field">
        <label class="modal-label">Payment Date &amp; Time</label>
        <input type="datetime-local" name="paid_at" class="modal-input" value="<?= date('Y-m-d\TH:i') ?>">
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-modal-cancel" onclick="closeModal('modal-mark-paid')">Cancel</button>
        <button type="submit" class="btn-modal-submit"><i class="fas fa-check"></i> Confirm Payment</button>
      </div>
    </form>
  </div>
</div>


<!-- ====================================================== -->
<!-- Mark Refunded Modal -->
<!-- ====================================================== -->
<div class="modal-overlay" id="modal-mark-refunded">
  <div class="modal-box">
    <h2 class="modal-title"><i class="fas fa-undo" style="color:#dc2626;"></i> Mark as Refunded</h2>
    <div class="modal-warning">
      <i class="fas fa-exclamation-triangle"></i>
      This action marks the record as refunded in the system. It does <strong>not</strong> trigger any automatic refund with a payment processor — ensure the actual refund is processed separately.
    </div>
    <p id="modal-refund-ref" style="font-size:13px;color:#6b7280;margin-bottom:18px;"></p>
    <form method="POST" action="<?= url('admin/receivables/mark-refunded') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="revenue_type" id="modal-refund-type">
      <input type="hidden" name="source_id"    id="modal-refund-source-id">

      <div class="modal-field">
        <label class="modal-label">Refund Reference (optional)</label>
        <input type="text" name="refund_reference" class="modal-input" placeholder="Stripe refund ID, interac ref, etc.">
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-modal-cancel" onclick="closeModal('modal-mark-refunded')">Cancel</button>
        <button type="submit" class="btn-modal-submit" style="background:#dc2626;"><i class="fas fa-undo"></i> Confirm Refund</button>
      </div>
    </form>
  </div>
</div>


<script>
function toggleBreakdown(idx) {
  const el = document.getElementById('breakdown-' + idx);
  if (el) el.classList.toggle('open');
}

function openMarkPaidModal(sourceId, refNumber) {
  document.getElementById('modal-paid-source-id').value = sourceId;
  document.getElementById('modal-paid-ref').textContent  = 'Request: ' + refNumber;
  document.getElementById('modal-mark-paid').classList.add('open');
}

function openMarkRefundedModal(revenueType, sourceId, refNumber) {
  document.getElementById('modal-refund-type').value      = revenueType;
  document.getElementById('modal-refund-source-id').value = sourceId;
  document.getElementById('modal-refund-ref').textContent = 'Reference: ' + refNumber;
  document.getElementById('modal-mark-refunded').classList.add('open');
}

function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}

// Close on backdrop click
document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
  overlay.addEventListener('click', function(e) {
    if (e.target === overlay) overlay.classList.remove('open');
  });
});

// Close on Escape
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.open').forEach(function(el) {
      el.classList.remove('open');
    });
  }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
