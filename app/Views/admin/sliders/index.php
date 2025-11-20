<?php
/**
 * Hero Sliders Management - List View
 * File: app/Views/admin/sliders/index.php
 */

$pageTitle = $pageTitle ?? 'Hero Sliders';
$currentPage = $currentPage ?? 'cms';

ob_start();
?>

<style>
  /* Hero Sliders Specific Styles */
  .cms-header {
    margin-bottom: 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .cms-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .cms-header-subtitle {
    color: var(--text-muted);
    font-size: 14px;
    margin-top: 4px;
  }

  .btn-create {
    padding: 12px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-create:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .btn-secondary {
    padding: 12px 24px;
    background: var(--gray-200);
    color: var(--dark);
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-secondary:hover {
    background: var(--gray-300);
  }

  .sliders-grid {
    display: grid;
    gap: 24px;
  }

  .slider-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    display: grid;
    grid-template-columns: 60px 200px 1fr auto;
    gap: 20px;
    align-items: center;
    transition: all var(--transition-base);
  }

  .slider-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }

  .slider-order-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
  }

  .drag-handle {
    cursor: move;
    color: var(--text-muted);
    font-size: 20px;
  }

  .slider-order {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: var(--gray-100);
    border-radius: var(--radius-md);
    font-weight: 700;
    color: var(--text);
    font-size: 14px;
  }

  .slider-image {
    width: 200px;
    height: 120px;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .slider-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .slider-image-placeholder {
    font-size: 48px;
    color: var(--text-muted);
  }

  .slider-content {
    flex: 1;
  }

  .slider-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .slider-description {
    color: var(--text);
    margin-bottom: 12px;
    line-height: 1.5;
  }

  .slider-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    font-size: 13px;
    color: var(--text-muted);
  }

  .slider-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .slider-meta-item strong {
    color: var(--text);
  }

  .status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .status-active {
    background: #dcfce7;
    color: #166534;
  }

  .status-inactive {
    background: var(--gray-200);
    color: var(--text-muted);
  }

  .slider-actions {
    display: flex;
    gap: 8px;
    flex-direction: column;
  }

  .btn-edit, .btn-delete {
    padding: 8px 16px;
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: none;
    white-space: nowrap;
  }

  .btn-edit {
    background: var(--primary);
    color: white;
  }

  .btn-edit:hover {
    background: var(--primary-600);
  }

  .btn-delete {
    background: #fee2e2;
    color: #991b1b;
  }

  .btn-delete:hover {
    background: #fca5a5;
  }

  .empty-state {
    text-align: center;
    padding: 80px 40px;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
  }

  .empty-state-icon {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
  }

  .empty-state h2 {
    font-size: 24px;
    color: var(--text);
    margin-bottom: 8px;
  }

  .empty-state p {
    color: var(--text-muted);
    margin-bottom: 24px;
  }
</style>

<!-- Page Header -->
<div class="cms-header">
  <div>
    <h1>
      <span>🎨</span> Hero Sliders
    </h1>
    <div class="cms-header-subtitle">Manage homepage hero slider images and content</div>
  </div>
  <div style="display: flex; gap: 12px;">
    <a href="<?= url('/admin/sliders/create') ?>" class="btn-create">
      + Add New Slide
    </a>
    <a href="<?= url('/admin/cms') ?>" class="btn-secondary">
      ← Back to CMS
    </a>
  </div>
</div>

<!-- Sliders List -->
<?php if (!empty($sliders)): ?>
  <div class="sliders-grid">
    <?php foreach ($sliders as $slider): ?>
      <div class="slider-card" data-id="<?= $slider['id'] ?>">
        <!-- Order & Drag Handle -->
        <div class="slider-order-col">
          <span class="drag-handle" title="Drag to reorder">⋮⋮</span>
          <span class="slider-order"><?= $slider['sort_order'] ?></span>
        </div>

        <!-- Image -->
        <div class="slider-image">
          <?php if (!empty($slider['image_path'])): ?>
            <img src="<?= url($slider['image_path']) ?>"
                 alt="<?= htmlspecialchars($slider['title']) ?>">
          <?php else: ?>
            <div class="slider-image-placeholder">🖼️</div>
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
             class="btn-edit">
            ✎ Edit
          </a>
          <button class="btn-delete delete-slider"
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
    <a href="<?= url('/admin/sliders/create') ?>" class="btn-create">
      + Create First Slide
    </a>
  </div>
<?php endif; ?>

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
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
