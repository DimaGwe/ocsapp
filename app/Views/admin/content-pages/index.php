<?php
/**
 * Admin Content Pages - List View
 */
$pageTitle = 'Content Pages Management';
$currentPage = 'content-pages';

ob_start();
?>

<style>
  /* Page Header */
  .cms-header {
    margin-bottom: 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
  }

  .cms-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .header-desc {
    font-size: 14px;
    color: var(--gray-600);
    font-weight: 400;
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

  /* Table Styles */
  .table-responsive {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  .data-table {
    width: 100%;
    border-collapse: collapse;
  }

  .data-table thead {
    background: var(--gray-50);
    border-bottom: 2px solid var(--border);
  }

  .data-table th {
    padding: 16px 20px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .data-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
  }

  .data-table tbody tr:hover {
    background: var(--gray-50);
  }

  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge-info {
    background: #dbeafe;
    color: #1e40af;
  }

  .badge-warning {
    background: #fef3c7;
    color: #92400e;
  }

  .badge-success {
    background: #dcfce7;
    color: #166534;
  }

  .badge-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .action-buttons {
    display: flex;
    gap: 8px;
  }

  .btn-sm {
    padding: 6px 12px;
    font-size: 13px;
    border-radius: var(--radius-sm);
    border: none;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
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

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gray-500);
  }

  .empty-state i {
    font-size: 48px;
    color: var(--gray-300);
    margin-bottom: 16px;
  }

  .alert {
    padding: 16px 20px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .alert-success {
    background: #dcfce7;
    color: #166534;
    border-left: 4px solid #16a34a;
  }

  .alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #dc2626;
  }
</style>

<div class="cms-header">
  <div>
    <h1>
      <i class="fas fa-file-alt"></i> Content Pages Management
    </h1>
    <p class="header-desc">Manage About Us, Contact Us, and other static pages</p>
  </div>
  <a href="<?= url('/admin/content-pages/create') ?>" class="btn-create">
    <i class="fas fa-plus"></i> Create New Page
  </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <?= htmlspecialchars($_SESSION['success']) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
  <div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <?= htmlspecialchars($_SESSION['error']) ?>
    <?php unset($_SESSION['error']); ?>
  </div>
<?php endif; ?>

<div class="table-responsive">
  <table class="data-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Type</th>
        <th>Title</th>
        <th>Slug</th>
        <th>Language</th>
        <th>Status</th>
        <th>Last Updated</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($pages)): ?>
        <tr>
          <td colspan="8">
            <div class="empty-state">
              <i class="fas fa-inbox"></i>
              <p>No pages found</p>
            </div>
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($pages as $page): ?>
          <tr>
            <td><?= $page['id'] ?></td>
            <td>
              <span class="badge badge-<?= $page['page_type'] === 'about' ? 'info' : 'warning' ?>">
                <?= ucfirst($page['page_type']) ?>
              </span>
            </td>
            <td>
              <strong><?= htmlspecialchars($page['title']) ?></strong>
            </td>
            <td>
              <code><?= htmlspecialchars($page['slug']) ?></code>
            </td>
            <td>
              <span class="badge badge-secondary">
                <?= strtoupper($page['language']) ?>
              </span>
            </td>
            <td>
              <?php if ($page['is_published']): ?>
                <span class="badge badge-success">
                  <i class="fas fa-check"></i> Published
                </span>
              <?php else: ?>
                <span class="badge badge-secondary">
                  <i class="fas fa-eye-slash"></i> Draft
                </span>
              <?php endif; ?>
            </td>
            <td><?= formatDate($page['updated_at'], 'M d, Y g:i A') ?></td>
            <td>
              <div class="action-buttons">
                <a href="<?= url('/admin/content-pages/edit?id=' . $page['id']) ?>"
                   class="btn-sm btn-primary"
                   title="Edit">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="<?= url('/' . $page['slug']) ?>"
                   class="btn-sm btn-secondary"
                   title="View"
                   target="_blank">
                  <i class="fas fa-eye"></i>
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
