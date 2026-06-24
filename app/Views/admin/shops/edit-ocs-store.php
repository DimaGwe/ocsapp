<?php
/**
 * OCS Store Management - Admin Edit Page
 * File: app/Views/admin/shops/edit-ocs-store.php
 */

$pageTitle = 'Manage OCSAPP Store';
$currentPage = 'ocs-store';

ob_start();
?>

<style>
    .store-header {
        background: linear-gradient(135deg, #00b207 0%, #008a05 100%);
        color: white;
        padding: 32px;
        border-radius: 12px;
        margin-bottom: 32px;
        box-shadow: 0 4px 20px rgba(0, 178, 7, 0.2);
    }

    .store-header h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .store-header p {
        font-size: 16px;
        opacity: 0.9;
    }

    .store-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #00b207;
    }

    .stat-label {
        font-size: 13px;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #2d3748;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }

    .form-card h2 {
        font-size: 20px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e2e8f0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .form-field {
        display: flex;
        flex-direction: column;
    }

    .form-field.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
    }

    .form-label .required {
        color: #e53e3e;
    }

    .form-input,
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.3s;
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #00b207;
        box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-help {
        font-size: 12px;
        color: #718096;
        margin-top: 4px;
    }

    .image-upload {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .current-image {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .current-image img {
        max-height: 120px;
        width: auto;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
    }

    .file-input {
        padding: 12px 16px;
        border: 2px dashed #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .file-input:hover {
        border-color: #00b207;
        background: #f0fdf4;
    }

    .btn-save {
        padding: 14px 32px;
        background: #00b207;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-save:hover {
        background: #009206;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);
    }

    .btn-secondary {
        padding: 14px 32px;
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary:hover {
        background: #cbd5e0;
    }

    .action-buttons {
        display: flex;
        gap: 16px;
        margin-top: 32px;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .store-stats {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>

<!-- Store Header -->
<div class="store-header">
    <h1>🏪 Manage OCSAPP Store</h1>
    <p>Configure your official marketplace store - Shop ID #1</p>
</div>

<!-- Stats -->
<div class="store-stats">
    <div class="stat-card">
        <div class="stat-label">Store Status</div>
        <div class="stat-value" style="color: <?= $shop['is_active'] ? '#00b207' : '#e53e3e' ?>">
            <?= $shop['is_active'] ? 'Active' : 'Inactive' ?>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Products</div>
        <div class="stat-value"><?= number_format($inventoryCount ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Created</div>
        <div class="stat-value" style="font-size: 18px;">
            <?= date('M d, Y', strtotime($shop['created_at'])) ?>
        </div>
    </div>
</div>

<!-- Edit Form -->
<form method="POST" action="<?= url('admin/ocs-store/update') ?>" enctype="multipart/form-data">
    <?= csrfField() ?>

    <!-- Basic Information -->
    <div class="form-card">
        <h2>📋 Basic Information</h2>
        <div class="form-grid">
            <div class="form-field full-width">
                <label class="form-label">
                    Store Name <span class="required">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($shop['name'] ?? 'OCSAPP Store') ?>"
                    class="form-input"
                    required
                >
            </div>

            <div class="form-field full-width">
                <label class="form-label">
                    Description <span class="required">*</span>
                </label>
                <textarea
                    name="description"
                    class="form-textarea"
                    required
                ><?= htmlspecialchars($shop['description'] ?? '') ?></textarea>
                <p class="form-help">Brief description of your official store</p>
            </div>

            <div class="form-field">
                <label class="form-label">
                    Email <span class="required">*</span>
                </label>
                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($shop['email'] ?? '') ?>"
                    class="form-input"
                    required
                >
            </div>

            <div class="form-field">
                <label class="form-label">
                    Phone
                </label>
                <input
                    type="tel"
                    name="phone"
                    value="<?= htmlspecialchars($shop['phone'] ?? '') ?>"
                    class="form-input"
                >
            </div>
        </div>
    </div>

    <!-- Address Information -->
    <div class="form-card">
        <h2>📍 Address Information</h2>
        <div class="form-grid">
            <div class="form-field full-width">
                <label class="form-label">Full Address</label>
                <textarea
                    name="address"
                    class="form-textarea"
                    style="min-height: 80px;"
                ><?= htmlspecialchars($shop['address'] ?? '') ?></textarea>
                <p class="form-help">Complete store address including street, city, province, postal code</p>
            </div>
        </div>
    </div>

    <!-- Images -->
    <div class="form-card">
        <h2>🖼️ Store Images</h2>
        <div class="form-grid">
            <!-- Logo -->
            <div class="form-field">
                <label class="form-label">Store Logo</label>
                <div class="image-upload">
                    <?php if (!empty($shop['logo'])): ?>
                        <div class="current-image">
                            <span style="font-size: 12px; color: #718096;">Current Logo:</span>
                            <img src="<?= asset($shop['logo']) ?>" alt="Store Logo">
                        </div>
                    <?php endif; ?>
                    <input
                        type="file"
                        name="logo"
                        accept="image/*"
                        class="file-input"
                    >
                    <p class="form-help">Recommended: 200x200px, PNG or JPG</p>
                </div>
            </div>

            <!-- Cover Image -->
            <div class="form-field">
                <label class="form-label">Store Cover Image</label>
                <div class="image-upload">
                    <?php if (!empty($shop['cover_image'])): ?>
                        <div class="current-image">
                            <span style="font-size: 12px; color: #718096;">Current Cover:</span>
                            <img src="<?= asset($shop['cover_image']) ?>" alt="Store Cover">
                        </div>
                    <?php endif; ?>
                    <input
                        type="file"
                        name="cover_image"
                        accept="image/*"
                        class="file-input"
                    >
                    <p class="form-help">Recommended: 1200x400px, PNG or JPG</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button type="submit" class="btn-save">
            <i class="fas fa-save"></i> Save Changes
        </button>
        <a href="<?= url('admin/dashboard') ?>" class="btn-secondary">
            Cancel
        </a>
    </div>
</form>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
