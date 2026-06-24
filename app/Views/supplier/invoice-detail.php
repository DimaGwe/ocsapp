<?php require __DIR__ . '/layout-header.php'; ?>

<style>
  .back-link { color: var(--gray-400); text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 16px; }
  .back-link:hover { color: var(--primary); }

  .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
  .page-title { font-size: 22px; font-weight: 700; color: var(--gray-700); }

  .invoice-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }

  .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 24px; }
  .card-title { font-size: 16px; font-weight: 700; color: var(--gray-700); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }

  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .info-item label { font-size: 12px; color: var(--gray-400); text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 3px; }
  .info-item .value { font-size: 14px; color: var(--gray-700); font-weight: 500; }

  .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
  .badge-draft { background: #f3f4f6; color: #6b7280; }
  .badge-sent { background: #dbeafe; color: #1d4ed8; }
  .badge-partial { background: #fef3c7; color: #92400e; }
  .badge-paid { background: #d1fae5; color: #065f46; }
  .badge-overdue { background: #fee2e2; color: #991b1b; }
  .badge-cancelled { background: #f3f4f6; color: #9ca3af; }

  table { width: 100%; border-collapse: collapse; }
  th { padding: 12px 14px; text-align: left; font-size: 12px; font-weight: 600; color: var(--gray-400); text-transform: uppercase; background: var(--gray-50); border-bottom: 2px solid var(--gray-200); }
  td { padding: 12px 14px; font-size: 14px; color: var(--gray-700); border-bottom: 1px solid var(--gray-100); }

  .totals-table { margin-top: 16px; }
  .totals-table td { padding: 8px 14px; font-size: 14px; }
  .totals-table tr:last-child td { font-weight: 700; font-size: 16px; border-top: 2px solid var(--gray-200); }

  .amount { font-weight: 600; font-family: 'SF Mono', 'Cascadia Code', monospace; }

  .balance-box {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 2px solid #00b207; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 20px;
  }
  .balance-box.has-balance { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-color: #ef4444; }
  .balance-label { font-size: 13px; color: var(--gray-400); font-weight: 500; }
  .balance-amount { font-size: 28px; font-weight: 700; margin-top: 4px; font-family: 'SF Mono', monospace; }

  .timeline { position: relative; padding-left: 28px; }
  .timeline::before { content: ''; position: absolute; left: 8px; top: 4px; bottom: 4px; width: 2px; background: var(--gray-200); }
  .timeline-item { position: relative; margin-bottom: 18px; }
  .timeline-item::before {
    content: ''; position: absolute; left: -24px; top: 6px; width: 12px; height: 12px;
    background: #059669; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 2px #059669;
  }
  .timeline-item .tl-amount { font-size: 16px; font-weight: 700; color: #059669; }
  .timeline-item .tl-meta { font-size: 12px; color: var(--gray-400); margin-top: 2px; }
  .timeline-item .tl-method { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 4px; background: var(--gray-100); color: var(--gray-600); text-transform: uppercase; display: inline-block; margin-top: 4px; }

  .btn-pdf {
    display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px;
    background: #1f2937; color: white; border-radius: 8px; font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all 0.2s;
  }
  .btn-pdf:hover { background: #374151; transform: translateY(-1px); }

  @media (max-width: 900px) { .invoice-grid { grid-template-columns: 1fr; } }
</style>

<a href="<?= url('supplier/invoices') ?>" class="back-link"><i class="fas fa-arrow-left"></i> <?= $fr ? 'Retour aux factures' : 'Back to Invoices' ?></a>

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
    <h1 class="page-title"><?= htmlspecialchars($invoice['invoice_number']) ?></h1>
    <span class="badge badge-<?= $invoice['status'] ?>"><?= ucfirst($invoice['status']) ?></span>
  </div>
  <a href="<?= url('supplier/invoices/download-pdf?id=' . $invoice['id']) ?>" class="btn-pdf">
    <i class="fas fa-file-pdf"></i> <?= $fr ? 'Télécharger PDF' : 'Download PDF' ?>
  </a>
</div>

<div class="invoice-grid">
  <!-- Left Column -->
  <div>
    <!-- Invoice Info -->
    <div class="card">
      <h3 class="card-title"><i class="fas fa-file-invoice" style="color:var(--primary);"></i> <?= $fr ? 'Détails de la facture' : 'Invoice Details' ?></h3>
      <div class="info-grid">
        <div class="info-item">
          <label><?= $fr ? 'Numéro de facture' : 'Invoice Number' ?></label>
          <div class="value"><?= htmlspecialchars($invoice['invoice_number']) ?></div>
        </div>
        <div class="info-item">
          <label><?= $fr ? 'Bon de commande' : 'Purchase Order' ?></label>
          <div class="value"><?= htmlspecialchars($invoice['po_number'] ?? '—') ?></div>
        </div>
        <div class="info-item">
          <label><?= $fr ? 'Date d\'émission' : 'Issue Date' ?></label>
          <div class="value"><?= date('M j, Y', strtotime($invoice['issue_date'])) ?></div>
        </div>
        <div class="info-item">
          <label><?= $fr ? 'Date d\'échéance' : 'Due Date' ?></label>
          <div class="value" style="<?= $invoice['status'] === 'overdue' ? 'color:#dc2626;font-weight:700;' : '' ?>">
            <?= date('M j, Y', strtotime($invoice['due_date'])) ?>
            <?php if ($invoice['status'] === 'overdue'): ?> <span style="font-size:11px;color:#dc2626;">(<?= $fr ? 'en retard' : 'overdue' ?>)</span><?php endif; ?>
          </div>
        </div>
        <?php if ($invoice['paid_at']): ?>
        <div class="info-item">
          <label><?= $fr ? 'Payé le' : 'Paid On' ?></label>
          <div class="value" style="color:#059669;"><?= date('M j, Y g:i A', strtotime($invoice['paid_at'])) ?></div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Line Items -->
    <?php if (!empty($items)): ?>
    <div class="card">
      <h3 class="card-title"><i class="fas fa-list" style="color:var(--primary);"></i> <?= $fr ? 'Lignes de facturation' : 'Line Items' ?></h3>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th><?= $fr ? 'Produit' : 'Product' ?></th>
            <th>SKU</th>
            <th style="text-align:center;"><?= $fr ? 'Qté' : 'Qty' ?></th>
            <th style="text-align:right;"><?= $fr ? 'Prix unitaire' : 'Unit Price' ?></th>
            <th style="text-align:right;"><?= $fr ? 'Total' : 'Total' ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $lineNum = 1; foreach ($items as $item): ?>
          <tr>
            <td style="color:var(--gray-400);"><?= $lineNum++ ?></td>
            <td><?= htmlspecialchars($item['product_name'] ?? 'Product') ?></td>
            <td style="color:var(--gray-400);font-size:13px;"><?= htmlspecialchars($item['sku'] ?? '—') ?></td>
            <td style="text-align:center;"><?= $item['quantity_ordered'] ?></td>
            <td class="amount" style="text-align:right;">$<?= number_format($item['unit_cost'], 2) ?></td>
            <td class="amount" style="text-align:right;">$<?= number_format($item['total_cost'] ?? $item['quantity_ordered'] * $item['unit_cost'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Tax Breakdown -->
      <table class="totals-table" style="width:55%;margin-left:auto;">
        <tr><td><?= $fr ? 'Sous-total' : 'Subtotal' ?></td><td class="amount" style="text-align:right;">$<?= number_format($invoice['subtotal'], 2) ?></td></tr>
        <?php if ($invoice['shipping'] > 0): ?>
        <tr><td><?= $fr ? 'Livraison' : 'Shipping' ?></td><td class="amount" style="text-align:right;">$<?= number_format($invoice['shipping'], 2) ?></td></tr>
        <?php endif; ?>
        <tr><td>TPS (5%)</td><td class="amount" style="text-align:right;">$<?= number_format($invoice['tax_gst'], 2) ?></td></tr>
        <tr><td>TVQ (9.975%)</td><td class="amount" style="text-align:right;">$<?= number_format($invoice['tax_qst'], 2) ?></td></tr>
        <tr><td><?= $fr ? 'Total' : 'Total' ?></td><td class="amount" style="text-align:right;color:var(--primary);">$<?= number_format($invoice['total_amount'], 2) ?></td></tr>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- Right Column: Balance & Payment History -->
  <div>
    <!-- Balance Box -->
    <div class="balance-box <?= $invoice['balance_due'] > 0.01 ? 'has-balance' : '' ?>">
      <div class="balance-label"><?= $invoice['balance_due'] > 0.01 ? ($fr ? 'Solde dû' : 'Balance Due') : ($fr ? 'Entièrement payé' : 'Fully Paid') ?></div>
      <div class="balance-amount" style="color:<?= $invoice['balance_due'] > 0.01 ? '#dc2626' : '#059669' ?>;">
        $<?= number_format($invoice['balance_due'], 2) ?>
      </div>
      <?php if ($invoice['amount_paid'] > 0 && $invoice['balance_due'] > 0.01): ?>
        <div style="font-size:12px;color:var(--gray-400);margin-top:6px;">
          $<?= number_format($invoice['amount_paid'], 2) ?> <?= $fr ? 'payé sur' : 'paid of' ?> $<?= number_format($invoice['total_amount'], 2) ?>
        </div>
        <div style="margin-top:8px;background:var(--gray-200);border-radius:4px;height:6px;overflow:hidden;">
          <div style="height:100%;background:#059669;border-radius:4px;width:<?= min(100, round(($invoice['amount_paid'] / $invoice['total_amount']) * 100)) ?>%;"></div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Payment History Timeline -->
    <div class="card">
      <h3 class="card-title"><i class="fas fa-history" style="color:var(--primary);"></i> <?= $fr ? 'Historique des paiements' : 'Payment History' ?></h3>
      <?php if (!empty($payments)): ?>
        <div class="timeline">
          <?php foreach ($payments as $p): ?>
          <div class="timeline-item">
            <div class="tl-amount">$<?= number_format($p['amount_applied'], 2) ?></div>
            <div class="tl-meta">
              <?= date('M j, Y', strtotime($p['payment_date'])) ?>
              &middot; <?= htmlspecialchars($p['payment_number']) ?>
              <?php if (!empty($p['reference_number'])): ?>
                &middot; <?= $fr ? 'Réf :' : 'Ref:' ?> <?= htmlspecialchars($p['reference_number']) ?>
              <?php endif; ?>
            </div>
            <span class="tl-method"><?= ucfirst(str_replace('_', ' ', $p['payment_method'])) ?></span>
            <?php if (!empty($p['notes'])): ?>
              <div style="font-size:12px;color:var(--gray-400);margin-top:4px;font-style:italic;"><?= htmlspecialchars($p['notes']) ?></div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="text-align:center;padding:24px;color:var(--gray-400);">
          <i class="fas fa-clock" style="font-size:28px;margin-bottom:8px;display:block;"></i>
          <p style="font-size:13px;"><?= $fr ? 'Aucun paiement enregistré pour l\'instant.' : 'No payments recorded yet.' ?></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/layout-footer.php'; ?>
