<?php
$shop = $shop ?? null;
$payouts = $payouts ?? [];
$pendingBalance = $pendingBalance ?? 0.00;
$pageTitle = 'Payouts';
require __DIR__ . '/layout-header.php';
?>

<style>
  .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; margin-bottom: 24px; }
  .card-title { font-size: 18px; font-weight: 700; color: var(--gray-700); margin-bottom: 16px; }
  .balance-card { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-600) 100%); color: #fff; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
  .balance-label { font-size: 13px; opacity: .85; margin-bottom: 6px; }
  .balance-value { font-size: 32px; font-weight: 700; }
  table.payouts-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  table.payouts-table th { text-align: left; padding: 12px; color: var(--gray-600); font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid var(--gray-200); }
  table.payouts-table td { padding: 16px 12px; border-top: 1px solid var(--gray-100); }
  .amount-negative { color: #991b1b; }
  .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; white-space: nowrap; }
  .badge-pending { background: #fff3e0; color: #e65100; }
  .badge-paid { background: #dcfce7; color: #166534; }
  .badge-held { background: #fee2e2; color: #991b1b; }
  .no-shop { text-align: center; padding: 40px; }
  .no-shop i { font-size: 56px; color: var(--gray-300); display: block; margin-bottom: 16px; }
  .btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--primary); color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; }
  .table-scroll { overflow-x: auto; }
  .empty-state { text-align: center; padding: 40px 20px; color: var(--gray-600); }
</style>

<?php if (!$shop): ?>
    <div class="card">
        <div class="no-shop">
            <i class="fas fa-store"></i>
            <h2 style="font-size:20px;font-weight:600;margin-bottom:8px;color:var(--gray-700);">You don't have a shop yet</h2>
            <p style="color:var(--gray-600);margin-bottom:20px;">Create your shop to start selling on OCS Marketplace.</p>
            <a href="<?= url('seller/shop/create') ?>" class="btn-primary"><i class="fas fa-plus"></i> Create My Shop</a>
        </div>
    </div>
<?php else: ?>
    <div class="balance-card">
        <div class="balance-label">Pending Balance</div>
        <div class="balance-value">$<?= number_format($pendingBalance, 2) ?></div>
    </div>

    <div class="card">
        <h2 class="card-title">Payout Statement</h2>
        <?php if (empty($payouts)): ?>
            <div class="empty-state">No payouts yet - they're created once an order is delivered.</div>
        <?php else: ?>
            <div class="table-scroll">
            <table class="payouts-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Subtotal</th>
                        <th>Commission</th>
                        <th>Processing Fee</th>
                        <th>Chargeback</th>
                        <th>Net Payout</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payouts as $p): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($p['order_number']) ?></strong></td>
                            <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                            <td>$<?= number_format((float)$p['subtotal'], 2) ?></td>
                            <td class="amount-negative">-$<?= number_format((float)$p['commission_amount'], 2) ?></td>
                            <td class="amount-negative">-$<?= number_format((float)$p['processing_fee_amount'], 2) ?></td>
                            <td class="amount-negative"><?= (float)$p['chargeback_amount'] > 0 ? '-$' . number_format((float)$p['chargeback_amount'], 2) : '-' ?></td>
                            <td><strong>$<?= number_format((float)$p['net_payout_amount'] - (float)$p['chargeback_amount'], 2) ?></strong></td>
                            <td><span class="badge badge-<?= htmlspecialchars($p['status']) ?>"><?= ucfirst($p['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/layout-footer.php'; ?>
