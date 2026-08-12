<?php
/**
 * Admin Claim Review - Returns & Refund Automation (Sec 4)
 */
$currentPage = 'claims';
$pageTitle = 'Claim #' . $claim['id'];

ob_start();
?>

<div class="page-header">
    <h1>Claim #<?= $claim['id'] ?> <span class="badge"><?= htmlspecialchars(str_replace('_', ' ', $claim['status'])) ?></span></h1>
    <a href="<?= url('admin/claims') ?>">&laquo; Back to queue</a>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
    <div class="card" style="padding:20px;">
        <h3>Claim Details</h3>
        <table style="width:100%;">
            <tr><td style="padding:6px 0; color:#6b7280;">Track</td><td><?= $claim['track'] === 'A' ? 'A - Marché (B2C)' : 'B - Distribution (B2B)' ?></td></tr>
            <tr><td style="padding:6px 0; color:#6b7280;">Order</td><td><?= htmlspecialchars($claim['order_number'] ?? $claim['distribution_request_id'] ?? '-') ?> (<?= htmlspecialchars($claim['shop_name'] ?? '') ?>)</td></tr>
            <tr><td style="padding:6px 0; color:#6b7280;">Filed by</td><td><?= htmlspecialchars(trim(($claim['first_name'] ?? '') . ' ' . ($claim['last_name'] ?? ''))) ?> (<?= htmlspecialchars($claim['email'] ?? '') ?>)</td></tr>
            <tr><td style="padding:6px 0; color:#6b7280;">Type</td><td><?= htmlspecialchars(str_replace('_', ' ', $claim['claim_type'])) ?></td></tr>
            <tr><td style="padding:6px 0; color:#6b7280;">Claimed value</td><td>$<?= number_format($claim['claimed_value'], 2) ?></td></tr>
            <tr><td style="padding:6px 0; color:#6b7280;">Claim deadline</td><td><?= date('M j, Y g:ia', strtotime($claim['claim_deadline_at'])) ?></td></tr>
            <tr><td style="padding:6px 0; color:#6b7280;">Description</td><td><?= nl2br(htmlspecialchars($claim['description'] ?? '')) ?></td></tr>
        </table>

        <?php if (!empty($evidence_photos)): ?>
        <h4 style="margin-top:16px;">Claim Evidence</h4>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <?php foreach ($evidence_photos as $photo): ?>
                <img src="<?= url($photo) ?>" style="width:120px; height:120px; object-fit:cover; border-radius:6px;">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h4 style="margin-top:16px;">Delivery Evidence Chain (Sec 4.1)</h4>
        <div style="display:flex; gap:16px;">
            <div>
                <p style="color:#6b7280; font-size:13px;">Pickup photo</p>
                <?php if (!empty($evidence['pickup_photo_path'])): ?>
                    <img src="<?= url($evidence['pickup_photo_path']) ?>" style="width:150px; border-radius:6px;">
                <?php else: ?>
                    <p style="color:#dc2626;">None on file</p>
                <?php endif; ?>
            </div>
            <div>
                <p style="color:#6b7280; font-size:13px;">Delivery photo</p>
                <?php if (!empty($evidence['proof_of_delivery'])): ?>
                    <img src="<?= url($evidence['proof_of_delivery']) ?>" style="width:150px; border-radius:6px;">
                <?php else: ?>
                    <p style="color:#dc2626;">None on file</p>
                <?php endif; ?>
            </div>
            <div>
                <p style="color:#6b7280; font-size:13px;">Signature</p>
                <p><?= !empty($evidence['signature_collected']) ? '✅ Collected' : '❌ Not collected' ?></p>
            </div>
        </div>

        <?php if (!empty($claim['admin_notes'])): ?>
        <h4 style="margin-top:16px;">Admin Notes / Action Log</h4>
        <pre style="white-space:pre-wrap; background:#f9fafb; padding:10px; border-radius:6px; font-size:13px;"><?= htmlspecialchars($claim['admin_notes']) ?></pre>
        <?php endif; ?>
    </div>

    <div>
        <div class="card" style="padding:20px; margin-bottom:16px;">
            <h3>System Suggestion</h3>
            <p><strong><?= htmlspecialchars(str_replace('_', ' ', $claim['fault_determination'])) ?></strong></p>
            <p style="color:#6b7280; font-size:13px;"><?= htmlspecialchars($fault_signals['reason'] ?? '') ?></p>
            <p style="font-size:12px; color:#9ca3af;">This is a computed signal, not an AI judgment - confirm below.</p>
        </div>

        <?php if ($claim['status'] !== 'resolved'): ?>
        <div class="card" style="padding:20px; margin-bottom:16px;">
            <h3>Resolve Claim</h3>

            <form method="POST" action="<?= url('admin/claims/confirm-vendor-caused') ?>" style="margin-bottom:10px;">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $claim['id'] ?>">
                <?php if ($claim['track'] === 'B'): ?>
                    <input type="number" name="supplier_id" placeholder="Supplier ID" required style="width:100%; margin-bottom:6px; padding:6px;">
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-block">Confirm Vendor-Caused (Chargeback)</button>
            </form>

            <form method="POST" action="<?= url('admin/claims/confirm-transit-caused') ?>" style="margin-bottom:10px;">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $claim['id'] ?>">
                <button type="submit" class="btn btn-secondary btn-block">Confirm Transit-Caused (OCSAPP Absorbs)</button>
            </form>

            <form method="POST" action="<?= url('admin/claims/deny') ?>">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $claim['id'] ?>">
                <input type="text" name="admin_notes" placeholder="Reason for denial" style="width:100%; margin-bottom:6px; padding:6px;">
                <button type="submit" class="btn btn-danger btn-block">Deny Claim</button>
            </form>
        </div>
        <?php else: ?>
        <div class="card" style="padding:20px; margin-bottom:16px;">
            <h3>Resolved</h3>
            <p>Resolution: <strong><?= htmlspecialchars(str_replace('_', ' ', $claim['resolution'] ?? '')) ?></strong></p>
            <?php if ($claim['resolution'] === 'chargeback'): ?>
            <form method="POST" action="<?= url('admin/claims/dispatch-return') ?>" style="margin-top:10px;">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $claim['id'] ?>">
                <button type="submit" class="btn btn-primary btn-block">Dispatch Return / Returnless Refund</button>
            </form>
            <p style="font-size:12px; color:#9ca3af; margin-top:6px;">Auto-decides based on item value vs. reverse-trip cost.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($chargebacks)): ?>
        <div class="card" style="padding:20px;">
            <h3>Chargeback History</h3>
            <?php foreach ($chargebacks as $cb): ?>
            <div style="border-bottom:1px solid #f0f0f0; padding:8px 0;">
                <p><strong><?= htmlspecialchars($cb['party_type']) ?> #<?= $cb['party_id'] ?></strong> - $<?= number_format($cb['total_deduction'], 2) ?> (<?= htmlspecialchars($cb['status']) ?>)</p>
                <p style="font-size:12px; color:#6b7280;">Claimed $<?= number_format($cb['claimed_value'], 2) ?> + reverse fee $<?= number_format($cb['reverse_logistics_fee'], 2) ?></p>
                <p style="font-size:12px; color:#6b7280;">Dispute window ends <?= date('M j, Y', strtotime($cb['dispute_window_ends_at'])) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
