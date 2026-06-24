<?php
/**
 * OCS Admin Edit Seller
 * File: app/Views/admin/sellers/edit.php
 */

$pageTitle = 'Edit Seller';
$currentPage = 'sellers';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

ob_start();
?>

<style>
  .page-header {
    margin-bottom: 32px;
  }

  .page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 16px;
  }

  .back-link:hover {
    color: var(--primary-600);
  }

  .form-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    max-width: 800px;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 24px;
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
  .form-select:disabled {
    background: var(--gray-100);
    cursor: not-allowed;
  }

  .form-help {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 6px;
  }

  .info-box {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
    padding: 16px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
  }

  .info-box-title {
    font-weight: 600;
    color: #1e40af;
    margin-bottom: 8px;
  }

  .info-box-text {
    font-size: 14px;
    color: #1e3a8a;
  }

  .shops-list {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
  }

  .shop-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    margin-bottom: 8px;
  }

  .shop-name {
    font-weight: 600;
    color: var(--dark);
  }

  .shop-status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .shop-status.active { background: #dcfce7; color: #166534; }
  .shop-status.inactive { background: var(--gray-200); color: var(--gray-700); }
  .shop-status.pending { background: #fef3c7; color: #92400e; }

  .form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 24px;
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

  .btn-danger {
    background: #ef4444;
    color: white;
  }

  .btn-danger:hover {
    background: #dc2626;
  }

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

<div class="page-header">
  <a href="<?= url('admin/sellers') ?>" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Sellers
  </a>
  <h1>Edit Seller</h1>
</div>

<div class="form-card">
  <form method="POST" action="<?= url('admin/sellers/update') ?>">
    <?= csrfField() ?>
    <input type="hidden" name="seller_id" value="<?= $seller['id'] ?>">

    <!-- Info Box -->
    <div class="info-box">
      <div class="info-box-title">Seller ID: <?= $seller['id'] ?></div>
      <div class="info-box-text">
        Member since <?= formatDate($seller['created_at'], 'F j, Y') ?>
      </div>
    </div>

    <div class="form-grid">
      <!-- First Name -->
      <div class="form-group">
        <label class="form-label required">First Name</label>
        <input 
          type="text" 
          name="first_name" 
          value="<?= htmlspecialchars($seller['first_name']) ?>" 
          class="form-input" 
          required
        >
      </div>

      <!-- Last Name -->
      <div class="form-group">
        <label class="form-label required">Last Name</label>
        <input 
          type="text" 
          name="last_name" 
          value="<?= htmlspecialchars($seller['last_name']) ?>" 
          class="form-input" 
          required
        >
      </div>

      <!-- Email -->
      <div class="form-group">
        <label class="form-label required">Email</label>
        <input 
          type="email" 
          name="email" 
          value="<?= htmlspecialchars($seller['email']) ?>" 
          class="form-input" 
          required
        >
      </div>

      <!-- Phone -->
      <div class="form-group">
        <label class="form-label">Phone</label>
        <input 
          type="tel" 
          name="phone" 
          value="<?= htmlspecialchars($seller['phone'] ?? '') ?>" 
          class="form-input"
        >
      </div>

      <!-- Status -->
      <div class="form-group">
        <label class="form-label required">Account Status</label>
        <select name="status" class="form-select" required>
          <option value="active" <?= $seller['status'] === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= $seller['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          <option value="suspended" <?= $seller['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
        </select>
        <span class="form-help">Suspending will also deactivate all their shops</span>
      </div>

      <!-- Stats (Read-only) -->
      <div class="form-group">
        <label class="form-label">Statistics</label>
        <input 
          type="text" 
          value="<?= $seller['shop_count'] ?? 0 ?> Shops, <?= $seller['product_count'] ?? 0 ?> Products" 
          class="form-input" 
          disabled
          readonly
        >
      </div>

      <!-- Password Reset (Optional) -->
      <div class="form-group full-width">
        <label class="form-label">Reset Password</label>
        <input 
          type="password" 
          name="new_password" 
          class="form-input"
          minlength="8"
          placeholder="Leave blank to keep current password"
        >
        <span class="form-help">Enter a new password only if you want to reset it. Minimum 8 characters.</span>
      </div>
    </div>

    <!-- Shops List -->
    <?php if (!empty($seller['shops'])): ?>
      <div class="shops-list">
        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">Seller's Shops</h3>
        <?php foreach ($seller['shops'] as $shop): ?>
          <div class="shop-item">
            <div>
              <div class="shop-name"><?= htmlspecialchars($shop['name']) ?></div>
              <div style="font-size: 12px; color: var(--gray-500); margin-top: 4px;">
                <?= $shop['product_count'] ?? 0 ?> products
              </div>
            </div>
            <div>
              <?php
              $shopStatus = 'inactive';
              if ($shop['is_approved'] && $shop['is_active']) {
                $shopStatus = 'active';
              } elseif (!$shop['is_approved']) {
                $shopStatus = 'pending';
              }
              ?>
              <span class="shop-status <?= $shopStatus ?>">
                <?= ucfirst($shopStatus) ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Form Actions -->
    <div class="form-actions">
      <a href="<?= url('admin/sellers') ?>" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
      </a>
      
      <?php if ($seller['status'] === 'active'): ?>
        <button 
          type="button" 
          class="btn btn-danger"
          onclick="if(confirm('Suspend this seller? This will deactivate all their shops.')) { 
            document.getElementById('suspendForm').submit(); 
          }"
        >
          <i class="fas fa-ban"></i> Suspend Seller
        </button>
      <?php endif; ?>
      
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Save Changes
      </button>
    </div>
  </form>

  <!-- Separate Suspend Form -->
  <?php if ($seller['status'] === 'active'): ?>
    <form id="suspendForm" method="POST" action="<?= url('admin/sellers/suspend') ?>" style="display: none;">
      <?= csrfField() ?>
      <input type="hidden" name="seller_id" value="<?= $seller['id'] ?>">
    </form>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>