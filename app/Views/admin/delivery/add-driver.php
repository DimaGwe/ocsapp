<?php
/**
 * OCS Admin Add Delivery Driver
 * File: app/Views/admin/delivery/add-driver.php
 */

$pageTitle = 'Add Delivery Driver';
$currentPage = 'delivery-staff';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'add_driver' => 'Add Delivery Driver',
        'create_account' => 'Create a new delivery driver account',
        'personal_info' => 'Personal Information',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'contact_info' => 'Contact Information',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'account_security' => 'Account Security',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'min_chars' => 'Minimum 6 characters',
        'driver_setup' => 'Driver Account Setup',
        'setup_info1' => 'Driver will be created with "offline" status',
        'setup_info2' => 'They can login immediately with the provided credentials',
        'setup_info3' => 'Zone assignment can be done after account creation',
        'setup_info4' => 'Default max deliveries is set to 3',
        'cancel' => 'Cancel',
        'create_account_btn' => 'Create Driver Account',
        'required' => 'Required field',
        'passwords_not_match' => 'Passwords do not match!'
    ],
    'fr' => [
        'add_driver' => 'Ajouter Livreur',
        'create_account' => 'Créer un nouveau compte livreur',
        'personal_info' => 'Informations Personnelles',
        'first_name' => 'Prénom',
        'last_name' => 'Nom',
        'contact_info' => 'Informations de Contact',
        'email' => 'Adresse Email',
        'phone' => 'Numéro de Téléphone',
        'account_security' => 'Sécurité du Compte',
        'password' => 'Mot de Passe',
        'confirm_password' => 'Confirmer Mot de Passe',
        'min_chars' => 'Minimum 6 caractères',
        'driver_setup' => 'Configuration du Compte Livreur',
        'setup_info1' => 'Le livreur sera créé avec le statut "hors ligne"',
        'setup_info2' => 'Il peut se connecter immédiatement avec les identifiants fournis',
        'setup_info3' => 'L\'affectation de zone peut être faite après la création du compte',
        'setup_info4' => 'Le nombre maximum de livraisons est fixé à 3 par défaut',
        'cancel' => 'Annuler',
        'create_account_btn' => 'Créer Compte Livreur',
        'required' => 'Champ requis',
        'passwords_not_match' => 'Les mots de passe ne correspondent pas!'
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
/* Form Section */
.form-section {
    margin-bottom: 32px;
}

.form-section-header {
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.form-group {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
}

.form-label-required::after {
    content: " *";
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    transition: all var(--transition-base);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
}

.form-hint {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 4px;
}

/* Info Box */
.info-box {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
    border-radius: var(--radius-md);
    padding: 20px;
}

.info-box-content {
    display: flex;
    align-items: flex-start;
}

.info-box-icon {
    color: #3b82f6;
    font-size: 20px;
    margin-right: 12px;
    margin-top: 2px;
}

.info-box-text h4 {
    font-size: 14px;
    font-weight: 700;
    color: #1e40af;
    margin-bottom: 8px;
}

.info-box-text ul {
    list-style-type: disc;
    padding-left: 20px;
    margin: 0;
}

.info-box-text li {
    font-size: 13px;
    color: #1e40af;
    margin-bottom: 4px;
}

.info-box-text li:last-child {
    margin-bottom: 0;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 16px;
    padding-top: 24px;
    border-top: 1px solid var(--border);
    margin-top: 24px;
}

/* Responsive */
@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-user-plus text-primary mr-2"></i>
            <?= $t['add_driver'] ?>
        </h1>
        <p class="page-subtitle"><?= $t['create_account'] ?></p>
    </div>
</div>

<?php if (hasFlash('error')): ?>
<div class="alert alert-error">
    <div class="flex items-center">
        <i class="fa-solid fa-exclamation-circle mr-2"></i>
        <p><?= getFlash('error') ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Form -->
<div class="card">
    <form method="POST" action="<?= url('/admin/delivery/add-driver') ?>" id="addDriverForm">
        <?= csrfField() ?>

        <!-- Personal Information -->
        <div class="form-section">
            <h2 class="form-section-header"><?= $t['personal_info'] ?></h2>
            
            <div class="form-grid">
                <!-- First Name -->
                <div class="form-group">
                    <label class="form-label form-label-required">
                        <?= $t['first_name'] ?>
                    </label>
                    <input type="text" 
                           name="first_name" 
                           required
                           class="form-control"
                           placeholder="<?= $t['first_name'] ?>">
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label class="form-label form-label-required">
                        <?= $t['last_name'] ?>
                    </label>
                    <input type="text" 
                           name="last_name" 
                           required
                           class="form-control"
                           placeholder="<?= $t['last_name'] ?>">
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="form-section">
            <h2 class="form-section-header"><?= $t['contact_info'] ?></h2>
            
            <div class="form-grid">
                <!-- Email -->
                <div class="form-group">
                    <label class="form-label form-label-required">
                        <?= $t['email'] ?>
                    </label>
                    <input type="email" 
                           name="email" 
                           required
                           class="form-control"
                           placeholder="driver@example.com">
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label class="form-label">
                        <?= $t['phone'] ?>
                    </label>
                    <input type="tel" 
                           name="phone" 
                           class="form-control"
                           placeholder="809-123-4567">
                </div>
            </div>
        </div>

        <!-- Account Security -->
        <div class="form-section">
            <h2 class="form-section-header"><?= $t['account_security'] ?></h2>
            
            <div class="form-grid">
                <!-- Password -->
                <div class="form-group">
                    <label class="form-label form-label-required">
                        <?= $t['password'] ?>
                    </label>
                    <input type="password" 
                           name="password" 
                           required
                           minlength="6"
                           class="form-control"
                           placeholder="<?= $t['min_chars'] ?>">
                    <p class="form-hint"><?= $t['min_chars'] ?></p>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label class="form-label form-label-required">
                        <?= $t['confirm_password'] ?>
                    </label>
                    <input type="password" 
                           name="password_confirm" 
                           required
                           minlength="6"
                           class="form-control"
                           placeholder="<?= $t['confirm_password'] ?>">
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="form-section">
            <div class="info-box">
                <div class="info-box-content">
                    <i class="fa-solid fa-info-circle info-box-icon"></i>
                    <div class="info-box-text">
                        <h4><?= $t['driver_setup'] ?></h4>
                        <ul>
                            <li><?= $t['setup_info1'] ?></li>
                            <li><?= $t['setup_info2'] ?></li>
                            <li><?= $t['setup_info3'] ?></li>
                            <li><?= $t['setup_info4'] ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="<?= url('/admin/delivery/staff') ?>" 
               class="btn btn-secondary">
                <i class="fa-solid fa-times mr-2"></i> <?= $t['cancel'] ?>
            </a>
            <button type="submit" 
                    class="btn btn-primary">
                <i class="fa-solid fa-user-plus mr-2"></i> <?= $t['create_account_btn'] ?>
            </button>
        </div>
    </form>
</div>

<script>
// Password confirmation validation
document.getElementById('addDriverForm').addEventListener('submit', function(e) {
    const password = document.querySelector('input[name="password"]').value;
    const confirm = document.querySelector('input[name="password_confirm"]').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('<?= $t['passwords_not_match'] ?>');
        return false;
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
