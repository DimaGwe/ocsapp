<?php
$pageTitle = 'Edit Supplier';
$currentPage = 'suppliers';
ob_start();
?>

<style>
  .page-header {
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .breadcrumb {
    display: flex;
    gap: 8px;
    font-size: 14px;
    color: var(--gray-600);
  }

  .breadcrumb a {
    color: var(--primary);
    text-decoration: none;
  }

  .form-container {
    max-width: 1200px;
  }

  .form-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
  }

  .card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
  }

  .card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 20px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
  }

  .required {
    color: #ef4444;
  }

  .form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    transition: all 0.2s;
  }

  .form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  }

  .form-hint {
    font-size: 13px;
    color: var(--gray-500);
    margin-top: 6px;
  }

  .grid-cols-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }

  .form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding: 24px;
    background: white;
    border-top: 1px solid var(--border);
    position: sticky;
    bottom: 0;
  }

  .btn {
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
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
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .btn-secondary:hover {
    background: var(--gray-300);
  }

  @media (max-width: 768px) {
    .form-grid {
      grid-template-columns: 1fr;
    }

    .grid-cols-2 {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="page-header">
  <h1 class="page-title">Edit Supplier</h1>
  <div class="breadcrumb">
    <a href="<?= url('admin/suppliers') ?>">Suppliers</a>
    <span>/</span>
    <span>Edit</span>
  </div>
</div>

<?php if (($supplier['status'] ?? '') === 'pending_verification'): ?>
  <div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:12px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;align-items:center;gap:10px;">
      <i class="fas fa-clock" style="color:#d97706;font-size:20px;"></i>
      <div>
        <strong style="color:#92400e;">Pending Verification</strong>
        <span style="color:#92400e;font-size:13px;"> — This supplier is awaiting document review and approval.</span>
      </div>
    </div>
    <?php if (!empty($supplier['lead_id'])): ?>
      <a href="<?= url('admin/leads/view?id=' . $supplier['lead_id']) ?>" style="background:#d97706;color:white;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
        <i class="fas fa-clipboard-check"></i> Review Application
      </a>
    <?php endif; ?>
  </div>
<?php endif; ?>

<form method="POST" action="<?= url('admin/suppliers/update') ?>" class="form-container">
  <?= csrfField() ?>
  <input type="hidden" name="id" value="<?= $supplier['id'] ?>">

  <div class="form-grid">
    <!-- Main Column -->
    <div>
      <!-- Company Information -->
      <div class="card">
        <h3 class="card-title">Company Information</h3>

        <div class="form-group">
          <label for="company_name" class="form-label">
            Company Name <span class="required">*</span>
          </label>
          <input
            type="text"
            id="company_name"
            name="company_name"
            value="<?= htmlspecialchars($supplier['company_name'] ?? '') ?>"
            class="form-input"
            required
            placeholder="ABC Wholesale Inc."
          >
        </div>

        <div class="form-group">
          <label for="name" class="form-label">
            Display Name <span class="required">*</span>
          </label>
          <input
            type="text"
            id="name"
            name="name"
            value="<?= htmlspecialchars($supplier['name'] ?? '') ?>"
            class="form-input"
            required
            placeholder="ABC Wholesale"
          >
          <p class="form-hint">Short name for internal use</p>
        </div>

        <div class="grid-cols-2">
          <div class="form-group">
            <label for="email" class="form-label">
              Email
            </label>
            <input
              type="email"
              id="email"
              name="email"
              value="<?= htmlspecialchars($supplier['email'] ?? '') ?>"
              class="form-input"
              placeholder="orders@supplier.com"
            >
          </div>

          <div class="form-group">
            <label for="phone" class="form-label">
              Phone
            </label>
            <input
              type="tel"
              id="phone"
              name="phone"
              value="<?= htmlspecialchars($supplier['phone'] ?? '') ?>"
              class="form-input"
              pattern="[\d\s\-\(\)\+]{10,}"
              title="Enter a valid phone number (at least 10 digits)"
              placeholder="(555) 123-4567"
            >
          </div>
        </div>

        <div class="form-group">
          <label for="contact_person" class="form-label">
            Primary Contact Person
          </label>
          <input
            type="text"
            id="contact_person"
            name="contact_person"
            value="<?= htmlspecialchars($supplier['contact_person'] ?? '') ?>"
            class="form-input"
            placeholder="John Doe"
          >
        </div>
      </div>

      <!-- Address Information -->
      <div class="card">
        <h3 class="card-title">Address</h3>

        <div class="form-group">
          <label for="address" class="form-label">
            Street Address
          </label>
          <input
            type="text"
            id="address"
            name="address"
            value="<?= htmlspecialchars($supplier['address'] ?? '') ?>"
            class="form-input"
            placeholder="123 Main Street"
          >
        </div>

        <div class="grid-cols-2">
          <div class="form-group">
            <label for="city" class="form-label">
              City
            </label>
            <input
              type="text"
              id="city"
              name="city"
              value="<?= htmlspecialchars($supplier['city'] ?? '') ?>"
              class="form-input"
              placeholder="Toronto"
            >
          </div>

          <div class="form-group">
            <label for="province" class="form-label">
              Province
            </label>
            <select id="province" name="province" class="form-select">
              <option value="">Select Province</option>
              <option value="AB" <?= ($supplier['province'] ?? '') === 'AB' ? 'selected' : '' ?>>Alberta</option>
              <option value="BC" <?= ($supplier['province'] ?? '') === 'BC' ? 'selected' : '' ?>>British Columbia</option>
              <option value="MB" <?= ($supplier['province'] ?? '') === 'MB' ? 'selected' : '' ?>>Manitoba</option>
              <option value="NB" <?= ($supplier['province'] ?? '') === 'NB' ? 'selected' : '' ?>>New Brunswick</option>
              <option value="NL" <?= ($supplier['province'] ?? '') === 'NL' ? 'selected' : '' ?>>Newfoundland and Labrador</option>
              <option value="NS" <?= ($supplier['province'] ?? '') === 'NS' ? 'selected' : '' ?>>Nova Scotia</option>
              <option value="ON" <?= ($supplier['province'] ?? 'ON') === 'ON' ? 'selected' : '' ?>>Ontario</option>
              <option value="PE" <?= ($supplier['province'] ?? '') === 'PE' ? 'selected' : '' ?>>Prince Edward Island</option>
              <option value="QC" <?= ($supplier['province'] ?? '') === 'QC' ? 'selected' : '' ?>>Quebec</option>
              <option value="SK" <?= ($supplier['province'] ?? '') === 'SK' ? 'selected' : '' ?>>Saskatchewan</option>
              <option value="NT" <?= ($supplier['province'] ?? '') === 'NT' ? 'selected' : '' ?>>Northwest Territories</option>
              <option value="NU" <?= ($supplier['province'] ?? '') === 'NU' ? 'selected' : '' ?>>Nunavut</option>
              <option value="YT" <?= ($supplier['province'] ?? '') === 'YT' ? 'selected' : '' ?>>Yukon</option>
            </select>
          </div>
        </div>

        <div class="grid-cols-2">
          <div class="form-group">
            <label for="postal_code" class="form-label">
              Postal Code
            </label>
            <input
              type="text"
              id="postal_code"
              name="postal_code"
              value="<?= htmlspecialchars($supplier['postal_code'] ?? '') ?>"
              class="form-input"
              placeholder="A1B 2C3"
              pattern="[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d"
              title="Canadian postal code (e.g. A1B 2C3)"
            >
          </div>

          <div class="form-group">
            <label for="country" class="form-label">
              Country
            </label>
            <input
              type="text"
              id="country"
              name="country"
              value="<?= htmlspecialchars($supplier['country'] ?? 'Canada') ?>"
              class="form-input"
            >
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar Column -->
    <div>
      <!-- Business Details -->
      <div class="card">
        <h3 class="card-title">Business Details</h3>

        <?php
        $editPkg = $supplier['subscription_package'] ?? 'Essential';
        $editCommission = $supplier['commission_rate'] ?? '12.00';
        ?>
        <div class="form-group">
          <label for="subscription_package" class="form-label">Subscription Package</label>
          <select id="subscription_package" name="subscription_package" class="form-select" onchange="syncCommission(this.value)">
            <option value="Essential" <?= $editPkg === 'Essential' ? 'selected' : '' ?>>Essential</option>
            <option value="Experience" <?= $editPkg === 'Experience' ? 'selected' : '' ?>>Experience</option>
            <option value="Prestige" <?= $editPkg === 'Prestige' ? 'selected' : '' ?>>Prestige</option>
            <option value="Enterprise" <?= $editPkg === 'Enterprise' ? 'selected' : '' ?>>Enterprise</option>
          </select>
          <p class="form-hint">The supplier's subscription tier</p>
        </div>

        <div class="form-group">
          <label for="commission_rate" class="form-label">Commission Rate (%)</label>
          <input type="number" id="commission_rate" name="commission_rate" step="0.01" min="0" max="100"
            value="<?= htmlspecialchars($editCommission) ?>" class="form-input">
          <p class="form-hint">Auto-filled when package changes; override if needed</p>
        </div>

        <div class="form-group">
          <label for="payment_terms" class="form-label">
            Payment Terms
          </label>
          <select id="payment_terms" name="payment_terms" class="form-select">
            <option value="Net 30" <?= ($supplier['payment_terms'] ?? 'Net 30') === 'Net 30' ? 'selected' : '' ?>>Net 30</option>
            <option value="Net 60" <?= ($supplier['payment_terms'] ?? '') === 'Net 60' ? 'selected' : '' ?>>Net 60</option>
            <option value="Net 90" <?= ($supplier['payment_terms'] ?? '') === 'Net 90' ? 'selected' : '' ?>>Net 90</option>
            <option value="Due on Receipt" <?= ($supplier['payment_terms'] ?? '') === 'Due on Receipt' ? 'selected' : '' ?>>Due on Receipt</option>
            <option value="50% Deposit" <?= ($supplier['payment_terms'] ?? '') === '50% Deposit' ? 'selected' : '' ?>>50% Deposit</option>
            <option value="COD" <?= ($supplier['payment_terms'] ?? '') === 'COD' ? 'selected' : '' ?>>COD (Cash on Delivery)</option>
          </select>
          <p class="form-hint">Default payment terms for this supplier</p>
        </div>

        <div class="form-group">
          <label for="tax_number" class="form-label">
            Tax Number / GST/HST #
          </label>
          <input
            type="text"
            id="tax_number"
            name="tax_number"
            value="<?= htmlspecialchars($supplier['tax_number'] ?? '') ?>"
            class="form-input"
            placeholder="123456789RT0001"
          >
        </div>

        <div class="form-group">
          <label for="status" class="form-label">
            Status
          </label>
          <select id="status" name="status" class="form-select">
            <option value="active" <?= ($supplier['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= ($supplier['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="suspended" <?= ($supplier['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            <option value="pending_verification" <?= ($supplier['status'] ?? '') === 'pending_verification' ? 'selected' : '' ?>>Pending Verification</option>
          </select>
        </div>
      </div>

      <!-- Account Security -->
      <div class="card">
        <h3 class="card-title">Account Security</h3>

        <div class="form-group">
          <label for="password" class="form-label">
            New Password
          </label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-input"
            minlength="8"
            placeholder="Enter new password"
          >
          <p class="form-hint">Leave blank to keep current password. Minimum 8 characters.</p>
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="form-label">
            Confirm Password
          </label>
          <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-input"
            minlength="8"
            placeholder="Confirm new password"
          >
        </div>

        <?php if (!empty($supplier['password_changed_at'])): ?>
          <p class="form-hint" style="margin-top: -10px;">
            <i class="fas fa-clock" style="color:var(--gray-400);"></i>
            Password last changed: <?= date('M j, Y g:i A', strtotime($supplier['password_changed_at'])) ?>
          </p>
        <?php endif; ?>
      </div>

      <!-- Notes -->
      <div class="card">
        <h3 class="card-title">Notes</h3>

        <div class="form-group">
          <label for="notes" class="form-label">
            Internal Notes
          </label>
          <textarea
            id="notes"
            name="notes"
            rows="6"
            class="form-textarea"
            placeholder="Add any additional notes about this supplier..."
          ><?= htmlspecialchars($supplier['notes'] ?? '') ?></textarea>
          <p class="form-hint">These notes are for internal use only</p>
        </div>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <a href="<?= url('admin/suppliers') ?>" class="btn btn-secondary">
      Cancel
    </a>
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-save"></i> Update Supplier
    </button>
  </div>
</form>

<!-- Banking / Payment Info (read-only) -->
<?php
$hasBanking = !empty($supplier['payment_preference'])
    || !empty($supplier['bank_name'])
    || !empty($supplier['interac_email']);
?>
<?php if ($hasBanking): ?>
<div class="card" style="max-width:1200px;margin-top:24px;">
  <h3 class="card-title" style="display:flex;align-items:center;gap:8px;">
    <i class="fas fa-university" style="color:#00b207;"></i> Banking / Payment Information
    <span style="font-size:12px;color:#6b7280;font-weight:400;">(read-only — supplier managed)</span>
  </h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-top:16px;">
    <?php $pref = $supplier['payment_preference'] ?? ''; ?>
    <div>
      <div style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:4px;">Payment Preference</div>
      <div style="font-size:15px;color:#111827;font-weight:600;">
        <?= $pref === 'eft' ? 'EFT / Direct Deposit' : ($pref === 'interac' ? 'Interac e-Transfer' : ($pref === 'cheque' ? 'Cheque' : '—')) ?>
      </div>
    </div>
    <?php if ($pref === 'eft'): ?>
      <div><div style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:4px;">Bank Name</div><div><?= htmlspecialchars($supplier['bank_name'] ?? '—') ?></div></div>
      <div><div style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:4px;">Account Holder</div><div><?= htmlspecialchars($supplier['bank_account_holder'] ?? '—') ?></div></div>
      <div><div style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:4px;">Transit #</div><div><?= htmlspecialchars($supplier['bank_transit'] ?? '—') ?></div></div>
      <div><div style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:4px;">Institution #</div><div><?= htmlspecialchars($supplier['bank_institution'] ?? '—') ?></div></div>
      <div><div style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:4px;">Account #</div><div>••••<?= substr(htmlspecialchars($supplier['bank_account'] ?? ''), -4) ?></div></div>
      <div><div style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:4px;">Account Type</div><div><?= ucfirst($supplier['bank_account_type'] ?? '—') ?></div></div>
    <?php elseif ($pref === 'interac'): ?>
      <div><div style="font-size:12px;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:4px;">e-Transfer Email</div><div><?= htmlspecialchars($supplier['interac_email'] ?? '—') ?></div></div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- Activity Log -->
<?php if (!empty($auditLog)): ?>
<div class="card" style="max-width:1200px;margin-top:24px;">
  <h3 class="card-title" style="display:flex;align-items:center;gap:8px;">
    <i class="fas fa-history" style="color:var(--gray-500);"></i> Activity Log
  </h3>
  <table style="width:100%;border-collapse:collapse;">
    <thead>
      <tr>
        <th style="padding:10px 14px;text-align:left;font-size:12px;font-weight:600;color:var(--gray-600);text-transform:uppercase;background:var(--gray-50);border-bottom:2px solid var(--gray-200);">Date</th>
        <th style="padding:10px 14px;text-align:left;font-size:12px;font-weight:600;color:var(--gray-600);text-transform:uppercase;background:var(--gray-50);border-bottom:2px solid var(--gray-200);">Action</th>
        <th style="padding:10px 14px;text-align:left;font-size:12px;font-weight:600;color:var(--gray-600);text-transform:uppercase;background:var(--gray-50);border-bottom:2px solid var(--gray-200);">Details</th>
        <th style="padding:10px 14px;text-align:left;font-size:12px;font-weight:600;color:var(--gray-600);text-transform:uppercase;background:var(--gray-50);border-bottom:2px solid var(--gray-200);">By</th>
        <th style="padding:10px 14px;text-align:left;font-size:12px;font-weight:600;color:var(--gray-600);text-transform:uppercase;background:var(--gray-50);border-bottom:2px solid var(--gray-200);">IP</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($auditLog as $log): ?>
      <tr>
        <td style="padding:10px 14px;font-size:13px;color:var(--gray-600);border-bottom:1px solid var(--gray-100);white-space:nowrap;"><?= date('M j, Y g:i A', strtotime($log['created_at'])) ?></td>
        <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid var(--gray-100);">
          <span style="padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;text-transform:uppercase;
            <?php
              $colors = ['login' => 'background:#dbeafe;color:#1d4ed8;', 'updated' => 'background:#fef3c7;color:#92400e;', 'status_changed' => 'background:#fce7f3;color:#9d174d;', 'password_reset' => 'background:#fee2e2;color:#991b1b;', 'created' => 'background:#d1fae5;color:#065f46;'];
              echo $colors[$log['action']] ?? 'background:#f3f4f6;color:#6b7280;';
            ?>"><?= htmlspecialchars(str_replace('_', ' ', $log['action'])) ?></span>
        </td>
        <td style="padding:10px 14px;font-size:13px;color:var(--gray-700);border-bottom:1px solid var(--gray-100);"><?= htmlspecialchars($log['details'] ?? '') ?></td>
        <td style="padding:10px 14px;font-size:13px;color:var(--gray-600);border-bottom:1px solid var(--gray-100);"><?= htmlspecialchars($log['performer_name'] ?? 'System') ?></td>
        <td style="padding:10px 14px;font-size:12px;color:var(--gray-400);border-bottom:1px solid var(--gray-100);font-family:monospace;"><?= htmlspecialchars($log['ip_address'] ?? '') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<script>
const commissionDefaults = { Essential: 12, Experience: 10, Prestige: 8, Enterprise: 6 };
function syncCommission(pkg) {
  const field = document.getElementById('commission_rate');
  if (field && commissionDefaults[pkg] !== undefined) {
    field.value = commissionDefaults[pkg].toFixed(2);
  }
}
</script>

<?php
// Messages thread
try {
    $supplierId      = (int) $supplier['id'];
    $threadMessages  = \App\Controllers\SupplierMessagesController::getMessages($supplierId, 50);
    $supplierName    = $supplier['company_name'] ?: ($supplier['first_name'] . ' ' . $supplier['last_name']);
    require __DIR__ . '/messages-thread.php';
} catch (\Throwable $e) {
    // table may not exist yet
}
?>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
