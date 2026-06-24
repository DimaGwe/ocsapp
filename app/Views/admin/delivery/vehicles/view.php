<?php
/**
 * OCS Admin Fleet Management - Vehicle Details
 * File: app/Views/admin/delivery/vehicles/view.php
 */

$pageTitle = 'Vehicle Details';
$currentPage = 'vehicles';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'vehicle_details' => 'Vehicle Details',
        'back_to_fleet' => 'Back to Fleet',
        'edit_vehicle' => 'Edit Vehicle',
        'vehicle_information' => 'Vehicle Information',
        'driver_information' => 'Driver Assignment',
        'delivery_history' => 'Delivery History',
        'quick_actions' => 'Quick Actions',
        'change_status' => 'Change Status',
        'vehicle_type' => 'Vehicle Type',
        'make_model' => 'Make & Model',
        'year' => 'Year',
        'plate_number' => 'Plate Number',
        'color' => 'Color',
        'insurance_expiry' => 'Insurance Expiry',
        'status' => 'Status',
        'notes' => 'Notes',
        'assigned_driver' => 'Assigned Driver',
        'no_driver_assigned' => 'No driver assigned',
        'assign_driver' => 'Assign Driver',
        'change_driver' => 'Change Driver',
        'tracking_code' => 'Tracking Code',
        'delivery_address' => 'Delivery Address',
        'delivery_status' => 'Status',
        'date' => 'Date',
        'no_deliveries' => 'No delivery history available',
        'expired' => 'Expired',
        'expires_soon' => 'Expires Soon',
        'active' => 'Active',
        'maintenance' => 'Maintenance',
        'retired' => 'Retired',
        'bicycle' => 'Bicycle',
        'e_bike' => 'E-Bike',
        'scooter' => 'Scooter',
        'motorcycle' => 'Motorcycle',
        'car' => 'Car',
        'van' => 'Van',
        'no_notes' => 'No additional notes',
        'not_provided' => 'Not provided',
        'delivered' => 'Delivered',
        'in_transit' => 'In Transit',
        'pending' => 'Pending',
        'cancelled' => 'Cancelled'
    ],
    'fr' => [
        'vehicle_details' => 'Détails du Véhicule',
        'back_to_fleet' => 'Retour à la Flotte',
        'edit_vehicle' => 'Modifier Véhicule',
        'vehicle_information' => 'Informations du Véhicule',
        'driver_information' => 'Affectation du Livreur',
        'delivery_history' => 'Historique de Livraison',
        'quick_actions' => 'Actions Rapides',
        'change_status' => 'Changer le Statut',
        'vehicle_type' => 'Type de Véhicule',
        'make_model' => 'Marque & Modèle',
        'year' => 'Année',
        'plate_number' => 'Numéro de Plaque',
        'color' => 'Couleur',
        'insurance_expiry' => 'Expiration Assurance',
        'status' => 'Statut',
        'notes' => 'Notes',
        'assigned_driver' => 'Livreur Assigné',
        'no_driver_assigned' => 'Aucun livreur assigné',
        'assign_driver' => 'Assigner Livreur',
        'change_driver' => 'Changer Livreur',
        'tracking_code' => 'Code de Suivi',
        'delivery_address' => 'Adresse de Livraison',
        'delivery_status' => 'Statut',
        'date' => 'Date',
        'no_deliveries' => 'Aucun historique de livraison disponible',
        'expired' => 'Expiré',
        'expires_soon' => 'Expire Bientôt',
        'active' => 'Actif',
        'maintenance' => 'Maintenance',
        'retired' => 'Retiré',
        'bicycle' => 'Vélo',
        'e_bike' => 'Vélo Électrique',
        'scooter' => 'Scooter',
        'motorcycle' => 'Moto',
        'car' => 'Voiture',
        'van' => 'Camionnette',
        'no_notes' => 'Aucune note supplémentaire',
        'not_provided' => 'Non fourni',
        'delivered' => 'Livré',
        'in_transit' => 'En Transit',
        'pending' => 'En Attente',
        'cancelled' => 'Annulé'
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

// Vehicle type icons mapping
$vehicleIcons = [
    'bicycle' => '🚲',
    'e-bike' => '⚡',
    'scooter' => '🛵',
    'motorcycle' => '🏍️',
    'car' => '🚗',
    'van' => '🚐'
];

ob_start();
?>

<style>
/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 16px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
    font-family: 'Poppins', sans-serif;
}

.header-actions {
    display: flex;
    gap: 12px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all var(--transition-base);
    cursor: pointer;
    border: none;
    text-decoration: none;
    gap: 8px;
}

.btn-primary {
    background: #00b207;
    color: white;
}

.btn-primary:hover {
    background: #009206;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 178, 7, 0.2);
}

.btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
}

.btn-secondary:hover {
    background: var(--gray-300);
}

/* Grid Layout */
.details-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

/* Card */
.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    padding: 24px;
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--gray-100);
}

/* Vehicle Header */
.vehicle-header {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 24px;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-radius: 8px;
    margin-bottom: 24px;
}

.vehicle-icon-large {
    font-size: 64px;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.vehicle-header-info h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
}

.vehicle-header-info p {
    font-size: 14px;
    color: var(--gray-600);
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 12px;
    font-weight: 500;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}

.info-value {
    font-size: 15px;
    color: var(--dark);
    font-weight: 500;
}

.info-value.plate {
    font-family: monospace;
    font-weight: 700;
    font-size: 16px;
}

/* Badge */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
    gap: 6px;
}

.badge-active {
    background: #dcfce7;
    color: #166534;
}

.badge-maintenance {
    background: #ffedd5;
    color: #9a3412;
}

.badge-retired {
    background: var(--gray-200);
    color: var(--gray-600);
}

.badge-warning {
    background: #fee2e2;
    color: #991b1b;
}

.badge-info {
    background: #fef3c7;
    color: #92400e;
}

.badge-delivered {
    background: #dcfce7;
    color: #166534;
}

.badge-in-transit {
    background: #dbeafe;
    color: #1e40af;
}

.badge-pending {
    background: #fef3c7;
    color: #92400e;
}

.badge-cancelled {
    background: var(--gray-200);
    color: var(--gray-600);
}

/* Driver Card */
.driver-card {
    text-align: center;
}

.driver-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #00b207;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    margin: 0 auto 16px;
}

.driver-name {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
}

.driver-action {
    margin-top: 16px;
}

.no-driver {
    text-align: center;
    padding: 40px 20px;
    color: var(--gray-500);
}

.no-driver i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
    color: var(--gray-300);
}

/* Quick Actions */
.quick-actions-form {
    display: flex;
    gap: 12px;
    align-items: flex-end;
}

.form-group {
    flex: 1;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--gray-700);
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    transition: all var(--transition-base);
}

.form-control:focus {
    outline: none;
    border-color: #00b207;
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
}

/* Delivery History */
.delivery-list {
    max-height: 600px;
    overflow-y: auto;
}

.delivery-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    border: 1px solid var(--border);
    border-radius: 8px;
    margin-bottom: 12px;
    transition: all var(--transition-base);
}

.delivery-item:hover {
    background: var(--gray-50);
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.delivery-info h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
}

.delivery-info p {
    font-size: 13px;
    color: var(--gray-600);
}

.delivery-meta {
    text-align: right;
}

.delivery-date {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 8px;
}

.empty-history {
    text-align: center;
    padding: 40px 20px;
    color: var(--gray-500);
}

.empty-history i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
    color: var(--gray-300);
}

/* Responsive */
@media (max-width: 1024px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }

    .vehicle-header {
        flex-direction: column;
        text-align: center;
    }

    .quick-actions-form {
        flex-direction: column;
    }

    .header-actions {
        width: 100%;
    }

    .btn {
        flex: 1;
    }
}
</style>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-truck" style="color: #00b207;"></i>
            <?= $t['vehicle_details'] ?>
        </h1>
    </div>
    <div class="header-actions">
        <a href="<?= url('/admin/delivery/vehicles') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <?= $t['back_to_fleet'] ?>
        </a>
        <a href="<?= url('/admin/delivery/vehicles/edit/' . ($vehicle['id'] ?? '')) ?>" class="btn btn-primary">
            <i class="fa-solid fa-edit"></i>
            <?= $t['edit_vehicle'] ?>
        </a>
    </div>
</div>

<!-- Vehicle Header Card -->
<div class="vehicle-header">
    <div class="vehicle-icon-large">
        <?= $vehicleIcons[$vehicle['vehicle_type'] ?? 'car'] ?? '🚗' ?>
    </div>
    <div class="vehicle-header-info">
        <h2><?= htmlspecialchars(($vehicle['make'] ?? '') . ' ' . ($vehicle['model'] ?? '')) ?></h2>
        <p>
            <?= htmlspecialchars($vehicle['year'] ?? '') ?> •
            <?= $t[$vehicle['vehicle_type'] ?? 'car'] ?? ucfirst($vehicle['vehicle_type'] ?? 'Car') ?>
            <?php if (!empty($vehicle['plate_number'])): ?>
                • <?= htmlspecialchars($vehicle['plate_number']) ?>
            <?php endif; ?>
        </p>
    </div>
</div>

<!-- Main Content Grid -->
<div class="details-grid">
    <!-- Left Column -->
    <div>
        <!-- Vehicle Information Card -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 class="card-title"><?= $t['vehicle_information'] ?></h3>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label"><?= $t['vehicle_type'] ?></span>
                    <span class="info-value">
                        <?= $vehicleIcons[$vehicle['vehicle_type'] ?? 'car'] ?? '' ?>
                        <?= $t[$vehicle['vehicle_type'] ?? 'car'] ?? ucfirst($vehicle['vehicle_type'] ?? 'Car') ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label"><?= $t['year'] ?></span>
                    <span class="info-value"><?= htmlspecialchars($vehicle['year'] ?? 'N/A') ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label"><?= $t['make_model'] ?></span>
                    <span class="info-value"><?= htmlspecialchars(($vehicle['make'] ?? '') . ' ' . ($vehicle['model'] ?? '')) ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label"><?= $t['plate_number'] ?></span>
                    <span class="info-value plate">
                        <?= !empty($vehicle['plate_number']) ? htmlspecialchars($vehicle['plate_number']) : '<span style="color: var(--gray-400);">' . $t['not_provided'] . '</span>' ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label"><?= $t['color'] ?></span>
                    <span class="info-value"><?= htmlspecialchars($vehicle['color'] ?? 'N/A') ?></span>
                </div>

                <div class="info-item">
                    <span class="info-label"><?= $t['insurance_expiry'] ?></span>
                    <span class="info-value">
                        <?php
                        $insuranceExpiry = strtotime($vehicle['insurance_expiry'] ?? 'now');
                        $now = time();
                        $daysUntilExpiry = floor(($insuranceExpiry - $now) / 86400);
                        $isExpired = $daysUntilExpiry < 0;
                        $expiringSoon = $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
                        ?>
                        <?= date('M d, Y', $insuranceExpiry) ?>
                        <?php if ($isExpired): ?>
                            <span class="badge badge-warning" style="margin-left: 8px;">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?= $t['expired'] ?>
                            </span>
                        <?php elseif ($expiringSoon): ?>
                            <span class="badge badge-info" style="margin-left: 8px;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <?= $t['expires_soon'] ?>
                            </span>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label"><?= $t['status'] ?></span>
                    <span class="info-value">
                        <?php
                        $statusClass = 'badge-active';
                        if (($vehicle['status'] ?? 'active') === 'maintenance') {
                            $statusClass = 'badge-maintenance';
                        } elseif (($vehicle['status'] ?? 'active') === 'retired') {
                            $statusClass = 'badge-retired';
                        }
                        ?>
                        <span class="badge <?= $statusClass ?>">
                            <?= $t[$vehicle['status'] ?? 'active'] ?? ucfirst($vehicle['status'] ?? 'Active') ?>
                        </span>
                    </span>
                </div>

                <div class="info-item" style="grid-column: 1 / -1;">
                    <span class="info-label"><?= $t['notes'] ?></span>
                    <span class="info-value" style="color: var(--gray-600); font-weight: 400;">
                        <?= !empty($vehicle['notes']) ? nl2br(htmlspecialchars($vehicle['notes'])) : '<span style="color: var(--gray-400);">' . $t['no_notes'] . '</span>' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Delivery History Card -->
        <div class="card">
            <h3 class="card-title"><?= $t['delivery_history'] ?></h3>

            <?php if (!empty($deliveryHistory)): ?>
            <div class="delivery-list">
                <?php foreach (array_slice($deliveryHistory, 0, 20) as $delivery): ?>
                <div class="delivery-item">
                    <div class="delivery-info">
                        <h4>
                            <i class="fa-solid fa-hashtag" style="font-size: 12px; color: var(--gray-400);"></i>
                            <?= htmlspecialchars($delivery['tracking_code'] ?? 'N/A') ?>
                        </h4>
                        <p>
                            <i class="fa-solid fa-location-dot" style="font-size: 11px;"></i>
                            <?= htmlspecialchars($delivery['delivery_address'] ?? 'N/A') ?>
                        </p>
                        <p class="delivery-date">
                            <i class="fa-solid fa-calendar" style="font-size: 11px;"></i>
                            <?= date('M d, Y g:i A', strtotime($delivery['delivered_at'] ?? $delivery['created_at'] ?? 'now')) ?>
                        </p>
                    </div>
                    <div class="delivery-meta">
                        <?php
                        $deliveryStatus = $delivery['status'] ?? 'pending';
                        $statusBadgeClass = 'badge-pending';
                        if ($deliveryStatus === 'delivered') {
                            $statusBadgeClass = 'badge-delivered';
                        } elseif ($deliveryStatus === 'in_transit') {
                            $statusBadgeClass = 'badge-in-transit';
                        } elseif ($deliveryStatus === 'cancelled') {
                            $statusBadgeClass = 'badge-cancelled';
                        }
                        ?>
                        <span class="badge <?= $statusBadgeClass ?>">
                            <?= $t[$deliveryStatus] ?? ucfirst($deliveryStatus) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-history">
                <i class="fa-solid fa-box-open"></i>
                <p><?= $t['no_deliveries'] ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column -->
    <div>
        <!-- Driver Card -->
        <div class="card driver-card" style="margin-bottom: 24px;">
            <h3 class="card-title"><?= $t['driver_information'] ?></h3>

            <?php if (!empty($vehicle['driver_first_name'])): ?>
            <div class="driver-avatar">
                <?= strtoupper(substr($vehicle['driver_first_name'], 0, 1)) ?>
            </div>
            <p class="driver-name">
                <?= htmlspecialchars($vehicle['driver_first_name'] . ' ' . $vehicle['driver_last_name']) ?>
            </p>
            <p style="color: var(--gray-600); font-size: 13px;"><?= $t['assigned_driver'] ?></p>
            <div class="driver-action">
                <a href="<?= url('/admin/delivery/vehicles/edit/' . ($vehicle['id'] ?? '')) ?>" class="btn btn-secondary" style="width: 100%;">
                    <i class="fa-solid fa-user-edit"></i>
                    <?= $t['change_driver'] ?>
                </a>
            </div>
            <?php else: ?>
            <div class="no-driver">
                <i class="fa-solid fa-user-slash"></i>
                <p><?= $t['no_driver_assigned'] ?></p>
                <div class="driver-action">
                    <a href="<?= url('/admin/delivery/vehicles/edit/' . ($vehicle['id'] ?? '')) ?>" class="btn btn-primary" style="width: 100%;">
                        <i class="fa-solid fa-user-plus"></i>
                        <?= $t['assign_driver'] ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions Card -->
        <div class="card">
            <h3 class="card-title"><?= $t['quick_actions'] ?></h3>

            <form class="quick-actions-form" id="statusForm">
                <div class="form-group">
                    <label class="form-label"><?= $t['change_status'] ?></label>
                    <select name="status" class="form-control" id="statusSelect">
                        <option value="active" <?= ($vehicle['status'] ?? 'active') === 'active' ? 'selected' : '' ?>><?= $t['active'] ?></option>
                        <option value="maintenance" <?= ($vehicle['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>><?= $t['maintenance'] ?></option>
                        <option value="retired" <?= ($vehicle['status'] ?? '') === 'retired' ? 'selected' : '' ?>><?= $t['retired'] ?></option>
                    </select>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Quick status change via AJAX
document.getElementById('statusSelect').addEventListener('change', function() {
    const newStatus = this.value;
    const vehicleId = <?= json_encode($vehicle['id'] ?? 0) ?>;

    if (confirm('Are you sure you want to change the vehicle status?')) {
        fetch('<?= url('/admin/delivery/vehicles/update-status') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: vehicleId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to update status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status.');
        });
    } else {
        // Revert the select to original value
        this.value = '<?= $vehicle['status'] ?? 'active' ?>';
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../admin/layout.php';
?>
