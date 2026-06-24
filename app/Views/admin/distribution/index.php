<?php
$currentPage = 'distribution';
$pageTitle = 'Distribution Requests';

// Start output buffering to capture content
ob_start();
?>

<style>
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.stat-box {
    background: white;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.stat-box .value {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
}

.stat-box .label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 4px;
}

.stat-box.green .value { color: #00b207; }
.stat-box.blue .value { color: #3b82f6; }
.stat-box.orange .value { color: #f97316; }
.stat-box.purple .value { color: #a855f7; }

.filters-row {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    align-items: center;
}

.filters-row input,
.filters-row select {
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
}

.filters-row input:focus,
.filters-row select:focus {
    outline: none;
    border-color: #00b207;
}

.filters-row .search-input {
    flex: 1;
    min-width: 200px;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}

.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 14px 16px;
    text-align: left;
}

th {
    background: #f8fafc;
    font-size: 11px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    border-top: 1px solid #f3f4f6;
    font-size: 14px;
}

tr:hover {
    background: #f8fafc;
}

.company-name {
    font-weight: 600;
    color: #1a1a1a;
}

.request-number {
    font-weight: 600;
    color: #00b207;
}

.request-name {
    font-size: 12px;
    color: #666;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-draft { background: #f3f4f6; color: #666; }
.badge-submitted { background: #dbeafe; color: #1d4ed8; }
.badge-quoted { background: #fef3c7; color: #b45309; }
.badge-pending_payment { background: #fee2e2; color: #dc2626; }
.badge-paid { background: #d1fae5; color: #059669; }
.badge-processing { background: #e0e7ff; color: #4f46e5; }
.badge-ready { background: #cffafe; color: #0891b2; }
.badge-completed { background: #d1fae5; color: #059669; }
.badge-cancelled { background: #fef2f2; color: #991b1b; }

.item-count {
    display: flex;
    gap: 8px;
    font-size: 12px;
    color: #666;
}

.item-count span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.btn-view {
    padding: 6px 12px;
    background: #f3f4f6;
    color: #666;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-view:hover {
    background: #00b207;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 64px 24px;
    color: #666;
}

.empty-state i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 16px;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    padding: 20px;
}

.pagination a, .pagination span {
    padding: 8px 14px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    color: #666;
    text-decoration: none;
}

.pagination a:hover {
    background: #00b207;
    color: white;
    border-color: #00b207;
}

.pagination .active {
    background: #00b207;
    color: white;
    border-color: #00b207;
}

.alert {
    padding: 14px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success { background: #d1fae5; color: #065f46; }
.alert-error { background: #fee2e2; color: #991b1b; }

.needs-action {
    background: #fef3c7;
}

.needs-supplier-payment {
    background: #fff7ed;
    border-left: 3px solid #f97316;
}
</style>

<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 600; color: #1a1a1a;">Distribution Requests</h1>
        <p style="font-size: 14px; color: #666; margin-top: 4px;">Manage procurement requests from business accounts</p>
    </div>
</div>

<?php if ($flash = getFlash('success')): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>
<?php if ($flash = getFlash('error')): ?>
    <div class="alert alert-error"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-box">
        <div class="value"><?= $stats['total'] ?? 0 ?></div>
        <div class="label">Total Requests</div>
    </div>
    <div class="stat-box orange">
        <div class="value"><?= $stats['pending_review'] ?? 0 ?></div>
        <div class="label">Pending Review</div>
    </div>
    <div class="stat-box orange">
        <div class="value"><?= $stats['needs_supplier_payment'] ?? 0 ?></div>
        <div class="label">Pay Suppliers</div>
    </div>
    <div class="stat-box blue">
        <div class="value"><?= $stats['in_progress'] ?? 0 ?></div>
        <div class="label">In Progress</div>
    </div>
    <div class="stat-box green">
        <div class="value"><?= $stats['completed'] ?? 0 ?></div>
        <div class="label">Completed</div>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('admin/distribution') ?>" class="filters-row">
    <input type="text" name="search" class="search-input" placeholder="Search by request #, name, or company..."
           value="<?= htmlspecialchars($search) ?>">
    <select name="status">
        <option value="">All Statuses</option>
        <option value="submitted" <?= $currentStatus === 'submitted' ? 'selected' : '' ?>>Submitted</option>
        <option value="quoted" <?= $currentStatus === 'quoted' ? 'selected' : '' ?>>Quoted</option>
        <option value="pending_payment" <?= $currentStatus === 'pending_payment' ? 'selected' : '' ?>>Pending Payment</option>
        <option value="paid" <?= $currentStatus === 'paid' ? 'selected' : '' ?>>Paid</option>
        <option value="processing" <?= $currentStatus === 'processing' ? 'selected' : '' ?>>Processing</option>
        <option value="ready" <?= $currentStatus === 'ready' ? 'selected' : '' ?>>Ready</option>
        <option value="completed" <?= $currentStatus === 'completed' ? 'selected' : '' ?>>Completed</option>
        <option value="cancelled" <?= $currentStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        <option value="draft" <?= $currentStatus === 'draft' ? 'selected' : '' ?>>Draft</option>
    </select>
    <button type="submit" class="btn-view" style="padding: 10px 16px;">
        <i class="fas fa-search"></i> Filter
    </button>
    <?php if ($search || $currentStatus): ?>
        <a href="<?= url('admin/distribution') ?>" class="btn-view" style="padding: 10px 16px;">
            <i class="fas fa-times"></i> Clear
        </a>
    <?php endif; ?>
</form>

<!-- Requests Table -->
<div class="card">
    <?php if (empty($requests)): ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3 style="font-size: 16px; color: #1a1a1a; margin-bottom: 8px;">No requests found</h3>
            <p>No distribution requests match your criteria.</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Business</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <?php
                            $rowClass = '';
                            if ($request['status'] === 'submitted') $rowClass = 'needs-action';
                            elseif ($request['status'] === 'paid') $rowClass = 'needs-supplier-payment';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td>
                                <div class="request-number"><?= htmlspecialchars($request['request_number']) ?></div>
                                <div class="request-name"><?= htmlspecialchars($request['request_name']) ?></div>
                            </td>
                            <td>
                                <div class="company-name"><?= htmlspecialchars($request['company_name']) ?></div>
                                <div style="font-size: 12px; color: #666;"><?= htmlspecialchars($request['contact_email']) ?></div>
                            </td>
                            <td>
                                <div class="item-count">
                                    <?php if ($request['catalog_items_count'] > 0): ?>
                                        <span><i class="fas fa-box"></i> <?= $request['catalog_items_count'] ?></span>
                                    <?php endif; ?>
                                    <?php if ($request['shopping_items_count'] > 0): ?>
                                        <span><i class="fas fa-list"></i> <?= $request['shopping_items_count'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-<?= $request['status'] ?>">
                                    <?= ucwords(str_replace('_', ' ', $request['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($request['invoice_number']): ?>
                                    <span style="font-weight: 500;"><?= htmlspecialchars($request['invoice_number']) ?></span>
                                    <br>
                                    <span style="font-size: 12px; color: #666;">$<?= number_format($request['invoice_total'], 2) ?></span>
                                <?php else: ?>
                                    <span style="color: #999;">--</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 13px; color: #666;">
                                <?= date('M j, Y', strtotime($request['created_at'])) ?>
                                <br>
                                <span style="font-size: 11px;"><?= date('g:i A', strtotime($request['created_at'])) ?></span>
                            </td>
                            <td>
                                <a href="<?= url('admin/distribution/view?id=' . $request['id']) ?>" class="btn-view"
                                   <?= $request['status'] === 'paid' ? 'style="background:#fff7ed;color:#c2410c;font-weight:600;"' : '' ?>>
                                    <?php if ($request['status'] === 'submitted'): ?>Review
                                    <?php elseif ($request['status'] === 'paid'): ?>Pay Suppliers
                                    <?php else: ?>View<?php endif; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= url('admin/distribution?page=' . ($currentPage - 1) . ($currentStatus ? '&status=' . $currentStatus : '') . ($search ? '&search=' . urlencode($search) : '')) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= url('admin/distribution?page=' . $i . ($currentStatus ? '&status=' . $currentStatus : '') . ($search ? '&search=' . urlencode($search) : '')) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= url('admin/distribution?page=' . ($currentPage + 1) . ($currentStatus ? '&status=' . $currentStatus : '') . ($search ? '&search=' . urlencode($search) : '')) ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
// Capture the content and include layout
$content = ob_get_clean();
include dirname(__DIR__) . '/layout.php';
?>
