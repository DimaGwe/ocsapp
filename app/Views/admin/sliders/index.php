<?php
/**
 * Hero Sliders Management - List View
 * File: app/Views/admin/sliders/index.php
 */

$pageTitle = $pageTitle ?? 'Hero Sliders Management';
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
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
            font-size: 0.95rem;
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
            background: #64748b;
            color: white;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .flash-message {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .flash-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .flash-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .sliders-grid {
            display: grid;
            gap: 1.5rem;
        }

        .slider-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 200px 1fr auto;
            gap: 1.5rem;
            align-items: center;
            transition: all 0.2s;
        }

        .slider-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .slider-image {
            width: 200px;
            height: 120px;
            border-radius: 8px;
            object-fit: cover;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 3rem;
        }

        .slider-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .slider-content {
            flex: 1;
        }

        .slider-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .slider-description {
            color: #64748b;
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }

        .slider-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            font-size: 0.875rem;
            color: #64748b;
        }

        .slider-meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background: #f1f5f9;
            color: #64748b;
        }

        .slider-actions {
            display: flex;
            gap: 0.5rem;
            flex-direction: column;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h2 {
            font-size: 1.5rem;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #94a3b8;
            margin-bottom: 1.5rem;
        }

        .drag-handle {
            cursor: move;
            color: #94a3b8;
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }

        .slider-order {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #f1f5f9;
            border-radius: 6px;
            font-weight: 600;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1>🎨 Hero Sliders Management</h1>
                <p style="color: #64748b; margin-top: 0.5rem;">Manage homepage hero slider images and content</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="<?= url('/admin/sliders/create') ?>" class="btn btn-primary">
                    + Add New Slide
                </a>
                <a href="<?= url('/admin') ?>" class="btn btn-secondary">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (hasFlash('success')): ?>
            <div class="flash-message flash-success">
                ✓ <?= flash('success') ?>
            </div>
        <?php endif; ?>

        <?php if (hasFlash('error')): ?>
            <div class="flash-message flash-error">
                ✗ <?= flash('error') ?>
            </div>
        <?php endif; ?>

        <!-- Sliders List -->
        <?php if (!empty($sliders)): ?>
            <div class="sliders-grid">
                <?php foreach ($sliders as $slider): ?>
                    <div class="slider-card" data-id="<?= $slider['id'] ?>">
                        <!-- Order & Drag Handle -->
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                            <span class="drag-handle" title="Drag to reorder">⋮⋮</span>
                            <span class="slider-order"><?= $slider['sort_order'] ?></span>
                        </div>

                        <!-- Image -->
                        <div class="slider-image">
                            <?php if (!empty($slider['image_path'])): ?>
                                <img src="<?= url($slider['image_path']) ?>"
                                     alt="<?= htmlspecialchars($slider['title']) ?>">
                            <?php else: ?>
                                🖼️
                            <?php endif; ?>
                        </div>

                        <!-- Content -->
                        <div class="slider-content">
                            <div class="slider-title"><?= htmlspecialchars($slider['title']) ?></div>

                            <?php if (!empty($slider['description'])): ?>
                                <div class="slider-description">
                                    <?= htmlspecialchars($slider['description']) ?>
                                </div>
                            <?php endif; ?>

                            <div class="slider-meta">
                                <?php if (!empty($slider['button_text'])): ?>
                                    <div class="slider-meta-item">
                                        <strong>Button:</strong> <?= htmlspecialchars($slider['button_text']) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($slider['button_url'])): ?>
                                    <div class="slider-meta-item">
                                        <strong>URL:</strong> <?= htmlspecialchars($slider['button_url']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="slider-meta-item">
                                    <strong>Status:</strong>
                                    <span class="status-badge status-<?= $slider['status'] ?>">
                                        <?= $slider['status'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="slider-actions">
                            <a href="<?= url('/admin/sliders/edit?id=' . $slider['id']) ?>"
                               class="btn btn-primary btn-sm">
                                ✎ Edit
                            </a>
                            <button class="btn btn-danger btn-sm delete-slider"
                                    data-id="<?= $slider['id'] ?>"
                                    data-title="<?= htmlspecialchars($slider['title']) ?>">
                                🗑 Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">🎨</div>
                <h2>No Sliders Yet</h2>
                <p>Create your first hero slider to get started</p>
                <a href="<?= url('/admin/sliders/create') ?>" class="btn btn-primary">
                    + Create First Slide
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Delete slider functionality
        document.querySelectorAll('.delete-slider').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.dataset.id;
                const title = this.dataset.title;

                if (!confirm(`Are you sure you want to delete "${title}"?\n\nThis action cannot be undone.`)) {
                    return;
                }

                try {
                    const response = await fetch('<?= url('/admin/sliders/delete') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({
                            id: id,
                            _csrf_token: document.querySelector('meta[name="csrf-token"]')?.content || ''
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        window.location.reload();
                    } else {
                        alert('Failed to delete slider: ' + (result.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    alert('Failed to delete slider. Please try again.');
                }
            });
        });

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.flash-message').forEach(msg => {
                msg.style.transition = 'opacity 0.5s';
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
