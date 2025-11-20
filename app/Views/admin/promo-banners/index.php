<?php
/**
 * Promo Banners Management - List View
 * File: app/Views/admin/promo-banners/index.php
 */

$pageTitle = $pageTitle ?? 'Promo Banners';
$currentPage = $currentPage ?? 'cms';

ob_start();
?>

<style>
  /* Promo Banners Specific Styles */
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

  .banners-grid {
    display: grid;
    gap: 24px;
  }

  .banner-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 20px;
    align-items: center;
    transition: all var(--transition-base);
  }

  .banner-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }

  .banner-discount-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: var(--radius-lg);
    color: white;
    font-size: 28px;
    font-weight: 700;
    box-shadow: var(--shadow-sm);
  }

  .banner-content {
    flex: 1;
  }

  .banner-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .banner-subtitle {
    color: var(--text);
    margin-bottom: 12px;
    line-height: 1.5;
  }

  .banner-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    font-size: 13px;
    color: var(--text-muted);
  }

  .banner-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .banner-meta-item strong {
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

  .banner-actions {
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

  .product-count-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: var(--gray-100);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text);
  }
</style>

<!-- Page Header -->
<div class="cms-header">
  <div>
    <h1>
      <span>💰</span> Promo Banners
    </h1>
    <div class="cms-header-subtitle">Manage homepage promotional banners with custom discounts</div>
  </div>
  <div style="display: flex; gap: 12px;">
    <a href="<?= url('/admin/promo-banners/create') ?>" class="btn-create">
      + Add New Banner
    </a>
    <a href="<?= url('/admin/cms') ?>" class="btn-secondary">
      ← Back to CMS
    </a>
  </div>
</div>

<!-- Banners List -->
<?php if (!empty($banners)): ?>
  <div class="banners-grid">
    <?php foreach ($banners as $banner): ?>
      <?php
        $selectedProducts = json_decode($banner['selected_products'] ?? '[]', true) ?: [];
        $productCount = count($selectedProducts);
      ?>
      <div class="banner-card" data-id="<?= $banner['id'] ?>">
        <!-- Discount Badge -->
        <div class="banner-discount-badge">
          <?= $banner['discount_percentage'] ?>%
        </div>

        <!-- Content -->
        <div class="banner-content">
          <div class="banner-title"><?= htmlspecialchars($banner['title']) ?></div>

          <?php if (!empty($banner['subtitle'])): ?>
            <div class="banner-subtitle">
              <?= htmlspecialchars($banner['subtitle']) ?>
            </div>
          <?php endif; ?>

          <div class="banner-meta">
            <?php if (!empty($banner['button_text'])): ?>
              <div class="banner-meta-item">
                <strong>Button:</strong> <?= htmlspecialchars($banner['button_text']) ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($banner['button_url'])): ?>
              <div class="banner-meta-item">
                <strong>URL:</strong> <?= htmlspecialchars($banner['button_url']) ?>
              </div>
            <?php endif; ?>

            <div class="banner-meta-item">
              <span class="product-count-badge">
                🛍️ <?= $productCount ?> <?= $productCount === 1 ? 'product' : 'products' ?>
              </span>
            </div>

            <div class="banner-meta-item">
              <strong>Status:</strong>
              <span class="status-badge status-<?= $banner['status'] ?>">
                <?= $banner['status'] ?>
              </span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="banner-actions">
          <a href="<?= url('/admin/promo-banners/edit?id=' . $banner['id']) ?>"
             class="btn-edit">
            ✎ Edit
          </a>
          <button class="btn-delete delete-banner"
                  data-id="<?= $banner['id'] ?>"
                  data-title="<?= htmlspecialchars($banner['title']) ?>">
            🗑 Delete
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <div class="empty-state">
    <div class="empty-state-icon">💰</div>
    <h2>No Promo Banners Yet</h2>
    <p>Create your first promotional banner to get started</p>
    <a href="<?= url('/admin/promo-banners/create') ?>" class="btn-create">
      + Create First Banner
    </a>
  </div>
<?php endif; ?>

<script>
  // Delete banner functionality
  document.querySelectorAll('.delete-banner').forEach(btn => {
    btn.addEventListener('click', async function() {
      const id = this.dataset.id;
      const title = this.dataset.title;

      if (!confirm(`Are you sure you want to delete "${title}"?\n\nThis action cannot be undone.`)) {
        return;
      }

      try {
        const response = await fetch('<?= url('/admin/promo-banners/delete') ?>', {
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
          alert('Failed to delete banner: ' + (result.error || 'Unknown error'));
        }
      } catch (error) {
        console.error('Delete error:', error);
        alert('Failed to delete banner. Please try again.');
      }
    });
  });
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
