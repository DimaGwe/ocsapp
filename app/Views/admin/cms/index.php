<?php
/**
 * OCS Admin CMS - Content Management System
 * File: app/Views/admin/cms/index.php
 */

$pageTitle = $pageTitle ?? 'Content Management';
$currentPage = $currentPage ?? 'cms';

ob_start();
?>

<style>
  /* Page Header */
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

  /* Content Cards */
  .content-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
  }

  .content-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 16px;
    margin-bottom: 24px;
    border-bottom: 2px solid var(--border);
  }

  .content-card-header h2 {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    text-transform: capitalize;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .page-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    background: #dcfce7;
    color: var(--primary);
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }

  /* Content Table */
  .content-table {
    width: 100%;
    border-collapse: collapse;
  }

  .content-table th {
    text-align: left;
    padding: 12px 16px;
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: var(--gray-50);
    border-bottom: 2px solid var(--border);
  }

  .content-table td {
    padding: 16px;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
  }

  .content-table tr:hover {
    background: var(--gray-50);
  }

  .content-label {
    font-weight: 600;
    color: var(--dark);
  }

  .content-section {
    font-size: 12px;
    color: var(--gray-500);
    font-family: 'Courier New', monospace;
  }

  .content-preview {
    max-width: 400px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: var(--gray-600);
  }

  .status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
  }

  .status-active {
    background: #dcfce7;
    color: #16a34a;
  }

  .status-inactive {
    background: #f3f4f6;
    color: #6b7280;
  }

  .content-type-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    background: #e0e7ff;
    color: #4f46e5;
  }

  /* Action Buttons */
  .btn-actions {
    display: flex;
    gap: 8px;
  }

  .btn-edit {
    padding: 6px 16px;
    background: white;
    color: var(--primary);
    border: 2px solid var(--primary);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .btn-edit:hover {
    background: var(--primary);
    color: white;
  }

  .btn-delete {
    padding: 6px 16px;
    background: white;
    color: #ef4444;
    border: 2px solid #ef4444;
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .btn-delete:hover {
    background: #ef4444;
    color: white;
  }

  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 48px 24px;
    color: var(--gray-500);
  }

  .empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
  }

  /* Info Banner */
  .info-banner {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
    padding: 16px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
    display: flex;
    align-items: start;
    gap: 12px;
  }

  .info-banner i {
    color: #3b82f6;
    font-size: 20px;
    margin-top: 2px;
  }

  .info-banner-content p {
    margin: 0;
    font-size: 14px;
    color: #1e40af;
    line-height: 1.6;
  }
</style>

<!-- Page Header -->
<div class="cms-header">
  <div>
    <h1>📝 Content Management</h1>
    <p style="color: var(--gray-600); margin-top: 8px;">Manage all page content across your marketplace</p>
  </div>
  <a href="<?= url('admin/cms/create') ?>" class="btn-create">
    <i class="fas fa-plus"></i> Add New Content
  </a>
</div>

<!-- Info Banner -->
<div class="info-banner">
  <i class="fas fa-info-circle"></i>
  <div class="info-banner-content">
    <p><strong>How it works:</strong> Edit any content section below to update text that appears on your website. Changes take effect immediately across all pages.</p>
  </div>
</div>

<!-- Quick Links Section -->
<div style="margin-bottom: 32px;">
  <h2 style="font-size: 18px; font-weight: 700; color: var(--dark); margin-bottom: 16px;">Content Management Tools</h2>
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">

    <!-- Hero Sliders Card -->
    <a href="<?= url('/admin/sliders') ?>" class="quick-link-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 24px; border-radius: var(--radius-xl); text-decoration: none; display: block; transition: all var(--transition-base); box-shadow: var(--shadow-sm);">
      <div style="font-size: 36px; margin-bottom: 12px;">🎨</div>
      <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Hero Sliders</h3>
      <p style="opacity: 0.9; font-size: 14px; margin: 0;">Manage homepage hero slider images, titles, and buttons</p>
      <div style="margin-top: 16px; display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 14px;">
        Manage Sliders <span>→</span>
      </div>
    </a>

    <!-- Promo Banners Card -->
    <a href="<?= url('/admin/promo-banners') ?>" class="quick-link-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 24px; border-radius: var(--radius-xl); text-decoration: none; display: block; transition: all var(--transition-base); box-shadow: var(--shadow-sm);">
      <div style="font-size: 36px; margin-bottom: 12px;">💰</div>
      <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Promo Banners</h3>
      <p style="opacity: 0.9; font-size: 14px; margin: 0;">Customize discount percentages and featured products</p>
      <div style="margin-top: 16px; display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 14px;">
        Manage Banners <span>→</span>
      </div>
    </a>

    <!-- Add more quick links here in the future -->

  </div>
</div>

<style>
  .quick-link-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
  }
</style>

<!-- Content Sections -->
<?php if (empty($contentsByPage)): ?>
  <div class="content-card">
    <div class="empty-state">
      <i class="fas fa-file-alt"></i>
      <h3>No content sections yet</h3>
      <p>Create your first content section to get started</p>
    </div>
  </div>
<?php else: ?>
  <?php foreach ($contentsByPage as $page => $contents): ?>
    <div class="content-card">
      <div class="content-card-header">
        <h2>
          <span class="page-badge">
            <i class="fas fa-file-alt"></i>
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $page))) ?>
          </span>
        </h2>
        <span style="font-size: 13px; color: var(--gray-500);">
          <?= count($contents) ?> section<?= count($contents) !== 1 ? 's' : '' ?>
        </span>
      </div>

      <table class="content-table">
        <thead>
          <tr>
            <th>Label</th>
            <th>Section</th>
            <th>Content Preview</th>
            <th>Type</th>
            <th>Status</th>
            <th style="text-align: center;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($contents as $content): ?>
            <tr>
              <td>
                <div class="content-label"><?= htmlspecialchars($content['label']) ?></div>
                <?php if (!empty($content['description'])): ?>
                  <div style="font-size: 12px; color: var(--gray-500); margin-top: 4px;">
                    <?= htmlspecialchars($content['description']) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <span class="content-section"><?= htmlspecialchars($content['section']) ?></span>
              </td>
              <td>
                <div class="content-preview">
                  <?php if (empty($content['content'])): ?>
                    <em style="color: var(--gray-400);">Empty</em>
                  <?php else: ?>
                    <?= htmlspecialchars(mb_substr($content['content'], 0, 80)) ?>
                    <?= mb_strlen($content['content']) > 80 ? '...' : '' ?>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <span class="content-type-badge">
                  <?= htmlspecialchars($content['content_type']) ?>
                </span>
              </td>
              <td>
                <span class="status-badge status-<?= $content['status'] ?>">
                  <?= $content['status'] ?>
                </span>
              </td>
              <td>
                <div class="btn-actions" style="justify-content: center;">
                  <a href="<?= url('admin/cms/edit?id=' . $content['id']) ?>" class="btn-edit">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                  <form method="POST" action="<?= url('admin/cms/delete') ?>"
                        style="display: inline-block; margin: 0;"
                        onsubmit="return confirm('Are you sure you want to delete this content?');">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $content['id'] ?>">
                    <button type="submit" class="btn-delete">
                      <i class="fas fa-trash"></i> Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
