<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Seller Dashboard' ?> - OCSAPP</title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/seller-dashboard.css') ?>">
</head>
<body>
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div style="position: fixed; top: 20px; right: 20px; background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999; animation: slideIn 0.3s ease-out;">
            <strong>❌ Error:</strong> <?= $_SESSION['flash']['error'] ?>
        </div>
        <?php unset($_SESSION['flash']['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash']['success'])): ?>
        <div style="position: fixed; top: 20px; right: 20px; background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999; animation: slideIn 0.3s ease-out;">
            <strong>✅ Success:</strong> <?= $_SESSION['flash']['success'] ?>
        </div>
        <?php unset($_SESSION['flash']['success']); ?>
    <?php endif; ?>

    <!-- Navigation -->
    <div class="nav-bar">
        <a href="<?= url('/') ?>" class="nav-brand">
            <i class="fas fa-store"></i> OCS Seller
        </a>
        <div class="nav-links">
            <a href="<?= url('seller/dashboard') ?>" class="active">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="<?= url('seller/inventory') ?>">
                <i class="fas fa-boxes"></i> Products
            </a>
            <a href="<?= url('seller/orders') ?>">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="<?= url('seller/shop/settings') ?>">
                <i class="fas fa-cog"></i> Shop Settings
            </a>
            <a href="<?= url('profile') ?>">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="<?= url('logout') ?>">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1><i class="fas fa-store"></i> Seller Dashboard</h1>
            <p class="shop-name"><?= htmlspecialchars($shop['name'] ?? 'Your Shop') ?></p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card products">
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="label">Total Products</div>
                <div class="value"><?= $stats['total_products'] ?? 0 ?></div>
                <small><?= $stats['active_products'] ?? 0 ?> active</small>
            </div>
            
            <div class="stat-card orders">
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="label">Total Orders</div>
                <div class="value"><?= $stats['total_orders'] ?? 0 ?></div>
            </div>
            
            <div class="stat-card revenue">
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="label">Total Revenue</div>
                <div class="value"><?= currency($stats['total_revenue'] ?? 0) ?></div>
            </div>
            
            <div class="stat-card pending">
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="label">Pending Orders</div>
                <div class="value"><?= $stats['pending_orders'] ?? 0 ?></div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <?php if (!empty($lowStockProducts)): ?>
        <div class="alert alert-warning">
            <strong><i class="fas fa-exclamation-triangle"></i> Low Stock Alert!</strong>
            <p>You have <?= count($lowStockProducts) ?> product(s) running low on stock.</p>
        </div>
        
        <div class="content-section">
            <h2 class="section-title">
                <i class="fas fa-exclamation-circle"></i> Low Stock Products
            </h2>
            <ul class="low-stock-list">
                <?php foreach ($lowStockProducts as $product): ?>
                    <li class="low-stock-item">
                        <div>
                            <strong><?= htmlspecialchars($product['name']) ?></strong>
                            <span class="low-stock-quantity">
                                <i class="fas fa-box-open"></i> Only <?= $product['stock_quantity'] ?> left
                            </span>
                        </div>
                        <a href="<?= url('seller/inventory/edit?id=' . $product['id']) ?>" class="btn btn-secondary btn-small">
                            <i class="fas fa-plus-circle"></i> Restock
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Recent Orders -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-receipt"></i> Recent Orders
                </h2>
                <a href="<?= url('seller/inventory/add') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>
            
            <?php if (!empty($recentOrders)): ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>
                                    <strong>#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td><strong><?= currency($order['total_amount']) ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('seller/orders/' . $order['id']) ?>" class="btn btn-secondary btn-small">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <p>No orders yet. Start selling!</p>
                    <a href="<?= url('seller/inventory/add') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Your First Product
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions (Optional) -->
        <div class="content-section">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i> Quick Actions
            </h2>
            <div class="quick-actions">
                <a href="<?= url('seller/inventory/add') ?>" class="quick-action-card">
                    <span class="icon"><i class="fas fa-plus-circle"></i></span>
                    <span class="label">Add Product</span>
                </a>
                <a href="<?= url('seller/inventory') ?>" class="quick-action-card">
                    <span class="icon"><i class="fas fa-boxes"></i></span>
                    <span class="label">Manage Inventory</span>
                </a>
                <a href="<?= url('seller/orders') ?>" class="quick-action-card">
                    <span class="icon"><i class="fas fa-clipboard-list"></i></span>
                    <span class="label">View All Orders</span>
                </a>
                <a href="<?= url('seller/shop/settings') ?>" class="quick-action-card">
                    <span class="icon"><i class="fas fa-store-alt"></i></span>
                    <span class="label">Shop Settings</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>