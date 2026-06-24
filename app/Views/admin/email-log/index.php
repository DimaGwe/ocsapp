<?php
$pageTitle = 'Email Log';
$currentPage = 'email-log';
ob_start();
?>

<style>
  .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
  .page-title { font-size: 26px; font-weight: 700; color: var(--dark); }

  .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
  .stat-card {
    background: white; border-radius: var(--radius-xl); padding: 20px;
    box-shadow: var(--shadow-sm); border-left: 4px solid var(--primary);
  }
  .stat-card.success { border-left-color: #10b981; }
  .stat-card.danger { border-left-color: #ef4444; }
  .stat-card.info { border-left-color: #3b82f6; }
  .stat-label { font-size: 12px; color: #6b7280; font-weight: 500; text-transform: uppercase; margin-bottom: 4px; }
  .stat-value { font-size: 24px; font-weight: 700; color: #1f2937; }

  .filters-card { background: white; border-radius: var(--radius-xl); padding: 18px 20px; margin-bottom: 20px; box-shadow: var(--shadow-sm); }
  .filters-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
  .form-input, .form-select {
    padding: 8px 12px; border: 2px solid var(--border); border-radius: var(--radius-md);
    font-size: 13px; min-width: 140px;
  }
  .form-input:focus, .form-select:focus { border-color: var(--primary); outline: none; }
  .btn-filter { padding: 8px 18px; background: var(--primary); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-weight: 600; font-size: 13px; }
  .btn-clear { padding: 8px 18px; background: white; color: #6b7280; border: 2px solid var(--border); border-radius: var(--radius-md); cursor: pointer; font-size: 13px; text-decoration: none; }

  .table-card { background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); overflow: hidden; }
  table { width: 100%; border-collapse: collapse; }
  th { padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; background: #f9fafb; border-bottom: 2px solid #e5e7eb; }
  td { padding: 10px 14px; font-size: 13px; color: #374151; border-bottom: 1px solid #f3f4f6; }
  tr:hover td { background: #f9fafb; }

  .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; text-transform: uppercase; }
  .badge-sent { background: #d1fae5; color: #065f46; }
  .badge-failed { background: #fee2e2; color: #991b1b; }
  .badge-test_mode { background: #fef3c7; color: #92400e; }

  .type-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; background: #f3f4f6; color: #374151; text-transform: uppercase; }

  .subject-cell { max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  .pagination { display: flex; justify-content: center; align-items: center; gap: 6px; margin-top: 20px; padding: 16px; }
  .pagination a, .pagination span {
    padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none;
    border: 1px solid #e5e7eb; color: #374151;
  }
  .pagination a:hover { background: #f3f4f6; }
  .pagination .current { background: var(--primary); color: white; border-color: var(--primary); }

  .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
  .empty-state i { font-size: 48px; margin-bottom: 16px; }

  @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="page-header">
  <h1 class="page-title"><i class="fas fa-envelope-open-text" style="color:var(--primary);margin-right:8px;"></i> Email Log</h1>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label">Total Emails</div>
    <div class="stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
  </div>
  <div class="stat-card success">
    <div class="stat-label">Sent Successfully</div>
    <div class="stat-value" style="color:#059669;"><?= number_format($stats['sent_count'] ?? 0) ?></div>
  </div>
  <div class="stat-card danger">
    <div class="stat-label">Failed</div>
    <div class="stat-value" style="color:#dc2626;"><?= number_format($stats['failed_count'] ?? 0) ?></div>
  </div>
  <div class="stat-card info">
    <div class="stat-label">Unique Recipients</div>
    <div class="stat-value" style="color:#2563eb;"><?= number_format($stats['unique_recipients'] ?? 0) ?></div>
  </div>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET" action="<?= url('admin/email-log') ?>" class="filters-row">
    <input type="text" name="search" class="form-input" placeholder="Search email or subject..." value="<?= htmlspecialchars($search) ?>" style="min-width:200px;">
    <select name="status" class="form-select">
      <option value="">All Statuses</option>
      <option value="sent" <?= $statusFilter === 'sent' ? 'selected' : '' ?>>Sent</option>
      <option value="failed" <?= $statusFilter === 'failed' ? 'selected' : '' ?>>Failed</option>
      <option value="test_mode" <?= $statusFilter === 'test_mode' ? 'selected' : '' ?>>Test Mode</option>
    </select>
    <select name="type" class="form-select">
      <option value="">All Types</option>
      <?php foreach ($emailTypes as $type): ?>
        <option value="<?= htmlspecialchars($type) ?>" <?= $typeFilter === $type ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $type)) ?></option>
      <?php endforeach; ?>
    </select>
    <input type="date" name="date_from" class="form-input" value="<?= htmlspecialchars($dateFrom) ?>" placeholder="From">
    <input type="date" name="date_to" class="form-input" value="<?= htmlspecialchars($dateTo) ?>" placeholder="To">
    <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
    <?php if ($search || $statusFilter || $typeFilter || $dateFrom || $dateTo): ?>
      <a href="<?= url('admin/email-log') ?>" class="btn-clear">Clear</a>
    <?php endif; ?>
  </form>
</div>

<!-- Emails Table -->
<div class="table-card">
  <?php if (empty($emails)): ?>
    <div class="empty-state">
      <i class="fas fa-envelope-open-text"></i>
      <p>No emails found.</p>
      <p style="font-size:13px;">Emails will appear here once they are sent through the system.</p>
    </div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Recipient</th>
        <th>Subject</th>
        <th>Type</th>
        <th>Status</th>
        <th>Sent At</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($emails as $em): ?>
      <tr>
        <td>
          <strong style="font-size:13px;"><?= htmlspecialchars($em['recipient_email']) ?></strong>
          <?php if ($em['recipient_name']): ?>
            <div style="font-size:11px;color:#9ca3af;"><?= htmlspecialchars($em['recipient_name']) ?></div>
          <?php endif; ?>
        </td>
        <td class="subject-cell" title="<?= htmlspecialchars($em['subject']) ?>"><?= htmlspecialchars($em['subject']) ?></td>
        <td>
          <?php if ($em['email_type']): ?>
            <span class="type-badge"><?= htmlspecialchars(str_replace('_', ' ', $em['email_type'])) ?></span>
          <?php else: ?>
            <span style="color:#9ca3af;font-size:12px;">—</span>
          <?php endif; ?>
        </td>
        <td><span class="badge badge-<?= $em['status'] ?>"><?= ucfirst(str_replace('_', ' ', $em['status'])) ?></span></td>
        <td style="font-size:12px;color:#6b7280;white-space:nowrap;">
          <?= date('M j, Y', strtotime($em['created_at'])) ?>
          <div style="font-size:11px;color:#9ca3af;"><?= date('g:i A', strtotime($em['created_at'])) ?></div>
        </td>
        <td>
          <a href="<?= url('admin/email-log/view?id=' . $em['id']) ?>" style="color:var(--primary);font-weight:600;font-size:12px;">View</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="<?= url('admin/email-log?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>"><i class="fas fa-chevron-left"></i></a>
    <?php endif; ?>

    <?php
    $startPage = max(1, $page - 2);
    $endPage = min($totalPages, $page + 2);
    for ($i = $startPage; $i <= $endPage; $i++):
    ?>
      <?php if ($i == $page): ?>
        <span class="current"><?= $i ?></span>
      <?php else: ?>
        <a href="<?= url('admin/email-log?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
      <a href="<?= url('admin/email-log?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>"><i class="fas fa-chevron-right"></i></a>
    <?php endif; ?>
    <span style="font-size:12px;color:#9ca3af;margin-left:8px;">Page <?= $page ?> of <?= $totalPages ?> (<?= number_format($totalEmails) ?> emails)</span>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
