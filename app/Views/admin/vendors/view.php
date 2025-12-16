<?php
/**
 * Admin View Vendor Details - OCSAPP
 */
?>
<?php ob_start(); ?>

<style>
.vendor-view-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

.vendor-sidebar {
    background: white;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid #e5e7eb;
    height: fit-content;
}

.vendor-logo-large {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 700;
    color: var(--primary);
    margin: 0 auto 20px;
}

.vendor-name-header {
    text-align: center;
    margin-bottom: 20px;
}

.vendor-name-header h2 {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 8px;
}

.vendor-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.vendor-info-list li {
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.vendor-info-list li:last-child {
    border-bottom: none;
}

.vendor-info-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
}

.vendor-info-value {
    font-size: 14px;
    color: #1f2937;
    font-weight: 600;
}

.vendor-actions-card {
    background: #f9fafb;
    border-radius: 8px;
    padding: 16px;
    margin-top: 20px;
}

.action-btn {
    display: block;
    width: 100%;
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 6px;
    text-align: center;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
}

.action-btn:last-child {
    margin-bottom: 0;
}

.vendor-main-content {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.stat-card {
    background: white;
    padding: 24px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.stat-card-value {
    font-size: 36px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}

.stat-card-label {
    font-size: 13px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.content-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid #e5e7eb;
}

.content-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f3f4f6;
}

.content-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th {
    text-align: left;
    padding: 12px;
    background: #f9fafb;
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
}

table td {
    padding: 12px;
    border-top: 1px solid #f3f4f6;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

@media (max-width: 1024px) {
    .vendor-view-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <a href="<?= url('admin/vendors') ?>" style="color: #6b7280; text-decoration: none; margin-bottom: 8px; display: inline-block;">
                <i class="fa-solid fa-arrow-left"></i> Back to Vendors
            </a>
            <h1 style="font-size: 28px; font-weight: 700; margin: 0;"><?= htmlspecialchars($vendor['company_name']) ?></h1>
        </div>
        <div style="display: flex; gap: 12px;">
            <?php if ($vendor['status'] === 'pending'): ?>
                <button onclick="approveVendor(<?= $vendor['id'] ?>)" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fa-solid fa-check"></i> Approve Vendor
                </button>
            <?php elseif ($vendor['status'] === 'active'): ?>
                <button onclick="suspendVendor(<?= $vendor['id'] ?>)" style="padding: 10px 20px; background: #ef4444; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fa-solid fa-ban"></i> Suspend
                </button>
            <?php elseif ($vendor['status'] === 'suspended'): ?>
                <button onclick="activateVendor(<?= $vendor['id'] ?>)" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fa-solid fa-check"></i> Activate
                </button>
            <?php endif; ?>
            <a href="<?= url('admin/vendors/edit?id=' . $vendor['id']) ?>" style="padding: 10px 20px; background: #3b82f6; color: white; border-radius: 8px; font-weight: 600; text-decoration: none;">
                <i class="fa-solid fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="vendor-view-grid">
        <!-- Sidebar -->
        <div class="vendor-sidebar">
            <div class="vendor-logo-large">
                <?= strtoupper(substr($vendor['company_name'], 0, 2)) ?>
            </div>

            <div class="vendor-name-header">
                <span class="status-badge status-<?= $vendor['status'] ?>"><?= ucfirst($vendor['status']) ?></span>
            </div>

            <ul class="vendor-info-list">
                <li>
                    <div class="vendor-info-label">Email</div>
                    <div class="vendor-info-value"><?= htmlspecialchars($vendor['email']) ?></div>
                </li>
                <?php if (!empty($vendor['phone'])): ?>
                <li>
                    <div class="vendor-info-label">Phone</div>
                    <div class="vendor-info-value"><?= htmlspecialchars($vendor['phone']) ?></div>
                </li>
                <?php endif; ?>
                <?php if (!empty($vendor['contact_person'])): ?>
                <li>
                    <div class="vendor-info-label">Contact Person</div>
                    <div class="vendor-info-value"><?= htmlspecialchars($vendor['contact_person']) ?></div>
                </li>
                <?php endif; ?>
                <li>
                    <div class="vendor-info-label">Joined</div>
                    <div class="vendor-info-value"><?= date('M j, Y', strtotime($vendor['created_at'])) ?></div>
                </li>
                <?php if ($vendor['last_login_at']): ?>
                <li>
                    <div class="vendor-info-label">Last Login</div>
                    <div class="vendor-info-value"><?= date('M j, Y g:i A', strtotime($vendor['last_login_at'])) ?></div>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="vendor-main-content">
            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-value"><?= number_format($stats['total_products'] ?? 0) ?></div>
                    <div class="stat-card-label">Products</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value"><?= number_format($stats['total_orders'] ?? 0) ?></div>
                    <div class="stat-card-label">Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value"><?= number_format($stats['pending_orders'] ?? 0) ?></div>
                    <div class="stat-card-label">Pending Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></div>
                    <div class="stat-card-label">Total Revenue</div>
                </div>
            </div>

            <!-- Products -->
            <div class="content-card">
                <div class="content-card-header">
                    <h3 class="content-card-title"><i class="fa-solid fa-box"></i> Products (<?= count($products) ?>)</h3>
                </div>

                <?php if (empty($products)): ?>
                    <div style="text-align: center; padding: 40px; color: #9ca3af;">
                        No products linked to this vendor yet
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Vendor Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <?php if ($product['image']): ?>
                                            <img src="<?= asset('images/products/' . $product['image']) ?>" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                                        <?php endif; ?>
                                        <strong><?= htmlspecialchars($product['name']) ?></strong>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($product['vendor_sku'] ?? '-') ?></td>
                                <td>$<?= number_format($product['vendor_cost'] ?? 0, 2) ?></td>
                                <td>
                                    <?php if ($product['is_active']): ?>
                                        <span class="status-badge status-active">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Recent Orders -->
            <div class="content-card">
                <div class="content-card-header">
                    <h3 class="content-card-title"><i class="fa-solid fa-cart-shopping"></i> Recent Orders</h3>
                </div>

                <?php if (empty($orders)): ?>
                    <div style="text-align: center; padding: 40px; color: #9ca3af;">
                        No orders yet
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Quantity</th>
                                <th>Total Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($order['order_number']) ?></strong></td>
                                <td><?= date('M j, Y', strtotime($order['order_date'])) ?></td>
                                <td><?= $order['quantity'] ?></td>
                                <td>$<?= number_format($order['vendor_cost_total'] ?? 0, 2) ?></td>
                                <td><span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function approveVendor(id) {
    if (!confirm('Are you sure you want to approve this vendor?')) return;

    fetch('<?= url('admin/vendors/approve') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            '<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>': '<?= csrfToken() ?>',
            'id': id
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function suspendVendor(id) {
    if (!confirm('Are you sure you want to suspend this vendor?')) return;

    fetch('<?= url('admin/vendors/suspend') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            '<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>': '<?= csrfToken() ?>',
            'id': id
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function activateVendor(id) {
    if (!confirm('Are you sure you want to activate this vendor?')) return;

    fetch('<?= url('admin/vendors/activate') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            '<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>': '<?= csrfToken() ?>',
            'id': id
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>
