<?php
$pageTitle = 'Invoice ' . ($invoice['invoice_number'] ?? '');
$currentPage = 'payables';
ob_start();
?>

<style>
  .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
  .page-title { font-size: 24px; font-weight: 700; color: var(--dark); }
  .back-link { color: #6b7280; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; }
  .back-link:hover { color: var(--primary); }

  .invoice-grid { display: grid; grid-template-columns: 1fr 380px; gap: 24px; }

  .card { background: white; border-radius: var(--radius-xl); padding: 28px; box-shadow: var(--shadow-sm); margin-bottom: 24px; }
  .card-title { font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }

  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
  .info-item label { font-size: 12px; color: #6b7280; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 4px; }
  .info-item .value { font-size: 15px; color: #1f2937; font-weight: 500; }

  .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
  .badge-draft { background: #f3f4f6; color: #6b7280; }
  .badge-sent { background: #dbeafe; color: #1d4ed8; }
  .badge-partial { background: #fef3c7; color: #92400e; }
  .badge-paid { background: #d1fae5; color: #065f46; }
  .badge-overdue { background: #fee2e2; color: #991b1b; }
  .badge-cancelled { background: #f3f4f6; color: #9ca3af; }

  table { width: 100%; border-collapse: collapse; }
  th { padding: 12px 14px; text-align: left; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; background: #f9fafb; border-bottom: 2px solid #e5e7eb; }
  td { padding: 12px 14px; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6; }
  tr:hover td { background: #f9fafb; }

  .totals-table { margin-top: 16px; }
  .totals-table td { padding: 8px 14px; font-size: 14px; }
  .totals-table tr:last-child td { font-weight: 700; font-size: 16px; border-top: 2px solid #e5e7eb; }

  .amount { font-weight: 600; font-family: 'SF Mono', 'Cascadia Code', monospace; }

  .payment-form { margin-top: 16px; }
  .form-group { margin-bottom: 14px; }
  .form-group label { font-size: 13px; font-weight: 600; color: #374151; display: block; margin-bottom: 5px; }
  .form-input, .form-select { width: 100%; padding: 9px 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
  .form-input:focus, .form-select:focus { border-color: var(--primary); outline: none; }

  .btn-pay {
    width: 100%; padding: 12px; background: var(--primary); color: white; border: none;
    border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
  }
  .btn-pay:hover { background: var(--primary-600); }

  .payment-history { margin-top: 8px; }
  .payment-item {
    padding: 14px; border: 1px solid #f3f4f6; border-radius: 8px; margin-bottom: 10px;
    display: flex; justify-content: space-between; align-items: center;
  }
  .payment-item .method-badge {
    font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px;
    background: #f3f4f6; color: #374151; text-transform: uppercase;
  }

  .balance-box {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 2px solid #00b207; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 20px;
  }
  .balance-box.has-balance { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-color: #ef4444; }
  .balance-label { font-size: 13px; color: #6b7280; font-weight: 500; }
  .balance-amount { font-size: 28px; font-weight: 700; margin-top: 4px; font-family: 'SF Mono', monospace; }

  @media (max-width: 900px) { .invoice-grid { grid-template-columns: 1fr; } }
</style>

<a href="<?= url('admin/payables') ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to Payables</a>

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px;">
    <h1 class="page-title"><?= htmlspecialchars($invoice['invoice_number']) ?></h1>
    <a href="<?= url('admin/payables/download-pdf?id=' . $invoice['id']) ?>" class="btn" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#1f2937;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
      <i class="fas fa-file-pdf"></i> Download PDF
    </a>
  </div>
  <span class="badge badge-<?= $invoice['status'] ?>" style="font-size:14px;padding:6px 16px;"><?= ucfirst($invoice['status']) ?></span>
</div>

<div class="invoice-grid">
  <!-- Left Column: Invoice Details -->
  <div>
    <div class="card">
      <h3 class="card-title"><i class="fas fa-file-invoice" style="color:var(--primary);"></i> Invoice Details</h3>
      <div class="info-grid">
        <div class="info-item">
          <label>Supplier</label>
          <div class="value"><?= htmlspecialchars($invoice['company_name'] ?: $invoice['supplier_name']) ?></div>
        </div>
        <div class="info-item">
          <label>Email</label>
          <div class="value"><?= htmlspecialchars($invoice['supplier_email']) ?></div>
        </div>
        <div class="info-item">
          <label>Purchase Order</label>
          <div class="value">
            <?php if ($invoice['po_number']): ?>
              <a href="<?= url('admin/purchase-orders/view?id=' . $invoice['po_id']) ?>" style="color:var(--primary);font-weight:600;"><?= htmlspecialchars($invoice['po_number']) ?></a>
            <?php else: ?>—<?php endif; ?>
          </div>
        </div>
        <div class="info-item">
          <label>Payment Terms</label>
          <div class="value"><?= htmlspecialchars($invoice['payment_terms'] ?? 'Net 30') ?></div>
        </div>
        <div class="info-item">
          <label>Issue Date</label>
          <div class="value"><?= date('M j, Y', strtotime($invoice['issue_date'])) ?></div>
        </div>
        <div class="info-item">
          <label>Due Date</label>
          <div class="value" style="<?= $invoice['status'] === 'overdue' ? 'color:#dc2626;font-weight:700;' : '' ?>">
            <?= date('M j, Y', strtotime($invoice['due_date'])) ?>
            <?php if ($invoice['status'] === 'overdue'): ?> <span style="font-size:12px;">(overdue)</span><?php endif; ?>
          </div>
        </div>
        <?php if ($invoice['paid_at']): ?>
        <div class="info-item">
          <label>Paid At</label>
          <div class="value" style="color:#059669;"><?= date('M j, Y g:i A', strtotime($invoice['paid_at'])) ?></div>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($invoice['notes']): ?>
        <div style="padding:12px 16px;background:#f9fafb;border-radius:8px;font-size:13px;color:#374151;margin-top:12px;">
          <strong>Notes:</strong> <?= nl2br(htmlspecialchars($invoice['notes'])) ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Line Items -->
    <?php if (!empty($items)): ?>
    <div class="card">
      <h3 class="card-title"><i class="fas fa-list" style="color:var(--primary);"></i> Line Items</h3>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>SKU</th>
            <th>Qty Received</th>
            <th>Unit Cost</th>
            <th style="text-align:right;">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['product_name'] ?? 'Unknown Product') ?></td>
            <td style="color:#6b7280;font-size:13px;"><?= htmlspecialchars($item['sku'] ?? '—') ?></td>
            <td><?= $item['quantity_received'] ?> / <?= $item['quantity_ordered'] ?></td>
            <td class="amount">$<?= number_format($item['unit_cost'], 2) ?></td>
            <td class="amount" style="text-align:right;">$<?= number_format($item['total_cost'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <table class="totals-table" style="width:50%;margin-left:auto;">
        <tr><td>Subtotal</td><td class="amount" style="text-align:right;">$<?= number_format($invoice['subtotal'], 2) ?></td></tr>
        <?php if ($invoice['shipping'] > 0): ?><tr><td>Shipping</td><td class="amount" style="text-align:right;">$<?= number_format($invoice['shipping'], 2) ?></td></tr><?php endif; ?>
        <tr><td>GST (5%)</td><td class="amount" style="text-align:right;">$<?= number_format($invoice['tax_gst'], 2) ?></td></tr>
        <tr><td>QST (9.975%)</td><td class="amount" style="text-align:right;">$<?= number_format($invoice['tax_qst'], 2) ?></td></tr>
        <tr><td>Total</td><td class="amount" style="text-align:right;">$<?= number_format($invoice['total_amount'], 2) ?></td></tr>
      </table>
    </div>
    <?php endif; ?>

    <!-- Payment History -->
    <?php if (!empty($payments)): ?>
    <div class="card">
      <h3 class="card-title"><i class="fas fa-history" style="color:var(--primary);"></i> Payment History</h3>
      <div class="payment-history">
        <?php foreach ($payments as $p): ?>
        <div class="payment-item">
          <div>
            <strong style="font-size:14px;"><?= htmlspecialchars($p['payment_number']) ?></strong>
            <div style="font-size:12px;color:#6b7280;margin-top:2px;">
              <?= date('M j, Y', strtotime($p['payment_date'])) ?>
              <?php if ($p['reference_number']): ?> &middot; Ref: <?= htmlspecialchars($p['reference_number']) ?><?php endif; ?>
            </div>
            <?php if ($p['notes']): ?><div style="font-size:12px;color:#9ca3af;margin-top:2px;"><?= htmlspecialchars($p['notes']) ?></div><?php endif; ?>
          </div>
          <div style="text-align:right;">
            <div class="amount" style="color:#059669;font-size:16px;">$<?= number_format($p['amount_applied'], 2) ?></div>
            <span class="method-badge"><?= ucfirst(str_replace('_', ' ', $p['payment_method'])) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Right Column: Balance & Payment Form -->
  <div>
    <div class="balance-box <?= $invoice['balance_due'] > 0.01 ? 'has-balance' : '' ?>">
      <div class="balance-label"><?= $invoice['balance_due'] > 0.01 ? 'Balance Due' : 'Fully Paid' ?></div>
      <div class="balance-amount" style="color:<?= $invoice['balance_due'] > 0.01 ? '#dc2626' : '#059669' ?>;">
        $<?= number_format($invoice['balance_due'], 2) ?>
      </div>
      <?php if ($invoice['amount_paid'] > 0 && $invoice['balance_due'] > 0.01): ?>
        <div style="font-size:12px;color:#6b7280;margin-top:6px;">$<?= number_format($invoice['amount_paid'], 2) ?> paid of $<?= number_format($invoice['total_amount'], 2) ?></div>
      <?php endif; ?>
    </div>

    <?php if ($invoice['balance_due'] > 0.01 && $invoice['status'] !== 'cancelled'): ?>
    <div class="card">
      <h3 class="card-title"><i class="fas fa-credit-card" style="color:var(--primary);"></i> Record Payment</h3>
      <form method="POST" action="<?= url('admin/payables/record-payment') ?>" class="payment-form">
        <?= csrfField() ?>
        <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">

        <div class="form-group">
          <label>Amount ($)</label>
          <input type="number" name="amount" class="form-input" step="0.01" min="0.01" max="<?= $invoice['balance_due'] ?>" value="<?= $invoice['balance_due'] ?>" required>
        </div>

        <div class="form-group">
          <label>Payment Method</label>
          <select name="payment_method" class="form-select" required>
            <option value="interac">Interac e-Transfer</option>
            <option value="bank_transfer">Bank Transfer (EFT)</option>
            <option value="cheque">Cheque</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div class="form-group">
          <label>Reference / Confirmation #</label>
          <input type="text" name="reference_number" class="form-input" placeholder="e.g. transaction ID, cheque #">
        </div>

        <div class="form-group">
          <label>Payment Date</label>
          <input type="date" name="payment_date" class="form-input" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="form-group">
          <label>Notes <span style="font-weight:400;color:#9ca3af;">(optional)</span></label>
          <textarea name="notes" class="form-input" rows="2" placeholder="Any notes about this payment..."></textarea>
        </div>

        <button type="submit" class="btn-pay" onclick="return confirm('Record this payment?');">
          <i class="fas fa-check-circle"></i> Record Payment
        </button>
      </form>
    </div>
    <?php endif; ?>

    <!-- Supplier Address -->
    <div class="card">
      <h3 class="card-title"><i class="fas fa-building" style="color:#6b7280;"></i> Supplier Info</h3>
      <div style="font-size:14px;color:#374151;line-height:1.7;">
        <strong><?= htmlspecialchars($invoice['company_name'] ?: $invoice['supplier_name']) ?></strong><br>
        <?php if ($invoice['supplier_address']): ?><?= htmlspecialchars($invoice['supplier_address']) ?><br><?php endif; ?>
        <?php if ($invoice['supplier_city']): ?><?= htmlspecialchars($invoice['supplier_city']) ?>, <?= htmlspecialchars($invoice['supplier_province']) ?> <?= htmlspecialchars($invoice['supplier_postal']) ?><br><?php endif; ?>
        <?php if ($invoice['supplier_phone']): ?><i class="fas fa-phone" style="width:16px;color:#9ca3af;"></i> <?= htmlspecialchars($invoice['supplier_phone']) ?><br><?php endif; ?>
        <i class="fas fa-envelope" style="width:16px;color:#9ca3af;"></i> <?= htmlspecialchars($invoice['supplier_email']) ?>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
