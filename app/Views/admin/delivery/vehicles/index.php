<?php
/**
 * OCS Admin Fleet Management - Vehicle List
 * File: app/Views/admin/delivery/vehicles/index.php
 */

$pageTitle = 'Fleet Management';
$currentPage = 'vehicles';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'fleet_management' => 'Fleet Management',
        'manage_vehicles' => 'Manage delivery fleet and vehicle assignments',
        'add_vehicle' => 'Add Vehicle',
        'total_vehicles' => 'Total Vehicles',
        'active_vehicles' => 'Active Vehicles',
        'maintenance' => 'In Maintenance',
        'unassigned' => 'Unassigned',
        'all_status' => 'All Status',
        'all_types' => 'All Types',
        'vehicle' => 'Vehicle',
        'plate_number' => 'Plate #',
        'color' => 'Color',
        'driver' => 'Driver',
        'insurance_expiry' => 'Insurance Expiry',
        'status' => 'Status',
        'actions' => 'Actions',
        'view' => 'View',
        'edit' => 'Edit',
        'no_vehicles' => 'No vehicles found',
        'add_vehicle_to_start' => 'Add your first vehicle to get started',
        'unassigned_driver' => 'Unassigned',
        'expired' => 'Expired',
        'expires_soon' => 'Expires Soon',
        'active' => 'Active',
        'retired' => 'Retired',
        'bicycle' => 'Bicycle',
        'e_bike' => 'E-Bike',
        'scooter' => 'Scooter',
        'motorcycle' => 'Motorcycle',
        'car' => 'Car',
        'van' => 'Van'
    ],
    'fr' => [
        'fleet_management' => 'Gestion de Flotte',
        'manage_vehicles' => 'Gérer la flotte de livraison et les affectations de véhicules',
        'add_vehicle' => 'Ajouter Véhicule',
        'total_vehicles' => 'Total Véhicules',
        'active_vehicles' => 'Véhicules Actifs',
        'maintenance' => 'En Maintenance',
        'unassigned' => 'Non Assignés',
        'all_status' => 'Tous Statuts',
        'all_types' => 'Tous Types',
        'vehicle' => 'Véhicule',
        'plate_number' => 'Plaque',
        'color' => 'Couleur',
        'driver' => 'Livreur',
        'insurance_expiry' => 'Expiration Assurance',
        'status' => 'Statut',
        'actions' => 'Actions',
        'view' => 'Voir',
        'edit' => 'Modifier',
        'no_vehicles' => 'Aucun véhicule trouvé',
        'add_vehicle_to_start' => 'Ajoutez votre premier véhicule pour commencer',
        'unassigned_driver' => 'Non Assigné',
        'expired' => 'Expiré',
        'expires_soon' => 'Expire Bientôt',
        'active' => 'Actif',
        'retired' => 'Retiré',
        'bicycle' => 'Vélo',
        'e_bike' => 'Vélo Électrique',
        'scooter' => 'Scooter',
        'motorcycle' => 'Moto',
        'car' => 'Voiture',
        'van' => 'Camionnette'
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

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

/* Stats Bar */
.stats-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: all var(--transition-base);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-label {
    font-size: 13px;
    color: var(--gray-500);
    margin-bottom: 8px;
    font-weight: 500;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--dark);
}

.stat-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    margin-bottom: 12px;
    font-size: 18px;
}

.stat-icon.total { background: #dbeafe; color: #1d4ed8; }
.stat-icon.active { background: #dcfce7; color: #16a34a; }
.stat-icon.maintenance { background: #ffedd5; color: #ea580c; }
.stat-icon.unassigned { background: #f3e8ff; color: #9333ea; }

/* Card */
.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    padding: 24px;
    margin-bottom: 24px;
}

/* Filter Bar */
.filter-bar {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.form-group {
    flex: 1;
    min-width: 200px;
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

/* Table */
.table-container {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead {
    background: var(--gray-50);
}

.table th {
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-500);
    border-bottom: 1px solid var(--border);
}

.table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
}

.table tbody tr {
    transition: background var(--transition-base);
}

.table tbody tr:hover {
    background: var(--gray-50);
}

/* Vehicle Display */
.vehicle-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.vehicle-icon {
    font-size: 24px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    border-radius: 8px;
}

.vehicle-details h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
}

.vehicle-details p {
    font-size: 13px;
    color: var(--gray-600);
}

/* Badge */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
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

.badge-unassigned {
    background: #f3e8ff;
    color: #7e22ce;
}

.badge-warning {
    background: #fee2e2;
    color: #991b1b;
}

.badge-info {
    background: #fef3c7;
    color: #92400e;
}

/* Insurance Status */
.insurance-status {
    display: flex;
    align-items: center;
    gap: 8px;
}

.insurance-status.expired {
    color: #dc2626;
}

.insurance-status.expiring-soon {
    color: #ea580c;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 64px 24px;
}

.empty-state-icon {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 20px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
}

.empty-state p {
    color: var(--gray-500);
    margin-bottom: 24px;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-bar {
        grid-template-columns: 1fr 1fr;
    }

    .filter-bar {
        flex-direction: column;
    }

    .table-container {
        overflow-x: scroll;
    }
}
</style>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-truck" style="color: #00b207;"></i>
            <?= $t['fleet_management'] ?>
        </h1>
        <p class="page-subtitle"><?= $t['manage_vehicles'] ?></p>
    </div>
    <a href="<?= url('/admin/delivery/vehicles/create') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i>
        <?= $t['add_vehicle'] ?>
    </a>
</div>

<!-- Stats Bar -->
<div class="stats-bar">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fa-solid fa-truck"></i>
        </div>
        <p class="stat-label"><?= $t['total_vehicles'] ?></p>
        <p class="stat-value"><?= number_format($stats['total'] ?? 0) ?></p>
    </div>

    <div class="stat-card">
        <div class="stat-icon active">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <p class="stat-label"><?= $t['active_vehicles'] ?></p>
        <p class="stat-value" style="color: #16a34a;"><?= number_format($stats['active'] ?? 0) ?></p>
    </div>

    <div class="stat-card">
        <div class="stat-icon maintenance">
            <i class="fa-solid fa-wrench"></i>
        </div>
        <p class="stat-label"><?= $t['maintenance'] ?></p>
        <p class="stat-value" style="color: #ea580c;"><?= number_format($stats['maintenance'] ?? 0) ?></p>
    </div>

    <div class="stat-card">
        <div class="stat-icon unassigned">
            <i class="fa-solid fa-user-slash"></i>
        </div>
        <p class="stat-label"><?= $t['unassigned'] ?></p>
        <p class="stat-value" style="color: #9333ea;"><?= number_format($stats['unassigned'] ?? 0) ?></p>
    </div>
</div>

<!-- Filter + Table Card -->
<div class="card">
    <!-- Filter Bar -->
    <form method="GET" action="<?= url('/admin/delivery/vehicles') ?>">
        <div class="filter-bar">
            <div class="form-group">
                <label class="form-label"><?= $t['status'] ?></label>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value=""><?= $t['all_status'] ?></option>
                    <option value="active" <?= ($filterStatus ?? '') === 'active' ? 'selected' : '' ?>><?= $t['active'] ?></option>
                    <option value="maintenance" <?= ($filterStatus ?? '') === 'maintenance' ? 'selected' : '' ?>><?= $t['maintenance'] ?></option>
                    <option value="retired" <?= ($filterStatus ?? '') === 'retired' ? 'selected' : '' ?>><?= $t['retired'] ?></option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $t['vehicle'] ?> Type</label>
                <select name="type" class="form-control" onchange="this.form.submit()">
                    <option value=""><?= $t['all_types'] ?></option>
                    <option value="bicycle" <?= ($filterType ?? '') === 'bicycle' ? 'selected' : '' ?>><?= $t['bicycle'] ?></option>
                    <option value="e-bike" <?= ($filterType ?? '') === 'e-bike' ? 'selected' : '' ?>><?= $t['e_bike'] ?></option>
                    <option value="scooter" <?= ($filterType ?? '') === 'scooter' ? 'selected' : '' ?>><?= $t['scooter'] ?></option>
                    <option value="motorcycle" <?= ($filterType ?? '') === 'motorcycle' ? 'selected' : '' ?>><?= $t['motorcycle'] ?></option>
                    <option value="car" <?= ($filterType ?? '') === 'car' ? 'selected' : '' ?>><?= $t['car'] ?></option>
                    <option value="van" <?= ($filterType ?? '') === 'van' ? 'selected' : '' ?>><?= $t['van'] ?></option>
                </select>
            </div>
        </div>
    </form>

    <!-- Table -->
    <?php if (!empty($vehicles)): ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><?= $t['vehicle'] ?></th>
                    <th><?= $t['plate_number'] ?></th>
                    <th><?= $t['color'] ?></th>
                    <th><?= $t['driver'] ?></th>
                    <th><?= $t['insurance_expiry'] ?></th>
                    <th><?= $t['status'] ?></th>
                    <th><?= $t['actions'] ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicles as $vehicle): ?>
                <tr>
                    <td>
                        <div class="vehicle-info">
                            <div class="vehicle-icon">
                                <?= $vehicleIcons[$vehicle['vehicle_type']] ?? '🚗' ?>
                            </div>
                            <div class="vehicle-details">
                                <h4><?= htmlspecialchars($vehicle['make']) ?> <?= htmlspecialchars($vehicle['model']) ?></h4>
                                <p><?= htmlspecialchars($vehicle['year']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($vehicle['plate_number'])): ?>
                            <span style="font-family: monospace; font-weight: 600;"><?= htmlspecialchars($vehicle['plate_number']) ?></span>
                        <?php else: ?>
                            <span style="color: var(--gray-400);">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($vehicle['color']) ?></td>
                    <td>
                        <?php if (!empty($vehicle['driver_first_name'])): ?>
                            <?= htmlspecialchars($vehicle['driver_first_name'] . ' ' . $vehicle['driver_last_name']) ?>
                        <?php else: ?>
                            <span class="badge badge-unassigned"><?= $t['unassigned_driver'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $insuranceExpiry = strtotime($vehicle['insurance_expiry'] ?? 'now');
                        $now = time();
                        $daysUntilExpiry = floor(($insuranceExpiry - $now) / 86400);
                        $isExpired = $daysUntilExpiry < 0;
                        $expiringSoon = $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30;
                        ?>
                        <div class="insurance-status <?= $isExpired ? 'expired' : ($expiringSoon ? 'expiring-soon' : '') ?>">
                            <?php if ($isExpired): ?>
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span><?= date('M d, Y', $insuranceExpiry) ?></span>
                                <span class="badge badge-warning"><?= $t['expired'] ?></span>
                            <?php elseif ($expiringSoon): ?>
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span><?= date('M d, Y', $insuranceExpiry) ?></span>
                                <span class="badge badge-info"><?= $t['expires_soon'] ?></span>
                            <?php else: ?>
                                <?= date('M d, Y', $insuranceExpiry) ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php
                        $statusClass = 'badge-active';
                        if ($vehicle['status'] === 'maintenance') {
                            $statusClass = 'badge-maintenance';
                        } elseif ($vehicle['status'] === 'retired') {
                            $statusClass = 'badge-retired';
                        }
                        ?>
                        <span class="badge <?= $statusClass ?>">
                            <?= $t[$vehicle['status']] ?? ucfirst($vehicle['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="<?= url('/admin/delivery/vehicles/view/' . $vehicle['id']) ?>" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-eye"></i>
                                <?= $t['view'] ?>
                            </a>
                            <a href="<?= url('/admin/delivery/vehicles/edit/' . $vehicle['id']) ?>" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-edit"></i>
                                <?= $t['edit'] ?>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <!-- Empty State -->
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fa-solid fa-truck"></i>
        </div>
        <h3><?= $t['no_vehicles'] ?></h3>
        <p><?= $t['add_vehicle_to_start'] ?></p>
        <a href="<?= url('/admin/delivery/vehicles/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i>
            <?= $t['add_vehicle'] ?>
        </a>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../admin/layout.php';
?>
