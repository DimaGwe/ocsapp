<?php
/**
 * Admin Edit Vendor - OCSAPP
 */
?>
<?php ob_start(); ?>

<link rel="stylesheet" href="<?= asset('css/admin-forms.css') ?>">

<div class="create-vendor-wrapper" style="max-width: 800px; margin: 0 auto;">
    <div class="page-header" style="margin-bottom: 32px;">
        <h1 style="font-size: 28px; font-weight: 700; margin: 0 0 8px;"><i class="fa-solid fa-edit"></i> Edit Vendor</h1>
        <p style="color: #6b7280; margin: 0;">Update vendor information and settings</p>
    </div>

    <form action="<?= url('admin/vendors/update') ?>" method="POST" style="background: white; border-radius: 12px; padding: 32px; border: 1px solid #e5e7eb;">
        <?= csrfField() ?>
        <input type="hidden" name="id" value="<?= $vendor['id'] ?>">

        <!-- Company Information -->
        <div style="margin-bottom: 32px;">
            <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Company Information</h3>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Company Name <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="company_name" value="<?= htmlspecialchars($vendor['company_name']) ?>" required style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Email <span style="color: #ef4444;">*</span></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($vendor['email']) ?>" required style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Phone</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($vendor['phone'] ?? '') ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Contact Person</label>
                    <input type="text" name="contact_person" value="<?= htmlspecialchars($vendor['contact_person'] ?? '') ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Business Number</label>
                    <input type="text" name="business_number" value="<?= htmlspecialchars($vendor['business_number'] ?? '') ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Tax ID / GST Number</label>
                    <input type="text" name="tax_id" value="<?= htmlspecialchars($vendor['tax_id'] ?? '') ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Website</label>
                    <input type="text" name="website" value="<?= htmlspecialchars($vendor['website'] ?? '') ?>" placeholder="https://example.com" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Status</label>
                    <select name="status" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                        <option value="active" <?= $vendor['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="pending" <?= $vendor['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="suspended" <?= $vendor['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        <option value="inactive" <?= $vendor['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div style="grid-column: 1 / -1; margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Description</label>
                    <textarea name="description" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; min-height: 100px;"><?= htmlspecialchars($vendor['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Address -->
        <div style="margin-bottom: 32px;">
            <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Address Information</h3>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Street Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($vendor['address'] ?? '') ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">City</label>
                    <input type="text" name="city" value="<?= htmlspecialchars($vendor['city'] ?? '') ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Province</label>
                    <input type="text" name="province" value="<?= htmlspecialchars($vendor['province'] ?? '') ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px;">Postal Code</label>
                    <input type="text" name="postal_code" value="<?= htmlspecialchars($vendor['postal_code'] ?? '') ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;">
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div style="margin-bottom: 32px;">
            <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Settings</h3>

            <div>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="auto_approve_orders" value="1" <?= $vendor['auto_approve_orders'] ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                    <span style="font-weight: 600;">Auto-approve all orders from this vendor</span>
                </label>
                <div style="font-size: 13px; color: #6b7280; margin-top: 6px; margin-left: 26px;">When enabled, orders from this vendor won't require manual approval</div>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 24px; border-top: 1px solid #e5e7eb;">
            <a href="<?= url('admin/vendors/view?id=' . $vendor['id']) ?>" style="padding: 12px 24px; border-radius: 8px; font-weight: 600; background: #f3f4f6; color: #4b5563; text-decoration: none;">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; border-radius: 8px; font-weight: 600; background: var(--primary); color: white; border: none; cursor: pointer;">
                <i class="fa-solid fa-check"></i> Update Vendor
            </button>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>
