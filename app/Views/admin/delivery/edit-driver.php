<?php
/**
 * OCS Admin Edit Delivery Driver
 * File: app/Views/admin/delivery/edit-driver.php
 */

$pageTitle = 'Edit Driver';
$currentPage = 'delivery';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

ob_start();
?>

<style>
  /* Page Layout */
  .edit-driver-page {
    max-width: 900px;
    margin: 0 auto;
  }

  /* Back Button */
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 24px;
    transition: color var(--transition-base);
  }

  .back-link:hover {
    color: var(--primary-600);
  }

  /* Page Header */
  .page-header {
    margin-bottom: 32px;
  }

  .page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .page-header p {
    font-size: 15px;
    color: var(--gray-600);
  }

  /* Form Card */
  .form-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 32px;
  }

  /* Form Sections */
  .form-section {
    margin-bottom: 32px;
    padding-bottom: 32px;
    border-bottom: 1px solid var(--border);
  }

  .form-section:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }

  .form-section-header {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 20px;
  }

  /* Form Grid */
  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
  }

  .form-group.full-width {
    grid-column: 1 / -1;
  }

  .form-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .form-label.required::after {
    content: ' *';
    color: #ef4444;
  }

  .form-input,
  .form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    transition: all var(--transition-base);
  }

  .form-input:focus,
  .form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .form-input:disabled,
  .form-input[readonly] {
    background: var(--gray-100);
    cursor: not-allowed;
    opacity: 0.7;
  }

  .form-hint {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 6px;
  }

  /* Info Box */
  .info-box {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
    padding: 16px;
    border-radius: var(--radius-md);
  }

  .info-box p {
    font-size: 14px;
    color: #1e40af;
    margin: 0;
  }

  .info-box i {
    margin-right: 8px;
  }

  .info-box strong {
    color: #1e3a8a;
  }

  /* Form Actions */
  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding-top: 24px;
    margin-top: 24px;
    border-top: 1px solid var(--border);
  }

  .btn {
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    border: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
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

  /* Alert */
  .alert {
    padding: 16px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
  }

  .alert-error {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
  }

  .alert-success {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #166534;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .form-grid {
      grid-template-columns: 1fr;
    }

    .form-actions {
      flex-direction: column;
    }

    .btn {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<div class="edit-driver-page">
  <!-- Back Button -->
  <a href="<?= url('admin/delivery/staff') ?>" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Delivery Staff
  </a>

  <!-- Page Header -->
  <div class="page-header">
    <h1>
      <i class="fas fa-user-edit" style="color: var(--primary);"></i>
      Edit Delivery Driver
    </h1>
    <p>Update driver information and settings</p>
  </div>

  <!-- Alerts -->
  <?php if (hasFlash('error')): ?>
    <div class="alert alert-error">
      <div style="display: flex; align-items: center;">
        <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
        <p style="margin: 0;"><?= getFlash('error') ?></p>
      </div>
    </div>
  <?php endif; ?>

  <?php if (hasFlash('success')): ?>
    <div class="alert alert-success">
      <div style="display: flex; align-items: center;">
        <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
        <p style="margin: 0;"><?= getFlash('success') ?></p>
      </div>
    </div>
  <?php endif; ?>

  <!-- Form -->
  <div class="form-card">
    <form method="POST" action="<?= url('admin/delivery/update-driver') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="driver_id" value="<?= $driver['id'] ?? '' ?>">

      <!-- Personal Information -->
      <div class="form-section">
        <h2 class="form-section-header">Personal Information</h2>
        
        <div class="form-grid">
          <!-- First Name -->
          <div class="form-group">
            <label class="form-label required">First Name</label>
            <input 
              type="text" 
              name="first_name" 
              value="<?= htmlspecialchars($driver['first_name'] ?? '') ?>"
              required
              class="form-input"
            >
          </div>

          <!-- Last Name -->
          <div class="form-group">
            <label class="form-label required">Last Name</label>
            <input 
              type="text" 
              name="last_name" 
              value="<?= htmlspecialchars($driver['last_name'] ?? '') ?>"
              required
              class="form-input"
            >
          </div>
        </div>
      </div>

      <!-- Contact Information -->
      <div class="form-section">
        <h2 class="form-section-header">Contact Information</h2>
        
        <div class="form-grid">
          <!-- Email (Read-only) -->
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input 
              type="email" 
              value="<?= htmlspecialchars($driver['email'] ?? '') ?>"
              readonly
              class="form-input"
            >
            <span class="form-hint">Email cannot be changed</span>
          </div>

          <!-- Phone -->
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input 
              type="tel" 
              name="phone" 
              value="<?= htmlspecialchars($driver['phone'] ?? '') ?>"
              class="form-input"
              placeholder="+1 (809) 555-1234"
            >
          </div>
        </div>
      </div>

      <!-- Delivery Settings -->
      <div class="form-section">
        <h2 class="form-section-header">Delivery Settings</h2>
        
        <div class="form-grid">
          <!-- Account Status -->
          <div class="form-group">
            <label class="form-label required">Account Status</label>
            <select name="status" required class="form-select">
              <option value="active" <?= ($driver['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= ($driver['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              <option value="suspended" <?= ($driver['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            </select>
          </div>

          <!-- Max Deliveries -->
          <div class="form-group">
            <label class="form-label">Max Simultaneous Deliveries</label>
            <input 
              type="number" 
              name="max_deliveries" 
              value="<?= htmlspecialchars($driver['max_deliveries'] ?? 3) ?>"
              min="1"
              max="10"
              class="form-input"
            >
            <span class="form-hint">How many orders can they handle at once</span>
          </div>
        </div>

        <!-- Zone Assignment -->
        <div class="form-group full-width" style="margin-top: 24px;">
          <label class="form-label">Assigned Delivery Zone</label>
          <select name="zone_id" class="form-select">
            <option value="">No zone assigned</option>
            <?php if (!empty($zones)): ?>
              <?php foreach ($zones as $zone): ?>
                <option 
                  value="<?= $zone['id'] ?>" 
                  <?= ($driver['zone_id'] ?? '') == $zone['id'] ? 'selected' : '' ?>
                >
                  <?= htmlspecialchars($zone['name']) ?> - <?= currency($zone['base_fee']) ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
      </div>

      <!-- Account Info -->
      <div class="form-section">
        <div class="info-box">
          <p>
            <i class="fas fa-calendar"></i>
            <strong>Account Created:</strong> 
            <?php if (!empty($driver['created_at'])): ?>
              <?= formatDate($driver['created_at'], 'F d, Y g:i A') ?>
            <?php else: ?>
              N/A
            <?php endif; ?>
          </p>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="form-actions">
        <a href="<?= url('admin/delivery/staff') ?>" class="btn btn-secondary">
          <i class="fas fa-times"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>