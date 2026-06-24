<?php
/**
 * Reusable Pagination Component
 *
 * Required variables:
 *   $page    - current page number
 *   $perPage - items per page
 *   $total   - total item count
 *   $baseUrl - base URL (e.g., 'supplier/products')
 *   $queryParams - array of filter params to preserve (e.g., ['search' => 'foo', 'status' => 'active'])
 */
if (!isset($page, $perPage, $total, $baseUrl)) return;
if ($total <= $perPage) return;

$totalPages = (int)ceil($total / $perPage);
$queryParams = $queryParams ?? [];

// Build URL helper
$buildUrl = function($pageNum) use ($baseUrl, $queryParams) {
    $params = array_merge($queryParams, ['page' => $pageNum]);
    $params = array_filter($params, fn($v) => $v !== '' && $v !== null);
    $qs = http_build_query($params);
    return url($baseUrl . ($qs ? '?' . $qs : ''));
};

$startItem = (($page - 1) * $perPage) + 1;
$endItem = min($page * $perPage, $total);
$rangeStart = max(1, $page - 2);
$rangeEnd = min($totalPages, $page + 2);
?>

<style>
  .pagination-bar { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; flex-wrap: wrap; gap: 12px; }
  .pagination-info { font-size: 13px; color: var(--gray-500, #6b7280); }
  .pagination-links { display: flex; gap: 4px; align-items: center; }
  .pagination-links a, .pagination-links span {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 10px;
    border-radius: 8px; font-size: 14px; font-weight: 500;
    text-decoration: none; transition: all 0.15s;
  }
  .pagination-links a { color: var(--gray-700, #374151); background: white; border: 1px solid var(--gray-200, #e5e7eb); }
  .pagination-links a:hover { background: var(--gray-50, #f9fafb); border-color: var(--primary, #00b207); color: var(--primary, #00b207); }
  .pagination-links .active { background: var(--primary, #00b207); color: white; border: 1px solid var(--primary, #00b207); pointer-events: none; }
  .pagination-links .dots { color: var(--gray-400, #9ca3af); border: none; background: none; min-width: auto; padding: 0 4px; }
  .pagination-links .disabled { opacity: 0.4; pointer-events: none; }
</style>

<div class="pagination-bar">
  <div class="pagination-info">
    Showing <?= $startItem ?>&ndash;<?= $endItem ?> of <?= number_format($total) ?>
  </div>
  <div class="pagination-links">
    <?php if ($page > 1): ?>
      <a href="<?= $buildUrl($page - 1) ?>" title="Previous"><i class="fas fa-chevron-left" style="font-size:12px;"></i></a>
    <?php else: ?>
      <span class="disabled"><i class="fas fa-chevron-left" style="font-size:12px;"></i></span>
    <?php endif; ?>

    <?php if ($rangeStart > 1): ?>
      <a href="<?= $buildUrl(1) ?>">1</a>
      <?php if ($rangeStart > 2): ?><span class="dots">&hellip;</span><?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $rangeStart; $i <= $rangeEnd; $i++): ?>
      <?php if ($i === $page): ?>
        <span class="active"><?= $i ?></span>
      <?php else: ?>
        <a href="<?= $buildUrl($i) ?>"><?= $i ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <?php if ($rangeEnd < $totalPages): ?>
      <?php if ($rangeEnd < $totalPages - 1): ?><span class="dots">&hellip;</span><?php endif; ?>
      <a href="<?= $buildUrl($totalPages) ?>"><?= $totalPages ?></a>
    <?php endif; ?>

    <?php if ($page < $totalPages): ?>
      <a href="<?= $buildUrl($page + 1) ?>" title="Next"><i class="fas fa-chevron-right" style="font-size:12px;"></i></a>
    <?php else: ?>
      <span class="disabled"><i class="fas fa-chevron-right" style="font-size:12px;"></i></span>
    <?php endif; ?>
  </div>
</div>
