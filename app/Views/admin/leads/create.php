<?php
$currentPage = 'leads';
$pageTitle = 'Add Lead';
ob_start();
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.form-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.form-card h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-card h3 i {
    color: #00b207;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.form-group {
    margin-bottom: 0;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #333;
    margin-bottom: 6px;
}

.form-label .required {
    color: #dc2626;
}

.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.2s;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: #00b207;
}

.form-textarea {
    min-height: 100px;
    resize: vertical;
}

.tags-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.tag-checkbox {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s;
}

.tag-checkbox:hover {
    border-color: #00b207;
}

.tag-checkbox input {
    display: none;
}

.tag-checkbox input:checked + .tag-label {
    font-weight: 600;
}

.tag-checkbox:has(input:checked) {
    background: #d1fae5;
    border-color: #00b207;
}

.tag-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: #00b207;
    color: white;
}

.btn-primary:hover {
    background: #009906;
}

.btn-secondary {
    background: #f3f4f6;
    color: #666;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #f3f4f6;
}
</style>

<div class="page-header">
    <h1 style="font-size: 24px; font-weight: 600;">Add New Lead</h1>
    <a href="<?= url('admin/leads') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Leads
    </a>
</div>

<form method="POST" action="<?= url('admin/leads/store') ?>">
    <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">

    <!-- Contact Information -->
    <div class="form-card">
        <h3><i class="fas fa-user"></i> Contact Information</h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">First Name <span class="required">*</span></label>
                <input type="text" name="first_name" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="tel" name="phone" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Company</label>
                <input type="text" name="company_name" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Job Title</label>
                <input type="text" name="job_title" class="form-input">
            </div>
        </div>
    </div>

    <!-- Lead Details -->
    <div class="form-card">
        <h3><i class="fas fa-info-circle"></i> Lead Details</h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Source</label>
                <select name="source" class="form-select">
                    <option value="manual">Manual Entry</option>
                    <option value="website">Website</option>
                    <option value="referral">Referral</option>
                    <option value="social_media">Social Media</option>
                    <option value="event">Event</option>
                    <option value="cold_call">Cold Call</option>
                    <option value="email_campaign">Email Campaign</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Source Details</label>
                <input type="text" name="source_details" class="form-input" placeholder="e.g., Referred by John">
            </div>
            <div class="form-group">
                <label class="form-label">Interest Type</label>
                <select name="interest_type" class="form-select">
                    <option value="seller">Seller (wants to sell on OCS)</option>
                    <option value="buyer">Buyer (wants to purchase)</option>
                    <option value="business">Business Account</option>
                    <option value="supplier">Supplier</option>
                    <option value="delivery_partner">Delivery Partner</option>
                    <option value="investor">Investor</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Estimated Value ($)</label>
                <input type="number" name="estimated_value" class="form-input" step="0.01" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="qualified">Qualified</option>
                    <option value="proposal">Proposal</option>
                    <option value="negotiation">Negotiation</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div class="form-group full-width">
                <label class="form-label">Interest Details</label>
                <textarea name="interest_details" class="form-textarea" placeholder="What are they interested in?"></textarea>
            </div>
        </div>
    </div>

    <!-- Location -->
    <div class="form-card">
        <h3><i class="fas fa-map-marker-alt"></i> Location</h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Province</label>
                <select name="province" class="form-select">
                    <option value="">Select Province</option>
                    <option value="QC">Quebec</option>
                    <option value="ON">Ontario</option>
                    <option value="BC">British Columbia</option>
                    <option value="AB">Alberta</option>
                    <option value="MB">Manitoba</option>
                    <option value="SK">Saskatchewan</option>
                    <option value="NS">Nova Scotia</option>
                    <option value="NB">New Brunswick</option>
                    <option value="NL">Newfoundland and Labrador</option>
                    <option value="PE">Prince Edward Island</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Country</label>
                <input type="text" name="country" class="form-input" value="Canada">
            </div>
        </div>
    </div>

    <!-- Assignment & Follow-up -->
    <div class="form-card">
        <h3><i class="fas fa-tasks"></i> Assignment & Follow-up</h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Assign To</label>
                <select name="assigned_to" class="form-select">
                    <option value="">Unassigned</option>
                    <?php foreach ($admins as $admin): ?>
                        <option value="<?= $admin['id'] ?>"><?= htmlspecialchars($admin['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Next Follow-up</label>
                <input type="date" name="next_follow_up" class="form-input" min="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group full-width">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-textarea" placeholder="Additional notes..."></textarea>
            </div>
        </div>
    </div>

    <!-- Tags -->
    <?php if (!empty($tags)): ?>
    <div class="form-card">
        <h3><i class="fas fa-tags"></i> Tags</h3>
        <div class="tags-grid">
            <?php foreach ($tags as $tag): ?>
                <label class="tag-checkbox">
                    <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>">
                    <span class="tag-dot" style="background: <?= $tag['color'] ?>"></span>
                    <span class="tag-label"><?= htmlspecialchars($tag['name']) ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="form-actions">
        <a href="<?= url('admin/leads') ?>" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Lead
        </button>
    </div>
</form>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
