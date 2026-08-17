<?php
/**
 * Admin Claims Queue - Returns & Refund Automation (Sec 4)
 */
$currentPage = 'claims';
$pageTitle = 'Return Claims';

ob_start();
?>

<div class="page-header">
    <h1>Return Claims</h1>
    <p style="color:#6b7280;">Fault determination is a system-computed suggestion only - every chargeback/absorb/deny action requires admin confirmation.</p>
</div>

<div style="display:flex; gap:8px; margin-bottom:20px;">
    <a href="<?= url('admin/claims') ?>" class="btn <?= empty($statusFilter) ? 'btn-primary' : 'btn-secondary' ?>">All (<?= array_sum($counts) ?>)</a>
    <?php foreach (['submitted', 'under_review', 'resolved', 'window_expired'] as $s): ?>
        <a href="<?= url('admin/claims?status=' . $s) ?>" class="btn <?= $statusFilter === $s ? 'btn-primary' : 'btn-secondary' ?>">
            <?= ucfirst(str_replace('_', ' ', $s)) ?> (<?= $counts[$s] ?? 0 ?>)
        </a>
    <?php endforeach; ?>
</div>

<table class="table" style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="border-bottom:2px solid #e5e7eb; text-align:left;">
            <th style="padding:10px;">ID</th>
            <th style="padding:10px;">Order</th>
            <th style="padding:10px;">Buyer</th>
            <th style="padding:10px;">Type</th>
            <th style="padding:10px;">Claimed</th>
            <th style="padding:10px;">Suggested Fault</th>
            <th style="padding:10px;">Status</th>
            <th style="padding:10px;">Filed</th>
            <th style="padding:10px;"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($claims as $c): ?>
        <tr style="border-bottom:1px solid #f0f0f0;">
            <td style="padding:10px;">#<?= $c['id'] ?> <?= $c['track'] === 'B' ? '<small style="color:#9ca3af;">(B)</small>' : '' ?></td>
            <td style="padding:10px;"><?= htmlspecialchars($c['order_number'] ?? $c['request_number'] ?? $c['shipment_number'] ?? '-') ?></td>
            <td style="padding:10px;"><?= htmlspecialchars(trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''))) ?></td>
            <td style="padding:10px;"><?= htmlspecialchars(str_replace('_', ' ', $c['claim_type'])) ?></td>
            <td style="padding:10px;">$<?= number_format($c['claimed_value'], 2) ?></td>
            <td style="padding:10px;"><?= htmlspecialchars(str_replace('_', ' ', $c['fault_determination'])) ?><?= $c['fault_determined_by'] ? '' : ' <small style="color:#9ca3af;">(suggested)</small>' ?></td>
            <td style="padding:10px;"><span class="badge"><?= htmlspecialchars(str_replace('_', ' ', $c['status'])) ?></span></td>
            <td style="padding:10px;"><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
            <td style="padding:10px;"><a href="<?= url('admin/claims/view?id=' . $c['id']) ?>" class="btn btn-sm btn-secondary">Review</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($claims)): ?>
        <tr><td colspan="9" style="padding:20px; text-align:center; color:#6b7280;">No claims.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
