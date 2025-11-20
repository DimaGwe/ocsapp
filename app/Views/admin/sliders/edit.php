<?php
/**
 * Hero Slider - Edit View
 * File: app/Views/admin/sliders/edit.php
 */

$pageTitle = $pageTitle ?? 'Edit Slider';
$slider = $slider ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - OCS Admin</title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        .admin-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            color: #64748b;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: #3b82f6;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .form-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group .help-text {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .file-upload-wrapper {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.2s;
            cursor: pointer;
        }

        .file-upload-wrapper:hover {
            border-color: #3b82f6;
            background: #f8fafc;
        }

        .file-upload-wrapper.drag-over {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .file-upload-wrapper input[type="file"] {
            display: none;
        }

        .upload-icon {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .current-image {
            margin-top: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
        }

        .current-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .flash-message {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .flash-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 32px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: 0.4s;
            border-radius: 32px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        .toggle-switch input:checked + .toggle-slider {
            background-color: #3b82f6;
        }

        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(28px);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="page-header">
            <div class="breadcrumb">
                <a href="<?= url('/admin') ?>">Dashboard</a> /
                <a href="<?= url('/admin/sliders') ?>">Sliders</a> /
                Edit
            </div>
            <h1>✎ Edit Slider</h1>
        </div>

        <!-- Flash Messages -->
        <?php if (hasFlash('error')): ?>
            <div class="flash-message flash-error">
                ✗ <?= flash('error') ?>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <div class="form-card">
            <form method="POST" action="<?= url('/admin/sliders/update') ?>" enctype="multipart/form-data">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $slider['id'] ?>">

                <!-- Title -->
                <div class="form-group">
                    <label for="title">Slide Title <span class="required">*</span></label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="<?= htmlspecialchars($slider['title'] ?? '') ?>"
                           required>
                    <div class="help-text">Main heading text for the slide</div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description"
                              name="description"
                              rows="3"><?= htmlspecialchars($slider['description'] ?? '') ?></textarea>
                    <div class="help-text">Subtitle or description text (optional)</div>
                </div>

                <!-- Button Text -->
                <div class="form-group">
                    <label for="button_text">Button Text</label>
                    <input type="text"
                           id="button_text"
                           name="button_text"
                           value="<?= htmlspecialchars($slider['button_text'] ?? '') ?>"
                           placeholder="e.g., Shop Now">
                    <div class="help-text">Text displayed on the button (optional)</div>
                </div>

                <!-- Button URL -->
                <div class="form-group">
                    <label for="button_url">Button URL</label>
                    <input type="text"
                           id="button_url"
                           name="button_url"
                           value="<?= htmlspecialchars($slider['button_url'] ?? '') ?>"
                           placeholder="/categories">
                    <div class="help-text">Where the button links to (e.g., /categories, /deals)</div>
                </div>

                <!-- Image Upload -->
                <div class="form-group">
                    <label for="image">Slider Image</label>

                    <?php if (!empty($slider['image_path'])): ?>
                        <div class="current-image">
                            <strong style="display: block; margin-bottom: 0.5rem;">Current Image:</strong>
                            <img src="<?= url($slider['image_path']) ?>"
                                 alt="Current slider image">
                        </div>
                        <div style="margin: 1rem 0; color: #64748b; font-size: 0.875rem;">
                            Upload a new image to replace the current one (leave empty to keep current)
                        </div>
                    <?php endif; ?>

                    <label for="image" class="file-upload-wrapper" id="fileUploadWrapper">
                        <div class="upload-icon">📤</div>
                        <div><strong>Click to upload</strong> or drag and drop</div>
                        <div class="help-text">JPEG, PNG, GIF, WebP (Max 5MB)</div>
                        <input type="file"
                               id="image"
                               name="image"
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                    </label>
                    <div id="fileName" style="margin-top: 0.5rem; color: #3b82f6; font-weight: 500;"></div>
                </div>

                <!-- Sort Order -->
                <div class="form-group">
                    <label for="sort_order">Display Order</label>
                    <input type="number"
                           id="sort_order"
                           name="sort_order"
                           value="<?= $slider['sort_order'] ?? 0 ?>"
                           min="0">
                    <div class="help-text">Lower numbers appear first (e.g., 1, 2, 3...)</div>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label>Status</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <label class="toggle-switch">
                            <input type="checkbox"
                                   name="status"
                                   value="active"
                                   <?= ($slider['status'] ?? 'active') === 'active' ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <span id="statusText">
                            <?= ($slider['status'] ?? 'active') === 'active' ? 'Active (Visible on website)' : 'Inactive (Hidden)' ?>
                        </span>
                    </div>
                    <input type="hidden" name="status" id="statusInput" value="<?= $slider['status'] ?? 'active' ?>">
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        💾 Save Changes
                    </button>
                    <a href="<?= url('/admin/sliders') ?>" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // File upload handling
        const fileInput = document.getElementById('image');
        const fileUploadWrapper = document.getElementById('fileUploadWrapper');
        const fileNameDisplay = document.getElementById('fileName');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileNameDisplay.textContent = '📎 Selected: ' + this.files[0].name;
            }
        });

        // Drag and drop
        fileUploadWrapper.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        fileUploadWrapper.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });

        fileUploadWrapper.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');

            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                fileNameDisplay.textContent = '📎 Selected: ' + e.dataTransfer.files[0].name;
            }
        });

        // Status toggle
        const statusCheckbox = document.querySelector('input[name="status"][type="checkbox"]');
        const statusText = document.getElementById('statusText');
        const statusInput = document.getElementById('statusInput');

        statusCheckbox.addEventListener('change', function() {
            if (this.checked) {
                statusText.textContent = 'Active (Visible on website)';
                statusInput.value = 'active';
            } else {
                statusText.textContent = 'Inactive (Hidden)';
                statusInput.value = 'inactive';
            }
        });
    </script>
</body>
</html>
