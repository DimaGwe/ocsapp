<?php
/**
 * Admin Business Accounts - Index View
 */
$currentPage = 'business-accounts';
$pageTitle = 'Business Accounts';

ob_start();
?>

<style>
.page-header {
    margin-bottom: 32px;
}

.page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.page-header p {
    font-size: 15px;
    color: var(--gray-600);
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
.stat-box.red .value { color: #ef4444; }
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

.btn {
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background: #00b207;
    color: white;
}

.btn-primary:hover {
    background: #009906;
}

.btn-outline {
    background: white;
    color: #666;
    border: 1px solid #e5e7eb;
}

.btn-outline:hover {
    border-color: #00b207;
    color: #00b207;
}

.btn-danger {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.btn-danger:hover {
    background: #fee2e2;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

.data-table {
    width: 100%;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.data-table th,
.data-table td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid #f3f4f6;
}

.data-table th {
    background: #f9fafb;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #666;
}

.data-table tr:hover {
    background: #f9fafb;
}

.data-table .company-cell {
    font-weight: 600;
    color: #1a1a1a;
}

.data-table .contact-cell {
    font-size: 13px;
}

.data-table .contact-cell .name {
    color: #1a1a1a;
}

.data-table .contact-cell .email {
    color: #666;
    font-size: 12px;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-active { background: #dcfce7; color: #15803d; }
.badge-suspended { background: #fef2f2; color: #dc2626; }
.badge-standard { background: #f3f4f6; color: #666; }
.badge-approved { background: #dbeafe; color: #1d4ed8; }
.badge-premium { background: #fef3c7; color: #b45309; }

.actions-cell {
    display: flex;
    gap: 8px;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
    background: white;
    border-radius: 12px;
}

.empty-state i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 16px;
    display: block;
}

.empty-state h3 {
    margin-bottom: 8px;
    color: #1a1a1a;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
}

.pagination a,
.pagination span {
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 14px;
    text-decoration: none;
}

.pagination a {
    background: white;
    color: #666;
    border: 1px solid #e5e7eb;
}

.pagination a:hover {
    border-color: #00b207;
    color: #00b207;
}

.pagination .active {
    background: #00b207;
    color: white;
    border: 1px solid #00b207;
}

@media (max-width: 768px) {
    .stats-row {
        grid-template-columns: repeat(2, 1fr);
    }

    .data-table {
        display: block;
        overflow-x: auto;
    }

    .actions-cell {
        flex-direction: column;
    }
}
</style>

<div class="page-header">
    <h1><i class="fa-solid fa-building"></i> Business Accounts</h1>
    <p>Manage business accounts for the Distribution portal</p>
</div>

<?php if (!empty($_SESSION['admin_success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['admin_success']) ?>
    </div>
    <?php unset($_SESSION['admin_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['admin_error'])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['admin_error']) ?>
    </div>
    <?php unset($_SESSION['admin_error']); ?>
<?php endif; ?>

<!-- Stats Row -->
<div class="stats-row">
    <div class="stat-box">
        <div class="value"><?= $stats['total'] ?? 0 ?></div>
        <div class="label">Total Accounts</div>
    </div>
    <div class="stat-box green">
        <div class="value"><?= $stats['active'] ?? 0 ?></div>
        <div class="label">Active</div>
    </div>
    <div class="stat-box red">
        <div class="value"><?= $stats['suspended'] ?? 0 ?></div>
        <div class="label">Suspended</div>
    </div>
    <div class="stat-box blue">
        <div class="value"><?= $stats['approved'] ?? 0 ?></div>
        <div class="label">Approved Tier</div>
    </div>
    <div class="stat-box purple">
        <div class="value"><?= $stats['premium'] ?? 0 ?></div>
        <div class="label">Premium Tier</div>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('admin/business-accounts') ?>">
    <div class="filters-row">
        <input type="text"
               name="search"
               class="search-input"
               placeholder="Search by company, name, or email..."
               value="<?= htmlspecialchars($search ?? '') ?>">

        <select name="status">
            <option value="">All Status</option>
            <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="suspended" <?= ($status ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
        </select>

        <select name="tier">
            <option value="">All Tiers</option>
            <option value="standard" <?= ($tier ?? '') === 'standard' ? 'selected' : '' ?>>Standard</option>
            <option value="approved" <?= ($tier ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="premium" <?= ($tier ?? '') === 'premium' ? 'selected' : '' ?>>Premium</option>
        </select>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Search
        </button>

        <?php if (($search ?? '') || ($status ?? '') || ($tier ?? '')): ?>
            <a href="<?= url('admin/business-accounts') ?>" class="btn btn-outline">
                <i class="fas fa-times"></i> Clear
            </a>
        <?php endif; ?>
    </div>
</form>

<!-- Data Table -->
<?php if (empty($businesses)): ?>
    <div class="empty-state">
        <i class="fas fa-building"></i>
        <h3>No business accounts found</h3>
        <p>Business accounts will appear here once they register through the Distribution portal.</p>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Company</th>
                <th>Contact</th>
                <th>Location</th>
                <th>Tier</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($businesses as $b): ?>
                <tr>
                    <td class="company-cell">
                        <?= htmlspecialchars($b['company_name'] ?? 'N/A') ?>
                    </td>
                    <td class="contact-cell">
                        <div class="name"><?= htmlspecialchars(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? '')) ?></div>
                        <div class="email"><?= htmlspecialchars($b['email'] ?? '') ?></div>
                    </td>
                    <td>
                        <?= htmlspecialchars($b['delivery_city'] ?? '') ?><?= !empty($b['delivery_city']) && !empty($b['delivery_province']) ? ', ' : '' ?><?= htmlspecialchars($b['delivery_province'] ?? '') ?>
                    </td>
                    <td>
                        <?php
                        $tierClass = [
                            'standard' => 'badge-standard',
                            'approved' => 'badge-approved',
                            'premium' => 'badge-premium'
                        ];
                        $tierLabel = ucfirst($b['account_tier'] ?? 'standard');
                        $badgeClass = $tierClass[$b['account_tier'] ?? 'standard'] ?? 'badge-standard';
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= $tierLabel ?></span>
                    </td>
                    <td>
                        <?php
                        $statusClass = ($b['business_status'] ?? 'active') === 'active' ? 'badge-active' : 'badge-suspended';
                        $statusLabel = ucfirst($b['business_status'] ?? 'Active');
                        ?>
                        <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                    </td>
                    <td>
                        <?= !empty($b['created_at']) ? date('M j, Y', strtotime($b['created_at'])) : 'N/A' ?>
                    </td>
                    <td class="actions-cell">
                        <a href="<?= url('admin/business-accounts/view?id=' . ($b['business_id'] ?? $b['id'] ?? 0)) ?>" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>

                        <?php if (($b['business_status'] ?? 'active') === 'active'): ?>
                            <form method="POST" action="<?= url('admin/business-accounts/suspend') ?>" style="display:inline;" onsubmit="return confirm('Suspend this business account?')">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= $b['business_id'] ?? $b['id'] ?? 0 ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="<?= url('admin/business-accounts/activate') ?>" style="display:inline;">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= $b['business_id'] ?? $b['id'] ?? 0 ?>">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if (($totalPages ?? 1) > 1): ?>
        <div class="pagination">
            <?php if (($page ?? 1) > 1): ?>
                <a href="<?= url('admin/business-accounts?page=' . (($page ?? 1) - 1) . '&search=' . urlencode($search ?? '') . '&status=' . urlencode($status ?? '') . '&tier=' . urlencode($tier ?? '')) ?>">
                    &laquo; Prev
                </a>
            <?php endif; ?>

            <?php for ($i = max(1, ($page ?? 1) - 2); $i <= min($totalPages ?? 1, ($page ?? 1) + 2); $i++): ?>
                <?php if ($i === ($page ?? 1)): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= url('admin/business-accounts?page=' . $i . '&search=' . urlencode($search ?? '') . '&status=' . urlencode($status ?? '') . '&tier=' . urlencode($tier ?? '')) ?>">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if (($page ?? 1) < ($totalPages ?? 1)): ?>
                <a href="<?= url('admin/business-accounts?page=' . (($page ?? 1) + 1) . '&search=' . urlencode($search ?? '') . '&status=' . urlencode($status ?? '') . '&tier=' . urlencode($tier ?? '')) ?>">
                    Next &raquo;
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
