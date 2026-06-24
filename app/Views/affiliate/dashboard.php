<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Affiliate Dashboard' ?> - Growcer</title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f5f7fa; color: #2d3748; }
        .dashboard-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .dashboard-header { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #2d3748; padding: 30px; border-radius: 15px; margin-bottom: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
        .stat-card .icon { font-size: 32px; margin-bottom: 10px; }
        .stat-card .label { color: #718096; font-size: 14px; }
        .stat-card .value { font-size: 28px; font-weight: 700; color: #2d3748; }
        .content-section { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); margin-bottom: 30px; }
        .affiliate-code { background: #f7fafc; border: 2px dashed #4299e1; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
        .code { font-size: 32px; font-weight: 700; color: #4299e1; letter-spacing: 2px; }
        .referral-item { padding: 15px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; background: #4299e1; color: white; border: none; cursor: pointer; }
        .nav-bar { background: white; padding: 15px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; display: flex; justify-content: space-between; }
        .nav-brand { font-size: 24px; font-weight: 700; color: #4299e1; text-decoration: none; }
        .nav-links a { margin-left: 20px; text-decoration: none; color: #4a5568; font-weight: 500; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-completed { background: #e8f5e9; color: #388e3c; }
        .status-pending { background: #fff3e0; color: #f57c00; }
    </style>
</head>
<body>
    <div class="nav-bar">
        <a href="<?= url('/') ?>" class="nav-brand">Growcer Affiliate</a>
        <div class="nav-links">
            <a href="<?= url('affiliate/dashboard') ?>">Dashboard</a>
            <a href="<?= url('affiliate/referrals') ?>">Referrals</a>
            <a href="<?= url('profile') ?>">Profile</a>
            <a href="<?= url('logout') ?>">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Affiliate Dashboard 🤝</h1>
            <p>Track your referrals and earnings</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">👥</div>
                <div class="label">Total Referrals</div>
                <div class="value"><?= $stats['total_referrals'] ?? 0 ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">✅</div>
                <div class="label">Successful</div>
                <div class="value"><?= $stats['successful_referrals'] ?? 0 ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">💰</div>
                <div class="label">Total Earnings</div>
                <div class="value">$<?= number_format($stats['total_earnings'] ?? 0, 2) ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">⏳</div>
                <div class="label">Pending</div>
                <div class="value">$<?= number_format($stats['pending_earnings'] ?? 0, 2) ?></div>
            </div>
        </div>

        <div class="content-section">
            <h2>Your Affiliate Code</h2>
            <div class="affiliate-code">
                <p style="color: #718096; margin-bottom: 10px;">Share this code to earn commissions</p>
                <div class="code"><?= htmlspecialchars($affiliateCode ?? 'N/A') ?></div>
                <button class="btn" style="margin-top: 15px;" onclick="copyCode()">📋 Copy Code</button>
            </div>
        </div>

        <div class="content-section">
            <h2>Recent Referrals</h2>
            <?php if (!empty($recentReferrals)): ?>
                <?php foreach ($recentReferrals as $referral): ?>
                    <div class="referral-item">
                        <div>
                            <strong>Order #<?= htmlspecialchars($referral['order_number']) ?></strong>
                            <p style="color: #718096; font-size: 14px;">
                                <?= htmlspecialchars($referral['first_name'] . ' ' . $referral['last_name']) ?> - 
                                $<?= number_format($referral['total_amount'], 2) ?>
                            </p>
                        </div>
                        <div>
                            <span class="status-badge status-<?= $referral['status'] ?>">
                                <?= ucfirst($referral['status']) ?>
                            </span>
                            <p style="color: #2d3748; font-weight: 600; margin-top: 5px;">
                                +$<?= number_format($referral['commission_amount'], 2) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #718096; padding: 40px;">No referrals yet. Start sharing your code!</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function copyCode() {
            const code = '<?= $affiliateCode ?>';
            navigator.clipboard.writeText(code).then(() => {
                alert('Code copied to clipboard!');
            });
        }
    </script>
</body>
</html>
