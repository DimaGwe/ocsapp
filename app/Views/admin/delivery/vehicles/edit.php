<?php
/**
 * OCS Admin Fleet Management - Edit Vehicle
 * File: app/Views/admin/delivery/vehicles/edit.php
 */

$pageTitle = 'Edit Vehicle';
$currentPage = 'vehicles';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'edit_vehicle' => 'Edit Vehicle',
        'back_to_fleet' => 'Back to Fleet',
        'vehicle_information' => 'Vehicle Information',
        'vehicle_type' => 'Vehicle Type',
        'select_type' => 'Select vehicle type',
        'make' => 'Make',
        'make_placeholder' => 'e.g., Toyota, Honda, Yamaha',
        'model' => 'Model',
        'model_placeholder' => 'e.g., Corolla, Civic, MT-07',
        'year' => 'Year',
        'plate_number' => 'Plate Number',
        'plate_placeholder' => 'e.g., ABC-1234',
        'plate_optional' => 'Optional',
        'color' => 'Color',
        'color_placeholder' => 'e.g., Black, White, Red',
        'insurance_expiry' => 'Insurance Expiry Date',
        'assign_driver' => 'Assign to Driver',
        'no_driver' => '— No driver —',
        'notes' => 'Notes',
        'notes_placeholder' => 'Additional information about this vehicle...',
        'submit' => 'Update Vehicle',
        'cancel' => 'Cancel',
        'bicycle' => 'Bicycle',
        'e_bike' => 'E-Bike',
        'scooter' => 'Scooter',
        'motorcycle' => 'Motorcycle',
        'car' => 'Car',
        'van' => 'Van'
    ],
    'fr' => [
        'edit_vehicle' => 'Modifier Véhicule',
        'back_to_fleet' => 'Retour à la Flotte',
        'vehicle_information' => 'Informations du Véhicule',
        'vehicle_type' => 'Type de Véhicule',
        'select_type' => 'Sélectionner le type',
        'make' => 'Marque',
        'make_placeholder' => 'ex: Toyota, Honda, Yamaha',
        'model' => 'Modèle',
        'model_placeholder' => 'ex: Corolla, Civic, MT-07',
        'year' => 'Année',
        'plate_number' => 'Numéro de Plaque',
        'plate_placeholder' => 'ex: ABC-1234',
        'plate_optional' => 'Optionnel',
        'color' => 'Couleur',
        'color_placeholder' => 'ex: Noir, Blanc, Rouge',
        'insurance_expiry' => 'Date d\'Expiration Assurance',
        'assign_driver' => 'Assigner au Livreur',
        'no_driver' => '— Aucun livreur —',
        'notes' => 'Notes',
        'notes_placeholder' => 'Informations supplémentaires sur ce véhicule...',
        'submit' => 'Mettre à Jour Véhicule',
        'cancel' => 'Annuler',
        'bicycle' => 'Vélo',
        'e_bike' => 'Vélo Électrique',
        'scooter' => 'Scooter',
        'motorcycle' => 'Moto',
        'car' => 'Voiture',
        'van' => 'Camionnette'
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

/* Card */
.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    padding: 32px;
    margin-bottom: 24px;
    max-width: 800px;
}

.card-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--gray-100);
}

/* Form */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

.form-row.single {
    grid-template-columns: 1fr;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--gray-700);
    font-size: 14px;
}

.form-label .optional {
    color: var(--gray-400);
    font-weight: 400;
    font-size: 12px;
    margin-left: 4px;
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

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 16px;
    justify-content: flex-end;
    padding-top: 24px;
    border-top: 2px solid var(--gray-100);
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-edit" style="color: #00b207;"></i>
            <?= $t['edit_vehicle'] ?>
        </h1>
    </div>
    <a href="<?= url('/admin/delivery/vehicles') ?>" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        <?= $t['back_to_fleet'] ?>
    </a>
</div>

<!-- Form Card -->
<div class="card">
    <h2 class="card-title"><?= $t['vehicle_information'] ?></h2>

    <form method="POST" action="<?= url('/admin/delivery/vehicles/update') ?>">
        <?= csrfField() ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($vehicle['id'] ?? '') ?>">

        <!-- Vehicle Type -->
        <div class="form-row single">
            <div class="form-group">
                <label class="form-label"><?= $t['vehicle_type'] ?> *</label>
                <select name="vehicle_type" class="form-control" required>
                    <option value=""><?= $t['select_type'] ?></option>
                    <option value="bicycle" <?= ($vehicle['vehicle_type'] ?? '') === 'bicycle' ? 'selected' : '' ?>><?= $t['bicycle'] ?> 🚲</option>
                    <option value="e-bike" <?= ($vehicle['vehicle_type'] ?? '') === 'e-bike' ? 'selected' : '' ?>><?= $t['e_bike'] ?> ⚡</option>
                    <option value="scooter" <?= ($vehicle['vehicle_type'] ?? '') === 'scooter' ? 'selected' : '' ?>><?= $t['scooter'] ?> 🛵</option>
                    <option value="motorcycle" <?= ($vehicle['vehicle_type'] ?? '') === 'motorcycle' ? 'selected' : '' ?>><?= $t['motorcycle'] ?> 🏍️</option>
                    <option value="car" <?= ($vehicle['vehicle_type'] ?? '') === 'car' ? 'selected' : '' ?>><?= $t['car'] ?> 🚗</option>
                    <option value="van" <?= ($vehicle['vehicle_type'] ?? '') === 'van' ? 'selected' : '' ?>><?= $t['van'] ?> 🚐</option>
                </select>
            </div>
        </div>

        <!-- Make & Model -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= $t['make'] ?> *</label>
                <input type="text" name="make" class="form-control"
                       placeholder="<?= $t['make_placeholder'] ?>"
                       value="<?= htmlspecialchars($vehicle['make'] ?? '') ?>"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $t['model'] ?> *</label>
                <input type="text" name="model" class="form-control"
                       placeholder="<?= $t['model_placeholder'] ?>"
                       value="<?= htmlspecialchars($vehicle['model'] ?? '') ?>"
                       required>
            </div>
        </div>

        <!-- Year & Plate Number -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= $t['year'] ?> *</label>
                <input type="number" name="year" class="form-control"
                       min="2000" max="<?= date('Y') + 1 ?>"
                       value="<?= htmlspecialchars($vehicle['year'] ?? date('Y')) ?>"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <?= $t['plate_number'] ?>
                    <span class="optional">(<?= $t['plate_optional'] ?>)</span>
                </label>
                <input type="text" name="plate_number" class="form-control"
                       placeholder="<?= $t['plate_placeholder'] ?>"
                       value="<?= htmlspecialchars($vehicle['plate_number'] ?? '') ?>">
            </div>
        </div>

        <!-- Color & Insurance Expiry -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= $t['color'] ?> *</label>
                <input type="text" name="color" class="form-control"
                       placeholder="<?= $t['color_placeholder'] ?>"
                       value="<?= htmlspecialchars($vehicle['color'] ?? '') ?>"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label"><?= $t['insurance_expiry'] ?> *</label>
                <input type="date" name="insurance_expiry" class="form-control"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($vehicle['insurance_expiry'] ?? '') ?>"
                       required>
            </div>
        </div>

        <!-- Assign Driver -->
        <div class="form-row single">
            <div class="form-group">
                <label class="form-label"><?= $t['assign_driver'] ?></label>
                <select name="driver_id" class="form-control">
                    <option value=""><?= $t['no_driver'] ?></option>
                    <?php if (!empty($drivers)): ?>
                        <?php foreach ($drivers as $driver): ?>
                            <option value="<?= $driver['driver_id'] ?>"
                                <?= ($vehicle['driver_id'] ?? '') == $driver['driver_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <!-- Notes -->
        <div class="form-row single">
            <div class="form-group">
                <label class="form-label"><?= $t['notes'] ?></label>
                <textarea name="notes" class="form-control" placeholder="<?= $t['notes_placeholder'] ?>"><?= htmlspecialchars($vehicle['notes'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="<?= url('/admin/delivery/vehicles') ?>" class="btn btn-secondary">
                <i class="fa-solid fa-times"></i>
                <?= $t['cancel'] ?>
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-check"></i>
                <?= $t['submit'] ?>
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../admin/layout.php';
?>
