<?php
/**
 * Buyer Account Dashboard
 * File: app/Views/buyer/account/dashboard.php
 */

$pageTitle = 'My Account';
$user = $user ?? [];
$stats = $stats ?? [];
$recentOrders = $recentOrders ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Growcer</title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        
        .account-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 20px;
        }
        
        /* Sidebar */
        .account-sidebar {
            background: white;
            border-radius: 12px;
            padding: 0;
            height: fit-content;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .user-profile {
            text-align: center;
            padding: 30px 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: 700;
        }
        
        .user-name {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }
        
        .user-email {
            font-size: 13px;
            color: #718096;
        }
        
        .sidebar-nav {
            padding: 10px 0;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 25px;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover, .nav-item.active {
            background: #f7fafc;
            color: #4CAF50;
            border-left-color: #4CAF50;
        }
        
        .nav-item i {
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .account-main {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            font-size: 28px;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .page-header p {
            color: #718096;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px;
            border-radius: 12px;
            color: white;
        }
        
        .stat-card:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card:nth-child(4) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .stat-icon {
            font-size: 32px;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        /* Recent Orders */
        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #2d3748;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .orders-table th {
            text-align: left;
            padding: 12px;
            background: #f7fafc;
            color: #4a5568;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .orders-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #2d3748;
        }
        
        .order-id {
            font-weight: 600;
            color: #4CAF50;
        }
        
        .order-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #45a049;
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
        }
        
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        .empty-state h3 {
            font-size: 20px;
            color: #4a5568;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #718096;
            margin-bottom: 25px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        @media (max-width: 768px) {
            .account-container {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .orders-table {
                font-size: 13px;
            }
            
            .orders-table th,
            .orders-table td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../components/header.php'; ?>

    <div class="account-container">
        <!-- Sidebar -->
        <aside class="account-sidebar">
            <div class="user-profile">
                <div class="user-avatar">
                    <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="user-name">
                    <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>
                </div>
                <div class="user-email">
                    <?= htmlspecialchars($user['email'] ?? '') ?>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="<?= url('account/dashboard') ?>" class="nav-item active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= url('account/orders') ?>" class="nav-item">
                    <i class="fas fa-shopping-bag"></i>
                    <span>My Orders</span>
                </a>
                <a href="<?= url('account/addresses') ?>" class="nav-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Addresses</span>
                </a>
                <a href="<?= url('account/wishlist') ?>" class="nav-item">
                    <i class="fas fa-heart"></i>
                    <span>Wishlist</span>
                </a>
                <a href="<?= url('account/settings') ?>" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="<?= url('logout') ?>" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="account-main">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Welcome back, <?= htmlspecialchars($user['first_name'] ?? 'User') ?>! 👋</h1>
                <p>Here's what's happening with your account</p>
            </div>

            <!-- Flash Messages -->
            <?php if (hasFlash('success')): ?>
                <div class="alert alert-success">
                    ✓ <?= getFlash('success') ?>
                </div>
            <?php endif; ?>

            <?php if (hasFlash('error')): ?>
                <div class="alert alert-error">
                    ✕ <?= getFlash('error') ?>
                </div>
            <?php endif; ?>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-value"><?= $stats['total_orders'] ?? 0 ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🚚</div>
                    <div class="stat-value"><?= $stats['pending_orders'] ?? 0 ?></div>
                    <div class="stat-label">Pending Orders</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-value"><?= $stats['completed_orders'] ?? 0 ?></div>
                    <div class="stat-label">Completed Orders</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-value">$<?= number_format($stats['total_spent'] ?? 0, 2) ?></div>
                    <div class="stat-label">Total Spent</div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="section-title">Recent Orders</div>
            
            <?php if (!empty($recentOrders)): ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td class="order-id">#<?= $order['id'] ?></td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td>$<?= number_format($order['total'], 2) ?></td>
                                <td>
                                    <span class="order-status status-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('account/orders/' . $order['id']) ?>" class="btn btn-secondary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="<?= url('account/orders') ?>" class="btn btn-primary">
                        View All Orders
                    </a>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h3>No Orders Yet</h3>
                    <p>Start shopping and your orders will appear here</p>
                    <a href="<?= url('home') ?>" class="btn btn-primary">
                        Start Shopping
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>