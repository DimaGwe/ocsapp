<?php
/**
 * OCS Admin Delivery Details
 * File: app/Views/admin/delivery/details.php
 */

$pageTitle = 'Delivery Details';
$currentPage = 'delivery';

// Get current language
$currentLang = <?php
/**
 * OCS Admin Delivery Details
 * File: app/Views/admin/delivery/details.php
 */

$pageTitle = 'Delivery Details';
$currentPage = 'delivery';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'delivery_details' => 'Delivery Details',
        'back' => 'Back to Active',
        'order_info' => 'Order Information',
        'order_number' => 'Order Number',
        'order_date' => 'Order Date',
        'order_status' => 'Order Status',
        'order_notes' => 'Order Notes',
        'customer_info' => 'Customer Information',
        'customer_name' => 'Customer Name',
        'customer_email' => 'Email',
        'customer_phone' => 'Phone',
        'delivery_address' => 'Delivery Address',
        'driver_info' => 'Driver Information',
        'driver_name' => 'Driver Name',
        'driver_phone' => 'Phone',
        'unassigned' => 'Unassigned',
        'assign_driver' => 'Assign Driver',
        'reassign_driver' => 'Reassign Driver',
        'shop_info' => 'Shop Information',
        'shop_name' => 'Shop Name',
        'shop_address' => 'Shop Address',
        'shop_phone' => 'Phone',
        'delivery_status' => 'Delivery Status',
        'delivery_fee' => 'Delivery Fee',
        'distance' => 'Distance',
        'created_at' => 'Created',
        'assigned_at' => 'Assigned',
        'accepted_at' => 'Accepted',
        'picked_up_at' => 'Picked Up',
        'delivered_at' => 'Delivered',
        'order_items' => 'Order Items',
        'product' => 'Product',
        'quantity' => 'Qty',
        'price' => 'Price',
        'subtotal' => 'Subtotal',
        'status_history' => 'Status History',
        'status' => 'Status',
        'notes' => 'Notes',
        'updated_by' => 'Updated By',
        'date' => 'Date',
        'no_history' => 'No status history available',
        'order_summary' => 'Order Summary',
        'tax' => 'Tax',
        'total' => 'Total',
        'update_status' => 'Update Status',
        'select_driver' => 'Select Driver',
        'available' => 'Available',
        'busy' => 'Busy',
        'offline' => 'Offline',
        'pending' => 'Pending',
        'assigned' => 'Assigned',
        'accepted' => 'Accepted',
        'picked_up' => 'Picked Up',
        'on_the_way' => 'On The Way',
        'delivered' => 'Delivered',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
        'n_a' => 'N/A',
        'km' => 'km'
    ],
    'fr' => [
        'delivery_details' => 'Détails de Livraison',
        'back' => 'Retour aux Actifs',
        'order_info' => 'Informations de Commande',
        'order_number' => 'Numéro de Commande',
        'order_date' => 'Date de Commande',
        'order_status' => 'Statut de Commande',
        'order_notes' => 'Notes de Commande',
        'customer_info' => 'Informations Client',
        'customer_name' => 'Nom du Client',
        'customer_email' => 'Courriel',
        'customer_phone' => 'Téléphone',
        'delivery_address' => 'Adresse de Livraison',
        'driver_info' => 'Informations du Livreur',
        'driver_name' => 'Nom du Livreur',
        'driver_phone' => 'Téléphone',
        'unassigned' => 'Non Assigné',
        'assign_driver' => 'Assigner Livreur',
        'reassign_driver' => 'Réassigner Livreur',
        'shop_info' => 'Informations de la Boutique',
        'shop_name' => 'Nom de la Boutique',
        'shop_address' => 'Adresse de la Boutique',
        'shop_phone' => 'Téléphone',
        'delivery_status' => 'Statut de Livraison',
        'delivery_fee' => 'Frais de Livraison',
        'distance' => 'Distance',
        'created_at' => 'Créé',
        'assigned_at' => 'Assigné',
        'accepted_at' => 'Accepté',
        'picked_up_at' => 'Récupéré',
        'delivered_at' => 'Livré',
        'order_items' => 'Articles de la Commande',
        'product' => 'Produit',
        'quantity' => 'Qté',
        'price' => 'Prix',
        'subtotal' => 'Sous-total',
        'status_history' => 'Historique des Statuts',
        'status' => 'Statut',
        'notes' => 'Notes',
        'updated_by' => 'Mis à jour par',
        'date' => 'Date',
        'no_history' => 'Aucun historique disponible',
        'order_summary' => 'Résumé de la Commande',
        'tax' => 'Taxe',
        'total' => 'Total',
        'update_status' => 'Mettre à Jour le Statut',
        'select_driver' => 'Sélectionner Livreur',
        'available' => 'Disponible',
        'busy' => 'Occupé',
        'offline' => 'Hors Ligne',
        'pending' => 'En Attente',
        'assigned' => 'Assigné',
        'accepted' => 'Accepté',
        'picked_up' => 'Récupéré',
        'on_the_way' => 'En Route',
        'delivered' => 'Livré',
        'failed' => 'Échoué',
        'cancelled' => 'Annulé',
        'n_a' => 'N/D',
        'km' => 'km'
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

.btn-sm {
    padding: 8px 16px;
    font-size: 13px;
}

/* Cards */
.card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}

.card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
}

/* Grid Layout */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

/* Info Rows */
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--gray-100);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 14px;
    color: var(--gray-500);
    font-weight: 500;
}

.info-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    text-align: right;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fee2e2; color: #991b1b; }
.status-assigned { background: #dbeafe; color: #1e40af; }
.status-accepted { background: #fef3c7; color: #92400e; }
.status-picked_up { background: #f3e8ff; color: #6b21a8; }
.status-on_the_way { background: #ffedd5; color: #c2410c; }
.status-delivered { background: #dcfce7; color: #166534; }
.status-failed { background: #fee2e2; color: #991b1b; }
.status-cancelled { background: #f3f4f6; color: #374151; }

/* Order Items Table */
.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table thead {
    background: var(--gray-50);
}

.items-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--gray-500);
}

.items-table td {
    padding: 16px;
    border-bottom: 1px solid var(--border);
}

.items-table tbody tr:hover {
    background: var(--gray-50);
}

.product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-image {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    object-fit: cover;
    background: var(--gray-100);
}

.product-name {
    font-weight: 600;
    color: var(--dark);
}

/* Order Summary */
.order-summary {
    background: var(--gray-50);
    border-radius: var(--radius-md);
    padding: 20px;
    margin-top: 16px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
}

.summary-row.total {
    border-top: 2px solid var(--border);
    margin-top: 8px;
    padding-top: 16px;
    font-weight: 700;
    font-size: 18px;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 24px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: var(--gray-200);
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -20px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--primary);
    border: 2px solid white;
    box-shadow: var(--shadow-sm);
}

.timeline-dot.pending { background: #ef4444; }
.timeline-dot.assigned { background: #3b82f6; }
.timeline-dot.accepted { background: #f59e0b; }
.timeline-dot.picked_up { background: #8b5cf6; }
.timeline-dot.on_the_way { background: #f97316; }
.timeline-dot.delivered { background: #22c55e; }
.timeline-dot.failed { background: #ef4444; }
.timeline-dot.cancelled { background: #6b7280; }

.timeline-content {
    background: var(--gray-50);
    border-radius: var(--radius-md);
    padding: 12px 16px;
}

.timeline-status {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
}

.timeline-meta {
    font-size: 13px;
    color: var(--gray-500);
}

/* Driver Select */
.driver-select-wrapper {
    margin-top: 16px;
}

.driver-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    margin-bottom: 12px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 32px;
    color: var(--gray-500);
}

/* Responsive */
@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .items-table {
        font-size: 13px;
    }

    .items-table th,
    .items-table td {
        padding: 10px 8px;
    }
}
</style>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-truck text-primary mr-2"></i>
            <?= $t['delivery_details'] ?> #<?= htmlspecialchars($delivery['order_number'] ?? '') ?>
        </h1>
        <p class="page-subtitle">
            <?php
            $status = $delivery['status'] ?? 'pending';
            $statusLabels = [
                'pending' => $t['pending'],
                'assigned' => $t['assigned'],
                'accepted' => $t['accepted'],
                'picked_up' => $t['picked_up'],
                'on_the_way' => $t['on_the_way'],
                'delivered' => $t['delivered'],
                'failed' => $t['failed'],
                'cancelled' => $t['cancelled']
            ];
            ?>
            <span class="status-badge status-<?= $status ?>">
                <?= $statusLabels[$status] ?? ucfirst($status) ?>
            </span>
        </p>
    </div>
    <a href="<?= url('/admin/delivery/active') ?>" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left mr-2"></i> <?= $t['back'] ?>
    </a>
</div>

<!-- Main Details Grid -->
<div class="details-grid">
    <!-- Order Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-receipt text-primary mr-2"></i>
                <?= $t['order_info'] ?>
            </h3>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['order_number'] ?></span>
            <span class="info-value">#<?= htmlspecialchars($delivery['order_number'] ?? '') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['order_date'] ?></span>
            <span class="info-value"><?= date('M d, Y g:i A', strtotime($delivery['order_created_at'] ?? 'now')) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['order_status'] ?></span>
            <span class="info-value"><?= ucfirst($delivery['order_status'] ?? '') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['delivery_fee'] ?></span>
            <span class="info-value"><?= currency($delivery['delivery_fee'] ?? 0) ?></span>
        </div>
        <?php if (!empty($delivery['distance_km'])): ?>
        <div class="info-row">
            <span class="info-label"><?= $t['distance'] ?></span>
            <span class="info-value"><?= number_format($delivery['distance_km'], 1) ?> <?= $t['km'] ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($delivery['order_notes'])): ?>
        <div class="info-row">
            <span class="info-label"><?= $t['order_notes'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['order_notes']) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Customer Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-user text-blue-500 mr-2"></i>
                <?= $t['customer_info'] ?>
            </h3>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['customer_name'] ?></span>
            <span class="info-value"><?= htmlspecialchars(($delivery['customer_first_name'] ?? '') . ' ' . ($delivery['customer_last_name'] ?? '')) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['customer_email'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['customer_email'] ?? $t['n_a']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['customer_phone'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['customer_phone'] ?? $t['n_a']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['delivery_address'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['delivery_address'] ?? $t['n_a']) ?></span>
        </div>
    </div>

    <!-- Driver Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-motorcycle text-purple-500 mr-2"></i>
                <?= $t['driver_info'] ?>
            </h3>
        </div>
        <?php if (!empty($delivery['driver_id'])): ?>
            <div class="info-row">
                <span class="info-label"><?= $t['driver_name'] ?></span>
                <span class="info-value"><?= htmlspecialchars($delivery['driver_first_name'] . ' ' . $delivery['driver_last_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><?= $t['driver_phone'] ?></span>
                <span class="info-value"><?= htmlspecialchars($delivery['driver_phone'] ?? $t['n_a']) ?></span>
            </div>
        <?php else: ?>
            <div class="info-row">
                <span class="info-label"><?= $t['status'] ?></span>
                <span class="info-value" style="color: #ef4444;"><?= $t['unassigned'] ?></span>
            </div>
        <?php endif; ?>

        <!-- Driver Assignment -->
        <?php if ($delivery['status'] !== 'delivered' && $delivery['status'] !== 'cancelled'): ?>
        <div class="driver-select-wrapper">
            <select id="driverSelect" class="driver-select">
                <option value=""><?= $t['select_driver'] ?></option>
                <?php foreach ($availableDrivers as $driver): ?>
                    <?php
                    $availStatus = $driver['availability_status'] ?? 'offline';
                    $statusLabel = $t[$availStatus] ?? $availStatus;
                    ?>
                    <option value="<?= $driver['id'] ?>" <?= ($driver['id'] == ($delivery['driver_id'] ?? 0)) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']) ?>
                        (<?= $statusLabel ?> - <?= $driver['active_deliveries'] ?? 0 ?> active)
                    </option>
                <?php endforeach; ?>
            </select>
            <button onclick="assignDriver()" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-user-check mr-1"></i>
                <?= empty($delivery['driver_id']) ? $t['assign_driver'] : $t['reassign_driver'] ?>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Shop Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-store text-orange-500 mr-2"></i>
                <?= $t['shop_info'] ?>
            </h3>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['shop_name'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['shop_name'] ?? '') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['shop_address'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['shop_address'] ?? $t['n_a']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['shop_phone'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['shop_phone'] ?? $t['n_a']) ?></span>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa-solid fa-shopping-basket text-primary mr-2"></i>
            <?= $t['order_items'] ?>
        </h3>
    </div>

    <?php if (!empty($orderItems)): ?>
    <table class="items-table">
        <thead>
            <tr>
                <th><?= $t['product'] ?></th>
                <th class="text-center"><?= $t['quantity'] ?></th>
                <th class="text-right"><?= $t['price'] ?></th>
                <th class="text-right"><?= $t['subtotal'] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orderItems as $item): ?>
            <tr>
                <td>
                    <div class="product-cell">
                        <?php if (!empty($item['product_image'])): ?>
                        <img src="<?= url('/uploads/products/' . $item['product_image']) ?>" alt="" class="product-image">
                        <?php else: ?>
                        <div class="product-image" style="display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-image text-gray-300"></i>
                        </div>
                        <?php endif; ?>
                        <span class="product-name"><?= htmlspecialchars($item['product_name'] ?? '') ?></span>
                    </div>
                </td>
                <td class="text-center"><?= $item['quantity'] ?? 1 ?></td>
                <td class="text-right"><?= currency($item['price'] ?? 0) ?></td>
                <td class="text-right"><?= currency(($item['price'] ?? 0) * ($item['quantity'] ?? 1)) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="order-summary">
        <div class="summary-row">
            <span><?= $t['subtotal'] ?></span>
            <span><?= currency($delivery['subtotal'] ?? 0) ?></span>
        </div>
        <div class="summary-row">
            <span><?= $t['tax'] ?></span>
            <span><?= currency($delivery['tax'] ?? 0) ?></span>
        </div>
        <div class="summary-row">
            <span><?= $t['delivery_fee'] ?></span>
            <span><?= currency($delivery['delivery_fee'] ?? 0) ?></span>
        </div>
        <div class="summary-row total">
            <span><?= $t['total'] ?></span>
            <span><?= currency($delivery['order_total'] ?? 0) ?></span>
        </div>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fa-solid fa-box-open" style="font-size: 32px; color: var(--gray-300); margin-bottom: 12px;"></i>
        <p>No items found</p>
    </div>
    <?php endif; ?>
</div>

<!-- Status History -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa-solid fa-history text-primary mr-2"></i>
            <?= $t['status_history'] ?>
        </h3>
    </div>

    <?php if (!empty($statusHistory)): ?>
    <div class="timeline">
        <?php foreach ($statusHistory as $history): ?>
        <div class="timeline-item">
            <div class="timeline-dot <?= $history['status'] ?? '' ?>"></div>
            <div class="timeline-content">
                <div class="timeline-status">
                    <?= ucfirst(str_replace('_', ' ', $history['status'] ?? '')) ?>
                </div>
                <div class="timeline-meta">
                    <?php if (!empty($history['notes'])): ?>
                        <?= htmlspecialchars($history['notes']) ?> &bull;
                    <?php endif; ?>
                    <?php if (!empty($history['first_name'])): ?>
                        <?= htmlspecialchars($history['first_name'] . ' ' . $history['last_name']) ?> &bull;
                    <?php endif; ?>
                    <?= date('M d, Y g:i A', strtotime($history['created_at'] ?? 'now')) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fa-solid fa-clock-rotate-left" style="font-size: 32px; color: var(--gray-300); margin-bottom: 12px;"></i>
        <p><?= $t['no_history'] ?></p>
    </div>
    <?php endif; ?>
</div>

<script>
async function assignDriver() {
    const driverId = document.getElementById('driverSelect').value;
    const deliveryId = <?= $delivery['id'] ?? 0 ?>;

    if (!driverId) {
        alert('Please select a driver');
        return;
    }

    try {
        const response = await fetch('<?= url('/admin/delivery/assign-driver') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                delivery_id: deliveryId,
                driver_id: driverId
            })
        });

        const data = await response.json();

        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        alert('Network error: ' + error.message);
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'delivery_details' => 'Delivery Details',
        'back' => 'Back to Active',
        'order_info' => 'Order Information',
        'order_number' => 'Order Number',
        'order_date' => 'Order Date',
        'order_status' => 'Order Status',
        'order_notes' => 'Order Notes',
        'customer_info' => 'Customer Information',
        'customer_name' => 'Customer Name',
        'customer_email' => 'Email',
        'customer_phone' => 'Phone',
        'delivery_address' => 'Delivery Address',
        'driver_info' => 'Driver Information',
        'driver_name' => 'Driver Name',
        'driver_phone' => 'Phone',
        'unassigned' => 'Unassigned',
        'assign_driver' => 'Assign Driver',
        'reassign_driver' => 'Reassign Driver',
        'shop_info' => 'Shop Information',
        'shop_name' => 'Shop Name',
        'shop_address' => 'Shop Address',
        'shop_phone' => 'Phone',
        'delivery_status' => 'Delivery Status',
        'delivery_fee' => 'Delivery Fee',
        'distance' => 'Distance',
        'created_at' => 'Created',
        'assigned_at' => 'Assigned',
        'accepted_at' => 'Accepted',
        'picked_up_at' => 'Picked Up',
        'delivered_at' => 'Delivered',
        'order_items' => 'Order Items',
        'product' => 'Product',
        'quantity' => 'Qty',
        'price' => 'Price',
        'subtotal' => 'Subtotal',
        'status_history' => 'Status History',
        'status' => 'Status',
        'notes' => 'Notes',
        'updated_by' => 'Updated By',
        'date' => 'Date',
        'no_history' => 'No status history available',
        'order_summary' => 'Order Summary',
        'tax' => 'Tax',
        'total' => 'Total',
        'update_status' => 'Update Status',
        'select_driver' => 'Select Driver',
        'available' => 'Available',
        'busy' => 'Busy',
        'offline' => 'Offline',
        'pending' => 'Pending',
        'assigned' => 'Assigned',
        'accepted' => 'Accepted',
        'picked_up' => 'Picked Up',
        'on_the_way' => 'On The Way',
        'delivered' => 'Delivered',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
        'n_a' => 'N/A',
        'km' => 'km'
    ],
    'fr' => [
        'delivery_details' => 'Détails de Livraison',
        'back' => 'Retour aux Actifs',
        'order_info' => 'Informations de Commande',
        'order_number' => 'Numéro de Commande',
        'order_date' => 'Date de Commande',
        'order_status' => 'Statut de Commande',
        'order_notes' => 'Notes de Commande',
        'customer_info' => 'Informations Client',
        'customer_name' => 'Nom du Client',
        'customer_email' => 'Courriel',
        'customer_phone' => 'Téléphone',
        'delivery_address' => 'Adresse de Livraison',
        'driver_info' => 'Informations du Livreur',
        'driver_name' => 'Nom du Livreur',
        'driver_phone' => 'Téléphone',
        'unassigned' => 'Non Assigné',
        'assign_driver' => 'Assigner Livreur',
        'reassign_driver' => 'Réassigner Livreur',
        'shop_info' => 'Informations de la Boutique',
        'shop_name' => 'Nom de la Boutique',
        'shop_address' => 'Adresse de la Boutique',
        'shop_phone' => 'Téléphone',
        'delivery_status' => 'Statut de Livraison',
        'delivery_fee' => 'Frais de Livraison',
        'distance' => 'Distance',
        'created_at' => 'Créé',
        'assigned_at' => 'Assigné',
        'accepted_at' => 'Accepté',
        'picked_up_at' => 'Récupéré',
        'delivered_at' => 'Livré',
        'order_items' => 'Articles de la Commande',
        'product' => 'Produit',
        'quantity' => 'Qté',
        'price' => 'Prix',
        'subtotal' => 'Sous-total',
        'status_history' => 'Historique des Statuts',
        'status' => 'Statut',
        'notes' => 'Notes',
        'updated_by' => 'Mis à jour par',
        'date' => 'Date',
        'no_history' => 'Aucun historique disponible',
        'order_summary' => 'Résumé de la Commande',
        'tax' => 'Taxe',
        'total' => 'Total',
        'update_status' => 'Mettre à Jour le Statut',
        'select_driver' => 'Sélectionner Livreur',
        'available' => 'Disponible',
        'busy' => 'Occupé',
        'offline' => 'Hors Ligne',
        'pending' => 'En Attente',
        'assigned' => 'Assigné',
        'accepted' => 'Accepté',
        'picked_up' => 'Récupéré',
        'on_the_way' => 'En Route',
        'delivered' => 'Livré',
        'failed' => 'Échoué',
        'cancelled' => 'Annulé',
        'n_a' => 'N/D',
        'km' => 'km'
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

.btn-sm {
    padding: 8px 16px;
    font-size: 13px;
}

/* Cards */
.card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}

.card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
}

/* Grid Layout */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

/* Info Rows */
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--gray-100);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 14px;
    color: var(--gray-500);
    font-weight: 500;
}

.info-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    text-align: right;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fee2e2; color: #991b1b; }
.status-assigned { background: #dbeafe; color: #1e40af; }
.status-accepted { background: #fef3c7; color: #92400e; }
.status-picked_up { background: #f3e8ff; color: #6b21a8; }
.status-on_the_way { background: #ffedd5; color: #c2410c; }
.status-delivered { background: #dcfce7; color: #166534; }
.status-failed { background: #fee2e2; color: #991b1b; }
.status-cancelled { background: #f3f4f6; color: #374151; }

/* Order Items Table */
.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table thead {
    background: var(--gray-50);
}

.items-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--gray-500);
}

.items-table td {
    padding: 16px;
    border-bottom: 1px solid var(--border);
}

.items-table tbody tr:hover {
    background: var(--gray-50);
}

.product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-image {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    object-fit: cover;
    background: var(--gray-100);
}

.product-name {
    font-weight: 600;
    color: var(--dark);
}

/* Order Summary */
.order-summary {
    background: var(--gray-50);
    border-radius: var(--radius-md);
    padding: 20px;
    margin-top: 16px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
}

.summary-row.total {
    border-top: 2px solid var(--border);
    margin-top: 8px;
    padding-top: 16px;
    font-weight: 700;
    font-size: 18px;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 24px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: var(--gray-200);
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -20px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--primary);
    border: 2px solid white;
    box-shadow: var(--shadow-sm);
}

.timeline-dot.pending { background: #ef4444; }
.timeline-dot.assigned { background: #3b82f6; }
.timeline-dot.accepted { background: #f59e0b; }
.timeline-dot.picked_up { background: #8b5cf6; }
.timeline-dot.on_the_way { background: #f97316; }
.timeline-dot.delivered { background: #22c55e; }
.timeline-dot.failed { background: #ef4444; }
.timeline-dot.cancelled { background: #6b7280; }

.timeline-content {
    background: var(--gray-50);
    border-radius: var(--radius-md);
    padding: 12px 16px;
}

.timeline-status {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
}

.timeline-meta {
    font-size: 13px;
    color: var(--gray-500);
}

/* Driver Select */
.driver-select-wrapper {
    margin-top: 16px;
}

.driver-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    margin-bottom: 12px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 32px;
    color: var(--gray-500);
}

/* Responsive */
@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .items-table {
        font-size: 13px;
    }

    .items-table th,
    .items-table td {
        padding: 10px 8px;
    }
}
</style>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-truck text-primary mr-2"></i>
            <?= $t['delivery_details'] ?> #<?= htmlspecialchars($delivery['order_number'] ?? '') ?>
        </h1>
        <p class="page-subtitle">
            <?php
            $status = $delivery['status'] ?? 'pending';
            $statusLabels = [
                'pending' => $t['pending'],
                'assigned' => $t['assigned'],
                'accepted' => $t['accepted'],
                'picked_up' => $t['picked_up'],
                'on_the_way' => $t['on_the_way'],
                'delivered' => $t['delivered'],
                'failed' => $t['failed'],
                'cancelled' => $t['cancelled']
            ];
            ?>
            <span class="status-badge status-<?= $status ?>">
                <?= $statusLabels[$status] ?? ucfirst($status) ?>
            </span>
        </p>
    </div>
    <a href="<?= url('/admin/delivery/active') ?>" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left mr-2"></i> <?= $t['back'] ?>
    </a>
</div>

<!-- Main Details Grid -->
<div class="details-grid">
    <!-- Order Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-receipt text-primary mr-2"></i>
                <?= $t['order_info'] ?>
            </h3>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['order_number'] ?></span>
            <span class="info-value">#<?= htmlspecialchars($delivery['order_number'] ?? '') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['order_date'] ?></span>
            <span class="info-value"><?= date('M d, Y g:i A', strtotime($delivery['order_created_at'] ?? 'now')) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['order_status'] ?></span>
            <span class="info-value"><?= ucfirst($delivery['order_status'] ?? '') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['delivery_fee'] ?></span>
            <span class="info-value"><?= currency($delivery['delivery_fee'] ?? 0) ?></span>
        </div>
        <?php if (!empty($delivery['distance_km'])): ?>
        <div class="info-row">
            <span class="info-label"><?= $t['distance'] ?></span>
            <span class="info-value"><?= number_format($delivery['distance_km'], 1) ?> <?= $t['km'] ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($delivery['order_notes'])): ?>
        <div class="info-row">
            <span class="info-label"><?= $t['order_notes'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['order_notes']) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Customer Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-user text-blue-500 mr-2"></i>
                <?= $t['customer_info'] ?>
            </h3>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['customer_name'] ?></span>
            <span class="info-value"><?= htmlspecialchars(($delivery['customer_first_name'] ?? '') . ' ' . ($delivery['customer_last_name'] ?? '')) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['customer_email'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['customer_email'] ?? $t['n_a']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['customer_phone'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['customer_phone'] ?? $t['n_a']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['delivery_address'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['delivery_address'] ?? $t['n_a']) ?></span>
        </div>
    </div>

    <!-- Driver Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-motorcycle text-purple-500 mr-2"></i>
                <?= $t['driver_info'] ?>
            </h3>
        </div>
        <?php if (!empty($delivery['driver_id'])): ?>
            <div class="info-row">
                <span class="info-label"><?= $t['driver_name'] ?></span>
                <span class="info-value"><?= htmlspecialchars($delivery['driver_first_name'] . ' ' . $delivery['driver_last_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><?= $t['driver_phone'] ?></span>
                <span class="info-value"><?= htmlspecialchars($delivery['driver_phone'] ?? $t['n_a']) ?></span>
            </div>
        <?php else: ?>
            <div class="info-row">
                <span class="info-label"><?= $t['status'] ?></span>
                <span class="info-value" style="color: #ef4444;"><?= $t['unassigned'] ?></span>
            </div>
        <?php endif; ?>

        <!-- Driver Assignment -->
        <?php if ($delivery['status'] !== 'delivered' && $delivery['status'] !== 'cancelled'): ?>
        <div class="driver-select-wrapper">
            <select id="driverSelect" class="driver-select">
                <option value=""><?= $t['select_driver'] ?></option>
                <?php foreach ($availableDrivers as $driver): ?>
                    <?php
                    $availStatus = $driver['availability_status'] ?? 'offline';
                    $statusLabel = $t[$availStatus] ?? $availStatus;
                    ?>
                    <option value="<?= $driver['id'] ?>" <?= ($driver['id'] == ($delivery['driver_id'] ?? 0)) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']) ?>
                        (<?= $statusLabel ?> - <?= $driver['active_deliveries'] ?? 0 ?> active)
                    </option>
                <?php endforeach; ?>
            </select>
            <button onclick="assignDriver()" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-user-check mr-1"></i>
                <?= empty($delivery['driver_id']) ? $t['assign_driver'] : $t['reassign_driver'] ?>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Shop Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa-solid fa-store text-orange-500 mr-2"></i>
                <?= $t['shop_info'] ?>
            </h3>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['shop_name'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['shop_name'] ?? '') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['shop_address'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['shop_address'] ?? $t['n_a']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= $t['shop_phone'] ?></span>
            <span class="info-value"><?= htmlspecialchars($delivery['shop_phone'] ?? $t['n_a']) ?></span>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa-solid fa-shopping-basket text-primary mr-2"></i>
            <?= $t['order_items'] ?>
        </h3>
    </div>

    <?php if (!empty($orderItems)): ?>
    <table class="items-table">
        <thead>
            <tr>
                <th><?= $t['product'] ?></th>
                <th class="text-center"><?= $t['quantity'] ?></th>
                <th class="text-right"><?= $t['price'] ?></th>
                <th class="text-right"><?= $t['subtotal'] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orderItems as $item): ?>
            <tr>
                <td>
                    <div class="product-cell">
                        <?php if (!empty($item['product_image'])): ?>
                        <img src="<?= url('/uploads/products/' . $item['product_image']) ?>" alt="" class="product-image">
                        <?php else: ?>
                        <div class="product-image" style="display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-image text-gray-300"></i>
                        </div>
                        <?php endif; ?>
                        <span class="product-name"><?= htmlspecialchars($item['product_name'] ?? '') ?></span>
                    </div>
                </td>
                <td class="text-center"><?= $item['quantity'] ?? 1 ?></td>
                <td class="text-right"><?= currency($item['price'] ?? 0) ?></td>
                <td class="text-right"><?= currency(($item['price'] ?? 0) * ($item['quantity'] ?? 1)) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="order-summary">
        <div class="summary-row">
            <span><?= $t['subtotal'] ?></span>
            <span><?= currency($delivery['subtotal'] ?? 0) ?></span>
        </div>
        <div class="summary-row">
            <span><?= $t['tax'] ?></span>
            <span><?= currency($delivery['tax'] ?? 0) ?></span>
        </div>
        <div class="summary-row">
            <span><?= $t['delivery_fee'] ?></span>
            <span><?= currency($delivery['delivery_fee'] ?? 0) ?></span>
        </div>
        <div class="summary-row total">
            <span><?= $t['total'] ?></span>
            <span><?= currency($delivery['order_total'] ?? 0) ?></span>
        </div>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fa-solid fa-box-open" style="font-size: 32px; color: var(--gray-300); margin-bottom: 12px;"></i>
        <p>No items found</p>
    </div>
    <?php endif; ?>
</div>

<!-- Status History -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa-solid fa-history text-primary mr-2"></i>
            <?= $t['status_history'] ?>
        </h3>
    </div>

    <?php if (!empty($statusHistory)): ?>
    <div class="timeline">
        <?php foreach ($statusHistory as $history): ?>
        <div class="timeline-item">
            <div class="timeline-dot <?= $history['status'] ?? '' ?>"></div>
            <div class="timeline-content">
                <div class="timeline-status">
                    <?= ucfirst(str_replace('_', ' ', $history['status'] ?? '')) ?>
                </div>
                <div class="timeline-meta">
                    <?php if (!empty($history['notes'])): ?>
                        <?= htmlspecialchars($history['notes']) ?> &bull;
                    <?php endif; ?>
                    <?php if (!empty($history['first_name'])): ?>
                        <?= htmlspecialchars($history['first_name'] . ' ' . $history['last_name']) ?> &bull;
                    <?php endif; ?>
                    <?= date('M d, Y g:i A', strtotime($history['created_at'] ?? 'now')) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fa-solid fa-clock-rotate-left" style="font-size: 32px; color: var(--gray-300); margin-bottom: 12px;"></i>
        <p><?= $t['no_history'] ?></p>
    </div>
    <?php endif; ?>
</div>

<script>
async function assignDriver() {
    const driverId = document.getElementById('driverSelect').value;
    const deliveryId = <?= $delivery['id'] ?? 0 ?>;

    if (!driverId) {
        alert('Please select a driver');
        return;
    }

    try {
        const response = await fetch('<?= url('/admin/delivery/assign-driver') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                delivery_id: deliveryId,
                driver_id: driverId
            })
        });

        const data = await response.json();

        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        alert('Network error: ' + error.message);
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
