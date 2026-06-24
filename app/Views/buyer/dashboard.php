<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Buyer Dashboard' ?> - Growcer</title>
    <?= csrfMeta() ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            color: #2d3748;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .dashboard-header h1 {
            font-size: 32px;
            margin-bottom: 5px;
        }
        
        .dashboard-header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.12);
        }
        
        .stat-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-card.orders .icon { background: #e3f2fd; color: #1976d2; }
        .stat-card.pending .icon { background: #fff3e0; color: #f57c00; }
        .stat-card.completed .icon { background: #e8f5e9; color: #388e3c; }
        .stat-card.spent .icon { background: #f3e5f5; color: #7b1fa2; }
        
        .stat-card .label {
            color: #718096;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
        }
        
        .content-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2d3748;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .orders-table th {
            background: #f7fafc;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .orders-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .orders-table tr:hover {
            background: #f7fafc;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending { background: #fff3e0; color: #f57c00; }
        .status-processing { background: #e3f2fd; color: #1976d2; }
        .status-shipped { background: #e1f5fe; color: #0277bd; }
        .status-delivered { background: #e8f5e9; color: #388e3c; }
        .status-cancelled { background: #ffebee; color: #c62828; }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }
        
        .btn-secondary:hover {
            background: #cbd5e0;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }
        
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .quick-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .nav-bar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-brand {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #667eea;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-header h1 {
                font-size: 24px;
            }
            
            .orders-table {
                font-size: 14px;
            }
            
            .orders-table th, .orders-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="nav-bar">
        <a href="<?= url('/') ?>" class="nav-brand">Growcer</a>
        <div class="nav-links">
            <a href="<?= url('/') ?>">Shop</a>
            <a href="<?= url('buyer/dashboard') ?>">Dashboard</a>
            <a href="<?= url('account/orders') ?>">Orders</a>
            <a href="<?= url('profile') ?>">Profile</a>
            <a href="<?= url('logout') ?>">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1>Welcome back, <?= htmlspecialchars(userName()) ?>! 👋</h1>
            <p>Here's what's happening with your orders today</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card orders">
                <div class="icon">📦</div>
                <div class="label">Total Orders</div>
                <div class="value"><?= $stats['total_orders'] ?? 0 ?></div>
            </div>
            
            <div class="stat-card pending">
                <div class="icon">⏳</div>
                <div class="label">Pending Orders</div>
                <div class="value"><?= $stats['pending_orders'] ?? 0 ?></div>
            </div>
            
            <div class="stat-card completed">
                <div class="icon">✅</div>
                <div class="label">Completed</div>
                <div class="value"><?= $stats['completed_orders'] ?? 0 ?></div>
            </div>
            
            <div class="stat-card spent">
                <div class="icon">💰</div>
                <div class="label">Total Spent</div>
                <div class="value">$<?= number_format($stats['total_spent'] ?? 0, 2) ?></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="content-section">
            <h2 class="section-title">Quick Actions</h2>
            <div class="quick-actions">
                <a href="<?= url('/') ?>" class="btn btn-primary">Continue Shopping</a>
                <a href="<?= url('account/orders') ?>" class="btn btn-secondary">View All Orders</a>
                <a href="<?= url('account/wishlist') ?>" class="btn btn-secondary">My Wishlist (<?= $wishlistCount ?? 0 ?>)</a>
                <a href="<?= url('account/addresses') ?>" class="btn btn-secondary">Manage Addresses</a>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="content-section">
            <h2 class="section-title">Recent Orders</h2>
            
            <?php if (!empty($recentOrders)): ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></strong></td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td><?= $order['items_count'] ?? 0 ?> items</td>
                                <td><strong>$<?= number_format($order['total_amount'], 2) ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('account/orders/' . $order['id']) ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 14px;">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h3>No Orders Yet</h3>
                    <p>Start shopping to see your orders here!</p>
                    <a href="<?= url('/') ?>" class="btn btn-primary" style="margin-top: 20px;">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
