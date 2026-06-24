<?php
$pageTitle = 'Add Supplier';
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
  <h1 class="page-title">Add New Supplier</h1>
  <div class="breadcrumb">
    <a href="<?= url('admin/suppliers') ?>">Suppliers</a>
    <span>/</span>
    <span>Add New</span>
  </div>
</div>

<form method="POST" action="<?= url('admin/suppliers/store') ?>" class="form-container">
  <?= csrfField() ?>

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
            value="<?= old('company_name') ?>"
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
            value="<?= old('name') ?>"
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
              value="<?= old('email') ?>"
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
              value="<?= old('phone') ?>"
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
            value="<?= old('contact_person') ?>"
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
            value="<?= old('address') ?>"
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
              value="<?= old('city') ?>"
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
              <option value="AB" <?= old('province') === 'AB' ? 'selected' : '' ?>>Alberta</option>
              <option value="BC" <?= old('province') === 'BC' ? 'selected' : '' ?>>British Columbia</option>
              <option value="MB" <?= old('province') === 'MB' ? 'selected' : '' ?>>Manitoba</option>
              <option value="NB" <?= old('province') === 'NB' ? 'selected' : '' ?>>New Brunswick</option>
              <option value="NL" <?= old('province') === 'NL' ? 'selected' : '' ?>>Newfoundland and Labrador</option>
              <option value="NS" <?= old('province') === 'NS' ? 'selected' : '' ?>>Nova Scotia</option>
              <option value="ON" <?= old('province', 'ON') === 'ON' ? 'selected' : '' ?>>Ontario</option>
              <option value="PE" <?= old('province') === 'PE' ? 'selected' : '' ?>>Prince Edward Island</option>
              <option value="QC" <?= old('province') === 'QC' ? 'selected' : '' ?>>Quebec</option>
              <option value="SK" <?= old('province') === 'SK' ? 'selected' : '' ?>>Saskatchewan</option>
              <option value="NT" <?= old('province') === 'NT' ? 'selected' : '' ?>>Northwest Territories</option>
              <option value="NU" <?= old('province') === 'NU' ? 'selected' : '' ?>>Nunavut</option>
              <option value="YT" <?= old('province') === 'YT' ? 'selected' : '' ?>>Yukon</option>
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
              value="<?= old('postal_code') ?>"
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
              value="<?= old('country', 'Canada') ?>"
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

        <div class="form-group">
          <label for="payment_terms" class="form-label">
            Payment Terms
          </label>
          <select id="payment_terms" name="payment_terms" class="form-select">
            <option value="Net 30" <?= old('payment_terms', 'Net 30') === 'Net 30' ? 'selected' : '' ?>>Net 30</option>
            <option value="Net 60" <?= old('payment_terms') === 'Net 60' ? 'selected' : '' ?>>Net 60</option>
            <option value="Net 90" <?= old('payment_terms') === 'Net 90' ? 'selected' : '' ?>>Net 90</option>
            <option value="Due on Receipt" <?= old('payment_terms') === 'Due on Receipt' ? 'selected' : '' ?>>Due on Receipt</option>
            <option value="50% Deposit" <?= old('payment_terms') === '50% Deposit' ? 'selected' : '' ?>>50% Deposit</option>
            <option value="COD" <?= old('payment_terms') === 'COD' ? 'selected' : '' ?>>COD (Cash on Delivery)</option>
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
            value="<?= old('tax_number') ?>"
            class="form-input"
            placeholder="123456789RT0001"
          >
        </div>

        <div class="form-group">
          <label for="status" class="form-label">
            Status
          </label>
          <select id="status" name="status" class="form-select">
            <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
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
          ><?= old('notes') ?></textarea>
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
      <i class="fas fa-save"></i> Add Supplier
    </button>
  </div>
</form>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
