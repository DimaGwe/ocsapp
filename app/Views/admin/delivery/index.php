<?php
/**
 * OCS Admin Delivery Management Dashboard
 * File: app/Views/admin/delivery/index.php
 */

$pageTitle = 'Delivery Management';
$currentPage = 'delivery';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'delivery_management' => 'Delivery Management',
        'monitor_operations' => 'Monitor and manage delivery operations',
        'total_deliveries' => 'Total Deliveries',
        'active_deliveries' => 'Active Deliveries',
        'awaiting_driver' => 'Awaiting Driver',
        'completed' => 'Completed',
        'total_revenue' => 'Total Revenue',
        'delivery_staff' => 'Delivery Staff',
        'manage_drivers' => 'Manage drivers',
        'active_deliveries_link' => 'Active Deliveries',
        'in_progress' => 'in progress',
        'analytics' => 'Analytics',
        'view_reports' => 'View reports',
        'active_drivers' => 'Active Drivers',
        'no_active_drivers' => 'No active drivers',
        'recent_deliveries' => 'Recent Deliveries',
        'no_recent_deliveries' => 'No recent deliveries',
        'no_zone' => 'No zone'
    ],
    'fr' => [
        'delivery_management' => 'Gestion des Livraisons',
        'monitor_operations' => 'Surveiller et gérer les opérations de livraison',
        'total_deliveries' => 'Livraisons Totales',
        'active_deliveries' => 'Livraisons Actives',
        'awaiting_driver' => 'En Attente de Livreur',
        'completed' => 'Terminées',
        'total_revenue' => 'Revenu Total',
        'delivery_staff' => 'Personnel de Livraison',
        'manage_drivers' => 'Gérer les livreurs',
        'active_deliveries_link' => 'Livraisons Actives',
        'in_progress' => 'en cours',
        'analytics' => 'Analytique',
        'view_reports' => 'Voir les rapports',
        'active_drivers' => 'Livreurs Actifs',
        'no_active_drivers' => 'Aucun livreur actif',
        'recent_deliveries' => 'Livraisons Récentes',
        'no_recent_deliveries' => 'Aucune livraison récente',
        'no_zone' => 'Aucune zone'
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
    font-family: 'Poppins', sans-serif;
}

.page-subtitle {
    color: var(--gray-600);
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all var(--transition-base);
    cursor: pointer;
    border: none;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
}

.btn-secondary:hover {
    background: var(--gray-300);
}

/* Card */
.card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    transition: all var(--transition-base);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.stat-icon.total { background: var(--primary-600); color: white; }
.stat-icon.active { background: #ffedd5; color: #f97316; }
.stat-icon.awaiting { background: #fee2e2; color: #ef4444; }
.stat-icon.completed { background: #dcfce7; color: #22c55e; }
.stat-icon.revenue { background: #f3e8ff; color: #a855f7; }

.stat-label {
    font-size: 14px;
    color: var(--gray-500);
    margin-bottom: 4px;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--dark);
}

.stat-value.active { color: #f97316; }
.stat-value.awaiting { color: #ef4444; }
.stat-value.completed { color: #22c55e; }
.stat-value.revenue { color: #a855f7; }

/* Quick Actions Grid */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.quick-action-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    text-decoration: none;
    transition: all var(--transition-base);
    border: 2px solid transparent;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
}

.quick-action-content {
    display: flex;
    align-items: center;
}

.quick-action-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-right: 16px;
    flex-shrink: 0;
}

.quick-action-icon.staff { background: #dbeafe; color: #3b82f6; }
.quick-action-icon.active { background: #ffedd5; color: #f97316; }
.quick-action-icon.analytics { background: #dcfce7; color: #22c55e; }

.quick-action-text h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
}

.quick-action-text p {
    font-size: 14px;
    color: var(--gray-600);
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.content-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.card-header {
    padding: 24px 24px 16px;
    border-bottom: 1px solid var(--border);
}

.card-header h2 {
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
}

.card-body {
    padding: 24px;
}

.driver-item,
.delivery-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    margin-bottom: 12px;
    transition: all var(--transition-base);
}

.driver-item:last-child,
.delivery-item:last-child {
    margin-bottom: 0;
}

.driver-item:hover,
.delivery-item:hover {
    background: var(--gray-100);
    transform: translateY(-1px);
}

.driver-info h4,
.delivery-info h4 {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
    font-size: 14px;
}

.driver-info p,
.delivery-info p {
    font-size: 13px;
    color: var(--gray-600);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-available {
    background: #dcfce7;
    color: #166534;
}

.status-busy {
    background: #ffedd5;
    color: #9a3412;
}

.time-stamp {
    font-size: 12px;
    color: var(--gray-500);
}

.empty-state {
    text-align: center;
    padding: 32px 24px;
    color: var(--gray-500);
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-value {
        font-size: 24px;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-action-content {
        flex-direction: column;
        text-align: center;
    }
    
    .quick-action-icon {
        margin-right: 0;
        margin-bottom: 16px;
    }
}
</style>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-shipping-fast text-primary mr-2"></i>
            <?= $t['delivery_management'] ?>
        </h1>
        <p class="page-subtitle"><?= $t['monitor_operations'] ?></p>
    </div>
</div>

<?php if (hasFlash('success')): ?>
<div class="alert alert-success">
    <?= getFlash('success') ?>
</div>
<?php endif; ?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon total">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>
        <p class="stat-label"><?= $t['total_deliveries'] ?></p>
        <p class="stat-value"><?= number_format($stats['total_deliveries'] ?? 0) ?></p>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon active">
                <i class="fa-solid fa-truck"></i>
            </div>
        </div>
        <p class="stat-label"><?= $t['active_deliveries'] ?></p>
        <p class="stat-value active"><?= number_format($stats['active'] ?? 0) ?></p>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon awaiting">
                <i class="fa-solid fa-user-clock"></i>
            </div>
        </div>
        <p class="stat-label"><?= $t['awaiting_driver'] ?></p>
        <p class="stat-value awaiting"><?= number_format($stats['awaiting_driver'] ?? 0) ?></p>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon completed">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        </div>
        <p class="stat-label"><?= $t['completed'] ?></p>
        <p class="stat-value completed"><?= number_format($stats['completed'] ?? 0) ?></p>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon revenue">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
        </div>
        <p class="stat-label"><?= $t['total_revenue'] ?></p>
        <p class="stat-value revenue"><?= currency($stats['total_revenue'] ?? 0) ?></p>
    </div>

    <?php if (!empty($distributionStats)): ?>
    <div class="stat-card" style="border-left: 3px solid #6366f1;">
        <div class="stat-header">
            <div class="stat-icon" style="background: #e0e7ff; color: #4f46e5;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>
        <p class="stat-label">B2B Distribution</p>
        <p class="stat-value" style="color: #4f46e5;"><?= number_format($distributionStats['active'] ?? 0) ?></p>
        <p style="font-size: 12px; color: var(--gray-500); margin-top: 4px;">
            <?= $distributionStats['awaiting_driver'] ?? 0 ?> need driver
        </p>
    </div>
    <?php endif; ?>

    <?php $poAssigned = ($poStats['po_assigned'] ?? 0) + ($poStats['po_picked_up'] ?? 0); if ($poAssigned > 0): ?>
    <div class="stat-card" style="border-left: 3px solid #7c3aed;">
        <div class="stat-header">
            <div class="stat-icon" style="background: #f5f3ff; color: #7c3aed;">
                <i class="fa-solid fa-box-open"></i>
            </div>
        </div>
        <p class="stat-label">PO Pickups</p>
        <p class="stat-value" style="color: #7c3aed;"><?= number_format($poAssigned) ?></p>
        <p style="font-size: 12px; color: var(--gray-500); margin-top: 4px;">
            <?= $poStats['po_assigned'] ?? 0 ?> awaiting &bull; <?= $poStats['po_picked_up'] ?? 0 ?> picked up
        </p>
    </div>
    <?php endif; ?>
</div>

<!-- Quick Actions -->
<div class="quick-actions-grid">
    <a href="<?= url('/admin/delivery/staff') ?>" class="quick-action-card">
        <div class="quick-action-content">
            <div class="quick-action-icon staff">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="quick-action-text">
                <h3><?= $t['delivery_staff'] ?></h3>
                <p><?= $t['manage_drivers'] ?></p>
            </div>
        </div>
    </a>

    <a href="<?= url('/admin/delivery/active') ?>" class="quick-action-card">
        <div class="quick-action-content">
            <div class="quick-action-icon active">
                <i class="fa-solid fa-truck-loading"></i>
            </div>
            <div class="quick-action-text">
                <h3><?= $t['active_deliveries_link'] ?></h3>
                <p><?= ($stats['active'] ?? 0) ?> <?= $t['in_progress'] ?></p>
            </div>
        </div>
    </a>

    <a href="<?= url('/admin/delivery/analytics') ?>" class="quick-action-card">
        <div class="quick-action-content">
            <div class="quick-action-icon analytics">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="quick-action-text">
                <h3><?= $t['analytics'] ?></h3>
                <p><?= $t['view_reports'] ?></p>
            </div>
        </div>
    </a>

    <a href="<?= url('/admin/delivery/active?type=distribution') ?>" class="quick-action-card">
        <div class="quick-action-content">
            <div class="quick-action-icon" style="background: #e0e7ff; color: #4f46e5;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="quick-action-text">
                <h3>B2B Distribution</h3>
                <p><?= ($distributionStats['active'] ?? 0) ?> active deliveries</p>
            </div>
        </div>
    </a>

    <a href="<?= url('/admin/delivery/live-map') ?>" class="quick-action-card">
        <div class="quick-action-content">
            <div class="quick-action-icon" style="background: #dcfce7; color: #16a34a;">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div class="quick-action-text">
                <h3>Live Map</h3>
                <p>Real-time driver tracking</p>
            </div>
        </div>
    </a>

    <a href="<?= url('/admin/delivery/route-optimizer') ?>" class="quick-action-card">
        <div class="quick-action-content">
            <div class="quick-action-icon" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-route"></i>
            </div>
            <div class="quick-action-text">
                <h3>Route Optimizer</h3>
                <p>Plan optimized routes</p>
            </div>
        </div>
    </a>

    <a href="<?= url('/admin/delivery/vehicles') ?>" class="quick-action-card">
        <div class="quick-action-content">
            <div class="quick-action-icon" style="background: #f3e8ff; color: #7c3aed;">
                <i class="fa-solid fa-car"></i>
            </div>
            <div class="quick-action-text">
                <h3>Fleet Management</h3>
                <p>Manage vehicles</p>
            </div>
        </div>
    </a>
</div>

<!-- Active Drivers & Recent Deliveries -->
<div class="content-grid">
    <!-- Active Drivers -->
    <div class="content-card">
        <div class="card-header">
            <h2><?= $t['active_drivers'] ?></h2>
        </div>
        <div class="card-body">
            <?php if (!empty($activeDrivers)): ?>
                <div class="drivers-list">
                    <?php foreach ($activeDrivers as $driver): ?>
                    <div class="driver-item">
                        <div class="driver-info">
                            <h4><?= htmlspecialchars(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? '')) ?></h4>
                            <p><?= htmlspecialchars($driver['zone_name'] ?? $t['no_zone']) ?> • 
                               <?= $driver['active_deliveries'] ?? 0 ?> active</p>
                        </div>
                        <?php
                        $status = $driver['status'] ?? 'offline';
                        $statusClass = $status === 'available' ? 'status-available' : 'status-busy';
                        ?>
                        <span class="status-badge <?= $statusClass ?>">
                            <?= ucfirst($status) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p><?= $t['no_active_drivers'] ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Deliveries -->
    <div class="content-card">
        <div class="card-header">
            <h2><?= $t['recent_deliveries'] ?></h2>
        </div>
        <div class="card-body">
            <?php if (!empty($recentDeliveries)): ?>
                <div class="deliveries-list">
                    <?php foreach (array_slice($recentDeliveries, 0, 8) as $delivery): ?>
                    <div class="delivery-item">
                        <div class="delivery-info">
                            <h4>
                                <?php if (($delivery['source_type'] ?? $delivery['delivery_type'] ?? 'order') === 'distribution'): ?>
                                    <span style="display: inline-block; padding: 2px 6px; border-radius: 4px; background: #e0e7ff; color: #4f46e5; font-size: 10px; font-weight: 600; margin-right: 4px;">B2B</span>
                                <?php else: ?>
                                    <span style="display: inline-block; padding: 2px 6px; border-radius: 4px; background: #dbeafe; color: #1d4ed8; font-size: 10px; font-weight: 600; margin-right: 4px;">B2C</span>
                                <?php endif; ?>
                                #<?= htmlspecialchars($delivery['reference_number'] ?? $delivery['order_number'] ?? '') ?>
                            </h4>
                            <p><?= htmlspecialchars($delivery['driver_name'] ?? '') ?> &bull; <?= htmlspecialchars($delivery['shop_name'] ?? '') ?></p>
                        </div>
                        <span class="time-stamp">
                            <?= date('g:i A', strtotime($delivery['created_at'] ?? 'now')) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p><?= $t['no_recent_deliveries'] ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
