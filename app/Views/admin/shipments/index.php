<?php
$currentPage = 'shipments';
$pageTitle = 'Shipment Management';
ob_start();
?>

<div class="content-header">
    <h1><i class="fas fa-truck"></i> Shipment Management</h1>
    <p>Manage outbound shipments and distribution</p>
</div>

<!-- Stats -->
<div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background: #dbeafe; color: #3b82f6;"><i class="fas fa-clock"></i></div>
        <div class="stat-value"><?= $stats['pending_quote'] ?? 0 ?></div>
        <div class="stat-label">Pending Quote</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;"><i class="fas fa-truck-loading"></i></div>
        <div class="stat-value"><?= $stats['in_progress'] ?? 0 ?></div>
        <div class="stat-label">In Progress</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #d1fae5; color: #10b981;"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value"><?= $stats['completed'] ?? 0 ?></div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #f3f4f6; color: #666;"><i class="fas fa-box"></i></div>
        <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
        <div class="stat-label">Total</div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body" style="padding: 16px;">
        <form method="GET" action="<?= url('admin/shipments') ?>" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <select name="status" class="form-control" style="width: auto;">
                <option value="">All Status</option>
                <option value="submitted" <?= ($currentStatus ?? '') === 'submitted' ? 'selected' : '' ?>>Pending Quote</option>
                <option value="quoted" <?= ($currentStatus ?? '') === 'quoted' ? 'selected' : '' ?>>Quoted</option>
                <option value="paid" <?= ($currentStatus ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="scheduled" <?= ($currentStatus ?? '') === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                <option value="picked_up" <?= ($currentStatus ?? '') === 'picked_up' ? 'selected' : '' ?>>Picked Up</option>
                <option value="in_transit" <?= ($currentStatus ?? '') === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
                <option value="delivered" <?= ($currentStatus ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="completed" <?= ($currentStatus ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <input type="text" name="search" class="form-control" placeholder="Search shipment or company..."
                   value="<?= htmlspecialchars($search ?? '') ?>" style="width: 250px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            <?php if (!empty($currentStatus) || !empty($search)): ?>
                <a href="<?= url('admin/shipments') ?>" class="btn btn-outline">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Shipments Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <?php if (empty($shipments)): ?>
            <div style="text-align: center; padding: 60px 20px; color: #666;">
                <i class="fas fa-truck" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                <h3 style="color: #1a1a1a; margin-bottom: 8px;">No shipments found</h3>
                <p>Shipments will appear here when businesses create them.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Shipment</th>
                            <th>Business</th>
                            <th>Type</th>
                            <th>Route</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shipments as $shipment): ?>
                            <tr>
                                <td>
                                    <a href="<?= url('admin/shipments/view?id=' . $shipment['id']) ?>" style="color: #00b207; font-weight: 600;">
                                        <?= htmlspecialchars($shipment['shipment_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($shipment['company_name']) ?></strong>
                                    <div style="font-size: 12px; color: #666;"><?= htmlspecialchars($shipment['contact_email']) ?></div>
                                </td>
                                <td>
                                    <?php
                                    $typeLabels = ['parcel' => 'Parcel', 'product_fulfillment' => 'Product', 'multi_drop' => 'Multi-Drop'];
                                    echo $typeLabels[$shipment['shipment_type']] ?? 'Parcel';
                                    ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($shipment['pickup_city']) ?> &rarr;
                                    <?php if ($shipment['is_multi_drop']): ?>
                                        <?= $shipment['destinations_count'] ?> stops
                                    <?php else: ?>
                                        <?= htmlspecialchars($shipment['destination_city']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $shipment['status'] ?>">
                                        <?= ucwords(str_replace('_', ' ', $shipment['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($shipment['total_amount'] > 0): ?>
                                        $<?= number_format($shipment['total_amount'], 2) ?>
                                    <?php elseif ($shipment['quote_total']): ?>
                                        $<?= number_format($shipment['quote_total'], 2) ?>
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 13px; color: #666;">
                                    <?= date('M j, Y', strtotime($shipment['created_at'])) ?>
                                </td>
                                <td>
                                    <a href="<?= url('admin/shipments/view?id=' . $shipment['id']) ?>" class="btn btn-sm btn-outline">
                                        <?= $shipment['status'] === 'submitted' ? 'Quote' : 'View' ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div style="padding: 20px; display: flex; justify-content: center; gap: 8px;">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= url('admin/shipments?page=' . ($currentPage - 1) . ($currentStatus ? '&status=' . $currentStatus : '') . ($search ? '&search=' . urlencode($search) : '')) ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <a href="<?= url('admin/shipments?page=' . $i . ($currentStatus ? '&status=' . $currentStatus : '') . ($search ? '&search=' . urlencode($search) : '')) ?>"
                           class="btn btn-sm <?= $i === $currentPage ? 'btn-primary' : 'btn-outline' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= url('admin/shipments?page=' . ($currentPage + 1) . ($currentStatus ? '&status=' . $currentStatus : '') . ($search ? '&search=' . urlencode($search) : '')) ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .stat-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px; }
    .stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .stat-value { font-size: 24px; font-weight: 700; color: #1a1a1a; }
    .stat-label { font-size: 13px; color: #666; }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .badge-draft { background: #f3f4f6; color: #666; }
    .badge-submitted { background: #dbeafe; color: #1d4ed8; }
    .badge-quoted { background: #fef3c7; color: #b45309; }
    .badge-pending_payment { background: #fee2e2; color: #dc2626; }
    .badge-paid { background: #d1fae5; color: #059669; }
    .badge-scheduled { background: #e0e7ff; color: #4f46e5; }
    .badge-picked_up { background: #fce7f3; color: #db2777; }
    .badge-in_transit { background: #cffafe; color: #0891b2; }
    .badge-delivered, .badge-completed { background: #d1fae5; color: #059669; }
    .badge-cancelled { background: #fee2e2; color: #dc2626; }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layout.php';
?>
