<?php
/**
 * Admin Create Vendor - OCSAPP
 */
?>
<?php ob_start(); ?>

<style>
.create-vendor-wrapper {
    max-width: 800px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 32px;
}

.page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 8px;
}

.page-header p {
    color: #6b7280;
    margin: 0;
}

.form-card {
    background: white;
    border-radius: 12px;
    padding: 32px;
    border: 1px solid #e5e7eb;
}

.form-section {
    margin-bottom: 32px;
}

.form-section:last-child {
    margin-bottom: 0;
}

.form-section-title {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e7eb;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-grid-full {
    grid-column: 1 / -1;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    margin-bottom: 8px;
}

.form-group label .required {
    color: #ef4444;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="tel"],
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
}

.form-help {
    font-size: 13px;
    color: #6b7280;
    margin-top: 6px;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    font-size: 14px;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-700);
}

.btn-secondary {
    background: #f3f4f6;
    color: #4b5563;
}

.btn-secondary:hover {
    background: #e5e7eb;
}
</style>

<div class="create-vendor-wrapper">
    <div class="page-header">
        <h1><i class="fa-solid fa-truck-field"></i> Create New Vendor</h1>
        <p>Add a new vendor to the system and send them login credentials</p>
    </div>

    <form action="<?= url('admin/vendors/store') ?>" method="POST" class="form-card">
        <?= csrfField() ?>

        <!-- Company Information -->
        <div class="form-section">
            <h3 class="form-section-title">Company Information</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Company Name <span class="required">*</span></label>
                    <input type="text" name="company_name" required>
                </div>

                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" required>
                    <div class="form-help">Login credentials will be sent to this email</div>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone">
                </div>

                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" name="contact_person" placeholder="John Doe">
                </div>

                <div class="form-group">
                    <label>Business Number</label>
                    <input type="text" name="business_number" placeholder="123456789RC0001">
                    <div class="form-help">Canadian business registration number</div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="form-section">
            <h3 class="form-section-title">Address Information</h3>

            <div class="form-grid">
                <div class="form-group form-grid-full">
                    <label>Street Address</label>
                    <input type="text" name="address" placeholder="123 Main St">
                </div>

                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" placeholder="Toronto">
                </div>

                <div class="form-group">
                    <label>Province</label>
                    <select name="province">
                        <option value="">Select Province</option>
                        <option value="ON">Ontario</option>
                        <option value="QC">Quebec</option>
                        <option value="BC">British Columbia</option>
                        <option value="AB">Alberta</option>
                        <option value="MB">Manitoba</option>
                        <option value="SK">Saskatchewan</option>
                        <option value="NS">Nova Scotia</option>
                        <option value="NB">New Brunswick</option>
                        <option value="NL">Newfoundland and Labrador</option>
                        <option value="PE">Prince Edward Island</option>
                        <option value="NT">Northwest Territories</option>
                        <option value="YT">Yukon</option>
                        <option value="NU">Nunavut</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" placeholder="M5H 2N2">
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="form-section">
            <h3 class="form-section-title">Vendor Settings</h3>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="auto_approve" name="auto_approve_orders" value="1">
                    <label for="auto_approve" style="margin: 0;">Auto-approve all orders from this vendor</label>
                </div>
                <div class="form-help" style="margin-left: 26px;">When enabled, orders from this vendor won't require manual approval</div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="<?= url('admin/vendors') ?>" class="btn btn-secondary">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-check"></i> Create Vendor
            </button>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>
