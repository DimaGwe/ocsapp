<?php
/**
 * OCS Admin Edit User
 * File: app/Views/admin/users/edit.php
 */

$pageTitle = 'Edit User';
$currentPage = 'users';

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

  .form-help {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 6px;
  }

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

  .roles-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 4px;
  }

  .role-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: var(--dark);
    background: white;
    transition: all var(--transition-base);
    user-select: none;
  }

  .role-checkbox:has(input:checked) {
    border-color: var(--primary);
    background: rgba(0, 178, 7, 0.06);
    color: var(--primary);
  }

  .role-checkbox.disabled {
    opacity: 0.55;
    cursor: not-allowed;
    background: var(--gray-100);
  }

  .role-checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--primary);
    cursor: pointer;
  }

  .role-checkbox.disabled input[type="checkbox"] {
    cursor: not-allowed;
  }

  .role-checkbox small {
    font-size: 11px;
    color: var(--gray-500);
    margin-left: 2px;
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
  <a href="<?= url('admin/users') ?>" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Users
  </a>
  <h1>Edit User</h1>
</div>

<div class="form-card">
  <form method="POST" action="<?= url('admin/users/update') ?>">
    <?= csrfField() ?>
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

    <div class="form-grid">
      <!-- First Name -->
      <div class="form-group">
        <label class="form-label required">First Name</label>
        <input 
          type="text" 
          name="first_name" 
          value="<?= htmlspecialchars($user['first_name']) ?>" 
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
          value="<?= htmlspecialchars($user['last_name']) ?>" 
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
          value="<?= htmlspecialchars($user['email']) ?>" 
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
          value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
          class="form-input"
        >
      </div>

      <!-- Roles -->
      <div class="form-group full-width">
        <label class="form-label required">Roles</label>
        <div class="roles-grid">
          <?php
          $adminTierRoles = ['super_admin', 'admin', 'admin_staff'];

          foreach ($roles ?? [] as $roleItem):
            $isAdminTier = in_array($roleItem['name'], $adminTierRoles);
            $isChecked   = in_array($roleItem['name'], $userRoles ?? []);
            $isDisabled  = $isAdminTier && empty($canAssignAdminRoles);
            $labelClass  = 'role-checkbox' . ($isDisabled ? ' disabled' : '');
          ?>
            <label class="<?= $labelClass ?>">
              <input
                type="checkbox"
                name="roles[]"
                value="<?= htmlspecialchars($roleItem['name']) ?>"
                <?= $isChecked ? 'checked' : '' ?>
                <?= $isDisabled ? 'disabled' : '' ?>
              >
              <?= htmlspecialchars($roleItem['display_name'] ?: ucfirst(str_replace('_', ' ', $roleItem['name']))) ?>
              <?php if ($isAdminTier): ?><small>(Admin)</small><?php endif; ?>
            </label>
          <?php endforeach; ?>
        </div>
        <?php if (empty($canAssignAdminRoles)): ?>
          <span class="form-help">Admin tier roles can only be assigned by Super Administrators</span>
        <?php endif; ?>
      </div>

      <!-- Status -->
      <div class="form-group">
        <label class="form-label required">Status</label>
        <select name="status" class="form-select" required>
          <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
          <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
        </select>
      </div>

      <!-- Department (admin team members) -->
      <div class="form-group">
        <label class="form-label">Department</label>
        <select name="department" class="form-select">
          <option value="">-- None --</option>
          <option value="management"  <?= ($user['department'] ?? '') === 'management'  ? 'selected' : '' ?>>Management</option>
          <option value="ops"         <?= ($user['department'] ?? '') === 'ops'         ? 'selected' : '' ?>>Operations</option>
          <option value="finance"     <?= ($user['department'] ?? '') === 'finance'     ? 'selected' : '' ?>>Finance</option>
          <option value="support"     <?= ($user['department'] ?? '') === 'support'     ? 'selected' : '' ?>>Support</option>
          <option value="logistics"   <?= ($user['department'] ?? '') === 'logistics'   ? 'selected' : '' ?>>Logistics</option>
          <option value="tech"        <?= ($user['department'] ?? '') === 'tech'        ? 'selected' : '' ?>>Technology</option>
        </select>
        <span class="form-help">For admin team members only.</span>
      </div>

      <!-- Password (Optional) -->
      <div class="form-group full-width">
        <label class="form-label">New Password</label>
        <div style="position:relative;">
          <input
            type="password"
            id="password-field"
            name="password"
            class="form-input"
            minlength="8"
            style="padding-right:2.5rem;"
          >
          <button type="button" onclick="
            var f=document.getElementById('password-field');
            var i=this.querySelector('i');
            if(f.type==='password'){f.type='text';i.classList.replace('fa-eye','fa-eye-slash');}
            else{f.type='password';i.classList.replace('fa-eye-slash','fa-eye');}
          " style="position:absolute;right:0.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6b7280;padding:0.25rem;" title="Toggle password visibility">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
        <span class="form-help">Leave blank to keep current password. Minimum 8 characters.</span>
      </div>
    </div>

    <div class="form-actions">
      <a href="<?= url('admin/users') ?>" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Save Changes
      </button>
    </div>
  </form>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
