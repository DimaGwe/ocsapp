<?php
/**
 * Admin Vendors List - OCSAPP
 */

$currentLang = $_SESSION['language'] ?? 'fr';
?>
<?php ob_start(); ?>

<style>
.vendors-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.vendors-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.vendors-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.2s;
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.stat-card.active {
    border-color: var(--primary);
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

.stat-label {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
}

.vendors-controls {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}

.search-box {
    flex: 1;
    max-width: 400px;
}

.search-box input {
    width: 100%;
    padding: 10px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
}

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    font-size: 14px;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-700);
}

.vendors-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.vendors-table table {
    width: 100%;
    border-collapse: collapse;
}

.vendors-table thead {
    background: #f9fafb;
}

.vendors-table th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.vendors-table td {
    padding: 16px;
    border-top: 1px solid #e5e7eb;
}

.vendor-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.vendor-logo {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: var(--primary);
}

.vendor-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.vendor-name {
    font-weight: 600;
    color: #1f2937;
}

.vendor-email {
    font-size: 13px;
    color: #6b7280;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-suspended {
    background: #fee2e2;
    color: #991b1b;
}

.status-inactive {
    background: #e5e7eb;
    color: #4b5563;
}

.vendor-actions {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
}

.pagination a {
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #4b5563;
    text-decoration: none;
}

.pagination a.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}
</style>

<div class="admin-content-wrapper">
    <div class="vendors-header">
        <h1><i class="fa-solid fa-truck-field"></i> Vendors</h1>
        <div style="display: flex; gap: 12px;">
            <a href="<?= url('admin/vendors/invites') ?>" class="btn" style="background: #6b7280; color: white;">
                <i class="fa-solid fa-envelope"></i> Manage Invites
            </a>
            <a href="<?= url('admin/vendors/create') ?>" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Create Vendor
            </a>
        </div>
    </div>

    <!-- Status Filter Cards -->
    <div class="vendors-stats">
        <div class="stat-card <?= $currentStatus === 'all' ? 'active' : '' ?>" onclick="window.location='<?= url('admin/vendors') ?>'">
            <div class="stat-label">All Vendors</div>
            <div class="stat-value"><?= $totalVendors ?></div>
        </div>
        <div class="stat-card <?= $currentStatus === 'pending' ? 'active' : '' ?>" onclick="window.location='<?= url('admin/vendors?status=pending') ?>'">
            <div class="stat-label">Pending Approval</div>
            <div class="stat-value"><?= $statusCounts['pending'] ?? 0 ?></div>
        </div>
        <div class="stat-card <?= $currentStatus === 'active' ? 'active' : '' ?>" onclick="window.location='<?= url('admin/vendors?status=active') ?>'">
            <div class="stat-label">Active</div>
            <div class="stat-value"><?= $statusCounts['active'] ?? 0 ?></div>
        </div>
        <div class="stat-card <?= $currentStatus === 'suspended' ? 'active' : '' ?>" onclick="window.location='<?= url('admin/vendors?status=suspended') ?>'">
            <div class="stat-label">Suspended</div>
            <div class="stat-value"><?= $statusCounts['suspended'] ?? 0 ?></div>
        </div>
    </div>

    <!-- Search -->
    <div class="vendors-controls">
        <form action="<?= url('admin/vendors') ?>" method="GET" class="search-box">
            <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
            <input type="search" name="search" placeholder="Search vendors..." value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <!-- Vendors Table -->
    <div class="vendors-table">
        <table>
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Contact</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vendors)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">
                        <i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                        No vendors found
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($vendors as $vendor): ?>
                <tr>
                    <td>
                        <div class="vendor-info">
                            <div class="vendor-logo">
                                <?php if (!empty($vendor['logo'])): ?>
                                    <img src="<?= asset($vendor['logo']) ?>" alt="<?= htmlspecialchars($vendor['company_name']) ?>">
                                <?php else: ?>
                                    <?= strtoupper(substr($vendor['company_name'], 0, 2)) ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="vendor-name"><?= htmlspecialchars($vendor['company_name']) ?></div>
                                <div class="vendor-email"><?= htmlspecialchars($vendor['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($vendor['contact_person'])): ?>
                            <div style="font-weight: 600;"><?= htmlspecialchars($vendor['contact_person']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($vendor['phone'])): ?>
                            <div style="font-size: 13px; color: #6b7280;"><?= htmlspecialchars($vendor['phone']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= $vendor['total_products'] ?></strong> products
                    </td>
                    <td>
                        <span class="status-badge status-<?= $vendor['status'] ?>">
                            <?= ucfirst($vendor['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?= date('M j, Y', strtotime($vendor['created_at'])) ?>
                    </td>
                    <td>
                        <div class="vendor-actions">
                            <a href="<?= url('admin/vendors/view?id=' . $vendor['id']) ?>" class="btn btn-sm" style="background: #3b82f6; color: white;">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                            <a href="<?= url('admin/vendors/edit?id=' . $vendor['id']) ?>" class="btn btn-sm" style="background: #6b7280; color: white;">
                                <i class="fa-solid fa-edit"></i> Edit
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?= url('admin/vendors?page=' . $i . '&status=' . $currentStatus . '&search=' . urlencode($search)) ?>"
               class="<?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>
