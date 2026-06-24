<?php
/**
 * OCS Admin Active Deliveries
 * File: app/Views/admin/delivery/active.php
 */

$pageTitle = 'Active Deliveries';
$currentPage = 'delivery';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'active_deliveries' => 'Active Deliveries',
        'monitor_realtime' => 'Monitor ongoing deliveries in real-time',
        'back' => 'Back',
        'all_active' => 'All Active',
        'pending' => 'Pending',
        'assigned' => 'Assigned',
        'accepted' => 'Accepted',
        'picked_up' => 'Picked Up',
        'on_the_way' => 'On The Way',
        'driver' => 'Driver',
        'customer' => 'Customer',
        'order_total' => 'Order Total',
        'delivery_fee' => 'Delivery',
        'no_active_deliveries' => 'No active deliveries',
        'no_deliveries_message' => 'All deliveries are completed or no orders yet',
        'assigned_at' => 'Assigned',
        'accepted_at' => 'Accepted',
        'picked_up_at' => 'Picked',
        'view_details' => 'View Details',
        'n_a' => 'N/A'
    ],
    'fr' => [
        'active_deliveries' => 'Livraisons Actives',
        'monitor_realtime' => 'Surveiller les livraisons en cours en temps réel',
        'back' => 'Retour',
        'all_active' => 'Toutes Actives',
        'pending' => 'En Attente',
        'assigned' => 'Assignées',
        'accepted' => 'Acceptées',
        'picked_up' => 'Récupérées',
        'on_the_way' => 'En Route',
        'driver' => 'Livreur',
        'customer' => 'Client',
        'order_total' => 'Total Commande',
        'delivery_fee' => 'Livraison',
        'no_active_deliveries' => 'Aucune livraison active',
        'no_deliveries_message' => 'Toutes les livraisons sont terminées ou aucune commande',
        'assigned_at' => 'Assignée',
        'accepted_at' => 'Acceptée',
        'picked_up_at' => 'Récupérée',
        'view_details' => 'Voir Détails',
        'n_a' => 'N/D'
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

/* Status Filter */
.status-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 24px;
}

.status-btn {
    padding: 10px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all var(--transition-base);
    border: none;
    cursor: pointer;
}

.status-btn.all { background: var(--gray-200); color: var(--gray-700); }
.status-btn.all.active,
.status-btn.all:hover { background: var(--primary); color: white; }

.status-btn.pending { background: var(--gray-200); color: var(--gray-700); }
.status-btn.pending.active,
.status-btn.pending:hover { background: #ef4444; color: white; }

.status-btn.assigned { background: var(--gray-200); color: var(--gray-700); }
.status-btn.assigned.active,
.status-btn.assigned:hover { background: #3b82f6; color: white; }

.status-btn.accepted { background: var(--gray-200); color: var(--gray-700); }
.status-btn.accepted.active,
.status-btn.accepted:hover { background: #f59e0b; color: white; }

.status-btn.picked_up { background: var(--gray-200); color: var(--gray-700); }
.status-btn.picked_up.active,
.status-btn.picked_up:hover { background: #8b5cf6; color: white; }

.status-btn.on_the_way { background: var(--gray-200); color: var(--gray-700); }
.status-btn.on_the_way.active,
.status-btn.on_the_way:hover { background: #f97316; color: white; }

/* Delivery Card */
.delivery-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 16px;
    transition: all var(--transition-base);
}

.delivery-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.delivery-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    gap: 16px;
}

.delivery-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #ffedd5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #f97316;
    flex-shrink: 0;
}

.delivery-title h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 4px;
}

.delivery-title p {
    font-size: 14px;
    color: var(--gray-600);
}

.delivery-title i {
    margin-right: 4px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: var(--radius-full);
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    white-space: nowrap;
}

.status-pending { background: #fee2e2; color: #991b1b; }
.status-assigned { background: #dbeafe; color: #1e40af; }
.status-accepted { background: #fef3c7; color: #92400e; }
.status-picked_up { background: #f3e8ff; color: #6b21a8; }
.status-on_the_way { background: #ffedd5; color: #c2410c; }

.delivery-title-section {
    display: flex;
    align-items: center;
    gap: 16px;
}

.delivery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

.delivery-section {
    padding: 16px;
    background: var(--gray-50);
    border-radius: var(--radius-md);
}

.delivery-section h4 {
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-500);
    text-transform: uppercase;
    margin-bottom: 12px;
    letter-spacing: 0.05em;
}

.driver-info,
.customer-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.driver-avatar,
.customer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    flex-shrink: 0;
}

.driver-avatar { background: var(--primary); }
.customer-avatar { background: #3b82f6; }

.driver-details h5,
.customer-details h5 {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
    font-size: 14px;
}

.driver-details p,
.customer-details p {
    font-size: 13px;
    color: var(--gray-600);
}

.driver-details i,
.customer-details i {
    margin-right: 4px;
}

.order-total {
    font-size: 28px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 4px;
}

.order-details {
    font-size: 13px;
    color: var(--gray-600);
}

.timeline {
    padding-top: 24px;
    border-top: 1px solid var(--border);
    margin-top: 24px;
}

.timeline-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    font-size: 13px;
}

.timeline-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--gray-600);
}

.timeline-item i.check { color: #22c55e; }
.timeline-item i.box { color: #8b5cf6; }

.card-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 16px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 48px 24px;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
}

.empty-state i {
    font-size: 48px;
    color: var(--gray-300);
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 20px;
    color: var(--gray-500);
    margin-bottom: 8px;
}

.empty-state p {
    color: var(--gray-400);
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .delivery-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .delivery-grid {
        grid-template-columns: 1fr;
    }
    
    .timeline-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .status-filter {
        gap: 8px;
    }
    
    .status-btn {
        padding: 8px 16px;
        font-size: 13px;
    }
}
</style>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-truck-loading text-orange-600 mr-2"></i>
            <?= $t['active_deliveries'] ?>
        </h1>
        <p class="page-subtitle"><?= $t['monitor_realtime'] ?></p>
    </div>
    <a href="<?= url('/admin/delivery') ?>" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left mr-2"></i> <?= $t['back'] ?>
    </a>
</div>

<!-- Type Filter -->
<?php $currentType = $selectedType ?? 'all'; ?>
<div class="card" style="padding: 16px 24px;">
    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 12px;">
        <span style="font-size: 13px; font-weight: 600; color: var(--gray-500); margin-right: 8px;">Source:</span>
        <a href="<?= url('/admin/delivery/active?type=all&status=' . ($selectedStatus ?? 'all')) ?>"
           style="padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none; <?= $currentType === 'all' ? 'background: var(--primary); color: white;' : 'background: var(--gray-200); color: var(--gray-700);' ?>">
            All
        </a>
        <a href="<?= url('/admin/delivery/active?type=orders&status=' . ($selectedStatus ?? 'all')) ?>"
           style="padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none; <?= $currentType === 'orders' ? 'background: #1d4ed8; color: white;' : 'background: var(--gray-200); color: var(--gray-700);' ?>">
            B2C Orders
        </a>
        <a href="<?= url('/admin/delivery/active?type=distribution&status=' . ($selectedStatus ?? 'all')) ?>"
           style="padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none; <?= $currentType === 'distribution' ? 'background: #4f46e5; color: white;' : 'background: var(--gray-200); color: var(--gray-700);' ?>">
            B2B Distribution
        </a>
    </div>
    <div class="status-filter">
        <a href="<?= url('/admin/delivery/active?status=all&type=' . $currentType) ?>"
           class="status-btn all <?= ($selectedStatus ?? '') === 'all' ? 'active' : '' ?>">
            <?= $t['all_active'] ?>
        </a>
        <a href="<?= url('/admin/delivery/active?status=pending&type=' . $currentType) ?>"
           class="status-btn pending <?= ($selectedStatus ?? '') === 'pending' ? 'active' : '' ?>">
            <?= $t['pending'] ?>
        </a>
        <a href="<?= url('/admin/delivery/active?status=assigned&type=' . $currentType) ?>"
           class="status-btn assigned <?= ($selectedStatus ?? '') === 'assigned' ? 'active' : '' ?>">
            <?= $t['assigned'] ?>
        </a>
        <a href="<?= url('/admin/delivery/active?status=accepted&type=' . $currentType) ?>"
           class="status-btn accepted <?= ($selectedStatus ?? '') === 'accepted' ? 'active' : '' ?>">
            <?= $t['accepted'] ?>
        </a>
        <a href="<?= url('/admin/delivery/active?status=picked_up&type=' . $currentType) ?>"
           class="status-btn picked_up <?= ($selectedStatus ?? '') === 'picked_up' ? 'active' : '' ?>">
            <?= $t['picked_up'] ?>
        </a>
        <a href="<?= url('/admin/delivery/active?status=on_the_way&type=' . $currentType) ?>"
           class="status-btn on_the_way <?= ($selectedStatus ?? '') === 'on_the_way' ? 'active' : '' ?>">
            <?= $t['on_the_way'] ?>
        </a>
    </div>
</div>

<!-- Deliveries List -->
<div class="deliveries-container">
    <?php if (!empty($deliveries)): ?>
        <?php foreach ($deliveries as $delivery):
            $isDistribution = ($delivery['source_type'] ?? $delivery['delivery_type'] ?? 'order') === 'distribution';
            $refNumber = $delivery['reference_number'] ?? $delivery['order_number'] ?? '';
        ?>
        <div class="delivery-card" style="<?= $isDistribution ? 'border-left: 4px solid #6366f1;' : '' ?>">
            <div class="delivery-header">
                <div class="delivery-title-section">
                    <div class="delivery-icon" style="<?= $isDistribution ? 'background: #e0e7ff; color: #4f46e5;' : '' ?>">
                        <i class="fa-solid <?= $isDistribution ? 'fa-boxes-stacked' : 'fa-box' ?>"></i>
                    </div>
                    <div class="delivery-title">
                        <h3>
                            <?php if ($isDistribution): ?>
                                <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; background: #e0e7ff; color: #4f46e5; font-size: 11px; font-weight: 600; margin-right: 6px; vertical-align: middle;">B2B</span>
                            <?php else: ?>
                                <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; background: #dbeafe; color: #1d4ed8; font-size: 11px; font-weight: 600; margin-right: 6px; vertical-align: middle;">B2C</span>
                            <?php endif; ?>
                            <?= $isDistribution ? 'Request' : 'Order' ?> #<?= htmlspecialchars($refNumber) ?>
                        </h3>
                        <p>
                            <i class="fa-solid <?= $isDistribution ? 'fa-building' : 'fa-store' ?>"></i>
                            <?= htmlspecialchars($delivery['shop_name'] ?? '') ?>
                        </p>
                    </div>
                </div>

                <?php
                $status = $delivery['status'] ?? '';
                $statusClasses = [
                    'pending' => 'status-pending',
                    'assigned' => 'status-assigned',
                    'accepted' => 'status-accepted',
                    'picked_up' => 'status-picked_up',
                    'on_the_way' => 'status-on_the_way'
                ];
                $statusClass = $statusClasses[$status] ?? 'status-pending';
                ?>
                <span class="status-badge <?= $statusClass ?>">
                    <?= ucfirst(str_replace('_', ' ', $status)) ?>
                </span>
            </div>

            <div class="delivery-grid">
                <!-- Driver Info -->
                <div class="delivery-section">
                    <h4><?= $t['driver'] ?></h4>
                    <div class="driver-info">
                        <div class="driver-avatar">
                            <?= strtoupper(substr($delivery['driver_first_name'] ?? 'U', 0, 1) . substr($delivery['driver_last_name'] ?? '', 0, 1)) ?>
                        </div>
                        <div class="driver-details">
                            <h5>
                                <?= htmlspecialchars(($delivery['driver_first_name'] ?? 'Unassigned') . ' ' . ($delivery['driver_last_name'] ?? '')) ?>
                            </h5>
                            <p>
                                <i class="fa-solid fa-phone"></i>
                                <?= htmlspecialchars($delivery['driver_phone'] ?? $t['n_a']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="delivery-section">
                    <h4><?= $t['customer'] ?></h4>
                    <div class="customer-info">
                        <div class="customer-avatar" style="<?= $isDistribution ? 'background: #6366f1;' : '' ?>">
                            <?= strtoupper(substr($delivery['customer_first_name'] ?? '', 0, 1) . substr($delivery['customer_last_name'] ?? '', 0, 1)) ?>
                        </div>
                        <div class="customer-details">
                            <h5>
                                <?= htmlspecialchars(($delivery['customer_first_name'] ?? '') . ' ' . ($delivery['customer_last_name'] ?? '')) ?>
                            </h5>
                            <p>
                                <i class="fa-solid fa-phone"></i>
                                <?= htmlspecialchars($delivery['customer_phone'] ?? $t['n_a']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="delivery-section">
                    <h4><?= $t['order_total'] ?></h4>
                    <div class="order-info">
                        <div class="order-total">
                            <?= currency($delivery['total'] ?? 0) ?>
                        </div>
                        <?php if (!$isDistribution): ?>
                        <div class="order-details">
                            <?= $t['delivery_fee'] ?>: <?= currency($delivery['delivery_fee'] ?? 0) ?>
                        </div>
                        <?php else: ?>
                        <div class="order-details">
                            <?= htmlspecialchars($delivery['delivery_address'] ?? '') ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <?php if (!$isDistribution): ?>
            <div class="timeline">
                <div class="timeline-content">
                    <div class="timeline-item">
                        <i class="fa-solid fa-clock text-gray-400"></i>
                        <span>
                            <?= $t['assigned_at'] ?>: <?= date('M d, g:i A', strtotime($delivery['assigned_at'] ?? $delivery['created_at'] ?? 'now')) ?>
                        </span>
                    </div>
                    <?php if (!empty($delivery['accepted_at'])): ?>
                    <div class="timeline-item">
                        <i class="fa-solid fa-check check"></i>
                        <span>
                            <?= $t['accepted_at'] ?>: <?= date('g:i A', strtotime($delivery['accepted_at'])) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($delivery['picked_up_at'])): ?>
                    <div class="timeline-item">
                        <i class="fa-solid fa-box box"></i>
                        <span>
                            <?= $t['picked_up_at'] ?>: <?= date('g:i A', strtotime($delivery['picked_up_at'])) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="card-actions">
                <?php if ($isDistribution): ?>
                    <a href="<?= url('/admin/distribution/view?id=' . ($delivery['distribution_request_id'] ?? '')) ?>"
                       class="btn btn-primary">
                        <i class="fa-solid fa-eye mr-1"></i> <?= $t['view_details'] ?>
                    </a>
                <?php else: ?>
                    <a href="<?= url('/admin/delivery/details?id=' . ($delivery['id'] ?? '')) ?>"
                       class="btn btn-primary">
                        <i class="fa-solid fa-eye mr-1"></i> <?= $t['view_details'] ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fa-solid fa-truck"></i>
            <h3><?= $t['no_active_deliveries'] ?></h3>
            <p><?= $t['no_deliveries_message'] ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- PO Pickups Section -->
<?php if (!empty($poPickups)): ?>
<div style="margin-top: 40px;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
        <span style="display: inline-flex; align-items: center; gap: 6px; background: #7c3aed; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
            <i class="fa-solid fa-box-open" style="font-size: 10px;"></i> PO PICKUPS
        </span>
        <h2 style="font-size: 20px; font-weight: 700; color: var(--dark); margin: 0;">Supplier Pickups — Assigned to Driver</h2>
    </div>
    <?php foreach ($poPickups as $po):
        $poStatus = $po['status'];
        $isPickedUp = $poStatus === 'picked_up';
    ?>
    <div class="delivery-card" style="border-left: 4px solid <?= $isPickedUp ? '#22c55e' : '#7c3aed' ?>;">
        <div class="delivery-header">
            <div class="delivery-title-section">
                <div class="delivery-icon" style="background: <?= $isPickedUp ? '#dcfce7' : '#f5f3ff' ?>; color: <?= $isPickedUp ? '#16a34a' : '#7c3aed' ?>;">
                    <i class="fa-solid <?= $isPickedUp ? 'fa-check-circle' : 'fa-inventory' ?>"></i>
                </div>
                <div class="delivery-title">
                    <h3>
                        <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; background: #f5f3ff; color: #7c3aed; font-size: 11px; font-weight: 600; margin-right: 6px; vertical-align: middle;">PO</span>
                        PO #<?= htmlspecialchars($po['po_number']) ?>
                    </h3>
                    <p><i class="fa-solid fa-warehouse"></i> <?= htmlspecialchars($po['supplier_name']) ?></p>
                </div>
            </div>
            <span class="status-badge" style="background: <?= $isPickedUp ? '#dcfce7' : '#f5f3ff' ?>; color: <?= $isPickedUp ? '#166534' : '#5b21b6' ?>;">
                <?= $isPickedUp ? 'Picked Up' : 'Ready for Pickup' ?>
            </span>
        </div>

        <div class="delivery-grid">
            <div class="delivery-section">
                <h4>Driver</h4>
                <div class="driver-info">
                    <div class="driver-avatar" style="background: #7c3aed;">
                        <?= strtoupper(substr($po['driver_name'], 0, 1)) ?>
                    </div>
                    <div class="driver-details">
                        <h5><?= htmlspecialchars($po['driver_name']) ?></h5>
                        <p><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($po['driver_phone'] ?? 'N/A') ?></p>
                    </div>
                </div>
            </div>
            <div class="delivery-section">
                <h4>Supplier</h4>
                <div style="font-size: 14px; color: var(--dark); font-weight: 600;"><?= htmlspecialchars($po['supplier_name']) ?></div>
                <div style="font-size: 13px; color: var(--gray-600); margin-top: 4px;"><?= htmlspecialchars($po['supplier_address']) ?></div>
            </div>
            <div class="delivery-section">
                <h4>Amount</h4>
                <div class="order-total">$<?= number_format($po['total_amount'], 2) ?></div>
                <?php if (!empty($po['driver_assigned_at'])): ?>
                <div class="order-details">Assigned: <?= date('M d, g:i A', strtotime($po['driver_assigned_at'])) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-actions">
            <a href="<?= url('/admin/purchase-orders/view?id=' . $po['id']) ?>" class="btn btn-primary" style="background: #7c3aed;">
                <i class="fa-solid fa-eye mr-1"></i> View PO
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ODA Live Board -->
<div style="margin-top: 40px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="display: inline-flex; align-items: center; gap: 6px; background: #0ea5e9; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                <span style="width: 7px; height: 7px; background: white; border-radius: 50%; animation: odaPulse 1.4s infinite;"></span> LIVE
            </span>
            <h2 style="font-size: 20px; font-weight: 700; color: var(--dark); margin: 0;">ODA App — Active Orders</h2>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <span id="oda-last-refresh" style="font-size: 13px; color: var(--gray-500);"></span>
            <span id="oda-refresh-countdown" style="font-size: 13px; color: var(--gray-400);"></span>
        </div>
    </div>
    <div id="oda-board">
        <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; color: var(--gray-400);">
            <i class="fa-solid fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 12px; display: block;"></i>
            Loading ODA orders...
        </div>
    </div>
</div>

<style>
@keyframes odaPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.4; transform: scale(0.7); }
}

.oda-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    padding: 20px 24px;
    margin-bottom: 12px;
    border-left: 4px solid #0ea5e9;
    transition: box-shadow 0.2s;
}
.oda-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.12); }

.oda-step-bar {
    display: flex;
    gap: 3px;
    margin: 14px 0 8px;
}
.oda-step {
    flex: 1;
    height: 5px;
    border-radius: 3px;
    background: #e5e7eb;
    transition: background 0.4s;
}
.oda-step.done  { background: #0ea5e9; }
.oda-step.active { background: #f59e0b; animation: odaStepPulse 1.2s ease-in-out infinite; }

@keyframes odaStepPulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.55; }
}

.oda-status-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #0369a1;
    background: #e0f2fe;
    padding: 3px 10px;
    border-radius: 12px;
}

.oda-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
    margin-top: 14px;
    font-size: 13px;
    color: var(--gray-600);
}
.oda-info-grid strong { color: var(--dark); display: block; font-size: 14px; margin-bottom: 2px; }
</style>

<script>
(function() {
    const STEPS = [
        { key: 'accepted',            label: 'Accepted' },
        { key: 'heading_to_merchant', label: 'To Merchant' },
        { key: 'arrived_merchant',    label: 'At Merchant' },
        { key: 'picked_up',           label: 'Picked Up' },
        { key: 'en_route',            label: 'En Route' },
        { key: 'arrived_customer',    label: 'At Customer' },
    ];

    const STEP_ICONS = {
        accepted: '✅', heading_to_merchant: '🚗', arrived_merchant: '🏪',
        picked_up: '📦', en_route: '🚀', arrived_customer: '🏠', delivered: '✅'
    };

    let countdown = 15;
    let timer;

    function timeAgo(dateStr) {
        if (!dateStr) return '—';
        const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
        if (diff < 60) return diff + 's ago';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        return Math.floor(diff / 3600) + 'h ago';
    }

    function renderOrders(orders) {
        const board = document.getElementById('oda-board');
        if (!orders.length) {
            board.innerHTML = '<div style="text-align:center;padding:40px;background:white;border-radius:12px;color:var(--gray-400);"><i class="fa-solid fa-mobile-screen-button" style="font-size:32px;margin-bottom:12px;display:block;"></i><p style="margin:0;font-size:15px;">No active ODA orders right now</p><p style="margin:4px 0 0;font-size:13px;">Orders appear here when drivers accept them in the ODA app</p></div>';
            return;
        }

        board.innerHTML = orders.map(o => {
            const stepIndex = o.step_index;
            const stepBar = STEPS.map((s, i) => {
                const cls = i < stepIndex ? 'done' : (i === stepIndex ? 'active' : '');
                return `<div class="oda-step ${cls}" title="${s.label}"></div>`;
            }).join('');

            const statusLabel = o.driver_status.replace(/_/g, ' ');
            const icon = STEP_ICONS[o.driver_status] || '📍';

            return `
            <div class="oda-card">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div>
                        <span style="font-size:16px;font-weight:700;color:var(--dark);">${icon} Order #${o.order_number}</span>
                        <span style="margin-left:10px;font-size:13px;color:var(--gray-500);">${o.shop_name}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                        <span class="oda-status-label">${statusLabel}</span>
                        <span style="font-size:12px;color:var(--gray-400);">${timeAgo(o.last_update)}</span>
                    </div>
                </div>

                <div class="oda-step-bar">${stepBar}</div>
                <div style="display:flex;gap:6px;font-size:11px;color:var(--gray-400);">
                    ${STEPS.map((s, i) => `<span style="flex:1;text-align:center;${i === stepIndex ? 'color:#0369a1;font-weight:700;' : ''}">${s.label}</span>`).join('')}
                </div>

                <div class="oda-info-grid" style="margin-top:16px;">
                    <div>
                        <strong>🧑‍✈️ ${o.driver.name}</strong>
                        <a href="tel:${o.driver.phone}" style="color:var(--gray-500);text-decoration:none;">${o.driver.phone || '—'}</a>
                        ${o.driver.lat ? `<br><span style="font-size:11px;color:#22c55e;">📍 GPS: ${timeAgo(o.driver.last_gps)}</span>` : '<br><span style="font-size:11px;color:#f59e0b;">📍 No GPS yet</span>'}
                    </div>
                    <div>
                        <strong>👤 ${o.customer.name}</strong>
                        <span>${o.customer.phone || '—'}</span><br>
                        <span style="font-size:12px;">${o.customer.address || '—'}</span>
                    </div>
                    <div>
                        <strong>📦 ${o.shop_name}</strong>
                        <span style="font-size:12px;">${o.shop_address || '—'}</span>
                    </div>
                    <div style="text-align:right;">
                        <strong style="font-size:18px;color:var(--primary);">$${parseFloat(o.total).toFixed(2)}</strong>
                        <span style="display:block;font-size:12px;">Driver: $${parseFloat(o.driver_payout).toFixed(2)}</span>
                        ${o.accepted_at ? `<span style="display:block;font-size:11px;color:var(--gray-400);">Accepted: ${timeAgo(o.accepted_at)}</span>` : ''}
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    function refresh() {
        fetch('/admin/api/delivery/oda-live')
            .then(r => r.json())
            .then(data => {
                renderOrders(data.orders || []);
                document.getElementById('oda-last-refresh').textContent = 'Updated ' + new Date().toLocaleTimeString();
            })
            .catch(() => {
                document.getElementById('oda-last-refresh').textContent = 'Update failed';
            });
    }

    function startCountdown() {
        clearInterval(timer);
        countdown = 15;
        timer = setInterval(() => {
            countdown--;
            document.getElementById('oda-refresh-countdown').textContent = `Refreshing in ${countdown}s`;
            if (countdown <= 0) {
                refresh();
                countdown = 15;
            }
        }, 1000);
    }

    // Initial load
    refresh();
    startCountdown();
})();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
