<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Vendor Dashboard' ?> - OCSAPP</title>
    <?= csrfMeta() ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #1f2937;
        }

        .dashboard-nav {
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            color: white;
            padding: 20px 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-nav h1 {
            font-size: 24px;
        }

        .dashboard-nav .logout-btn {
            background: white;
            color: #00b207;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .dashboard-nav .logout-btn:hover {
            background: #f3f4f6;
        }

        .dashboard-content {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .stat-card .stat-icon {
            font-size: 36px;
            margin-bottom: 15px;
            display: block;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #00b207;
            display: block;
            margin-bottom: 5px;
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dashboard-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .dashboard-section h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #1f2937;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th,
        .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .orders-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
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

        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .approve-btn, .reject-btn {
            padding: 6px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .approve-btn {
            background: #10b981;
            color: white;
        }

        .approve-btn:hover {
            background: #059669;
        }

        .reject-btn {
            background: #ef4444;
            color: white;
            margin-left: 8px;
        }

        .reject-btn:hover {
            background: #dc2626;
        }

        @media (max-width: 768px) {
            .dashboard-nav {
                flex-direction: column;
                gap: 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="dashboard-nav">
        <h1>📊 <?= htmlspecialchars($vendor['company_name'] ?? 'Vendor Dashboard') ?></h1>
        <a href="<?= url('vendor/logout') ?>" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>

    <div class="dashboard-content">
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-icon">📦</span>
                <span class="stat-value"><?= number_format($stats['total_products'] ?? 0) ?></span>
                <span class="stat-label">Total Products</span>
            </div>

            <div class="stat-card">
                <span class="stat-icon">📋</span>
                <span class="stat-value"><?= number_format($stats['total_orders'] ?? 0) ?></span>
                <span class="stat-label">Total Orders</span>
            </div>

            <div class="stat-card">
                <span class="stat-icon">⏳</span>
                <span class="stat-value"><?= number_format($stats['pending_orders'] ?? 0) ?></span>
                <span class="stat-label">Pending Approval</span>
            </div>

            <div class="stat-card">
                <span class="stat-icon">💰</span>
                <span class="stat-value">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></span>
                <span class="stat-label">Total Revenue</span>
            </div>
        </div>

        <!-- Pending Orders -->
        <?php if (!empty($pendingOrders)): ?>
        <div class="dashboard-section">
            <h2>⏳ Pending Orders (<?= count($pendingOrders) ?>)</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingOrders as $order): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($order['order_number']) ?></strong></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= date('M j, Y', strtotime($order['order_date'])) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td><span class="status-badge status-pending">Pending</span></td>
                        <td>
                            <button class="approve-btn" onclick="approveOrder(<?= $order['id'] ?>)">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="reject-btn" onclick="rejectOrder(<?= $order['id'] ?>)">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="dashboard-section">
            <h2>⏳ Pending Orders</h2>
            <p style="color: #6b7280; text-align: center; padding: 40px 0;">
                <i class="fas fa-inbox" style="font-size: 48px; display: block; margin-bottom: 15px;"></i>
                No pending orders at this time
            </p>
        </div>
        <?php endif; ?>

        <!-- Your Products -->
        <?php if (!empty($products)): ?>
        <div class="dashboard-section">
            <h2>📦 Your Products (<?= count($products) ?>)</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Retail Price</th>
                        <th>Your Cost</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($product['product_name']) ?></strong></td>
                        <td><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></td>
                        <td>$<?= number_format($product['base_price'], 2) ?></td>
                        <td>$<?= number_format($product['vendor_cost'] ?? 0, 2) ?></td>
                        <td><span class="status-badge status-approved">Active</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function approveOrder(orderId) {
            if (!confirm('Are you sure you want to approve this order?')) {
                return;
            }

            fetch('<?= url('vendor/order/approve') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: 'order_item_id=' + orderId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order approved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error approving order');
                console.error(error);
            });
        }

        function rejectOrder(orderId) {
            const reason = prompt('Please provide a reason for rejection:');

            if (!reason) {
                alert('Rejection reason is required');
                return;
            }

            fetch('<?= url('vendor/order/reject') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: 'order_item_id=' + orderId + '&reason=' + encodeURIComponent(reason)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order rejected successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error rejecting order');
                console.error(error);
            });
        }
    </script>
</body>
</html>
