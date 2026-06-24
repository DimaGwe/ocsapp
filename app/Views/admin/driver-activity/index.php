<?php
/**
 * Admin — Driver Activity Log
 * File: app/Views/admin/driver-activity/index.php
 */

$currentPage    = 'driver-activity';
$activeSection  = 'operations';
$pageTitle      = 'Driver Activity Log';

ob_start();
?>

<div class="admin-page-header">
    <div class="page-header-left">
        <h1 class="page-title">Driver Activity Log</h1>
        <p class="page-subtitle">Accept &amp; decline events from the ODA driver app</p>
    </div>
    <div class="page-header-right">
        <a href="<?= url('admin/driver-activity/export?' . http_build_query([
            'driver_id'  => $driverId,
            'action'     => $action,
            'order_type' => $orderType,
            'date_from'  => $dateFrom,
            'date_to'    => $dateTo,
        ])) ?>" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
</div>

<!-- ── Summary Cards ─────────────────────────────────────────────────────── -->
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">

    <div class="stat-card" style="background:#fff;border-radius:8px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">Total Accepts</div>
        <div style="font-size:28px;font-weight:700;color:#16a34a;"><?= number_format((int)($stats['total_accepted'] ?? 0)) ?></div>
        <div style="font-size:12px;color:#9ca3af;margin-top:4px;">All time</div>
    </div>

    <div class="stat-card" style="background:#fff;border-radius:8px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">Total Declines</div>
        <div style="font-size:28px;font-weight:700;color:#dc2626;"><?= number_format((int)($stats['total_declined'] ?? 0)) ?></div>
        <div style="font-size:12px;color:#9ca3af;margin-top:4px;">All time</div>
    </div>

    <div class="stat-card" style="background:#fff;border-radius:8px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">This Month Accepts</div>
        <div style="font-size:28px;font-weight:700;color:#2563eb;"><?= number_format((int)($stats['month_accepted'] ?? 0)) ?></div>
        <div style="font-size:12px;color:#9ca3af;margin-top:4px;"><?= date('F Y') ?></div>
    </div>

    <div class="stat-card" style="background:#fff;border-radius:8px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">Decline Rate</div>
        <div style="font-size:28px;font-weight:700;color:<?= ($stats['decline_rate'] ?? 0) > 25 ? '#dc2626' : '#f59e0b' ?>;">
            <?= $stats['decline_rate'] ?? 0 ?>%
        </div>
        <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Declines / (Accepts + Declines)</div>
    </div>

</div>

<!-- ── Filters ────────────────────────────────────────────────────────────── -->
<div class="filter-card" style="background:#fff;border-radius:8px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.08);margin-bottom:24px;">
    <form method="GET" action="<?= url('admin/driver-activity') ?>" class="filter-form">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;align-items:end;">

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Driver</label>
                <select name="driver_id" class="form-control form-control-sm">
                    <option value="">All Drivers</option>
                    <?php foreach ($drivers as $d): ?>
                        <option value="<?= (int)$d['id'] ?>" <?= (int)$driverId === (int)$d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Action</label>
                <select name="action" class="form-control form-control-sm">
                    <option value="">All</option>
                    <option value="accepted" <?= $action === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                    <option value="declined" <?= $action === 'declined' ? 'selected' : '' ?>>Declined</option>
                </select>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Order Type</label>
                <select name="order_type" class="form-control form-control-sm">
                    <option value="">All Types</option>
                    <option value="marketplace"  <?= $orderType === 'marketplace'  ? 'selected' : '' ?>>Marketplace</option>
                    <option value="distribution" <?= $orderType === 'distribution' ? 'selected' : '' ?>>Distribution</option>
                </select>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($dateTo) ?>">
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Reference #</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="e.g. PO-1042"
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="<?= url('admin/driver-activity') ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>

        </div>
    </form>
</div>

<!-- ── Activity Table ─────────────────────────────────────────────────────── -->
<div class="data-card" style="background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.08);margin-bottom:32px;">
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:15px;font-weight:600;">
            Activity Events
            <span style="font-size:13px;font-weight:400;color:#6b7280;margin-left:8px;">
                <?= number_format($total) ?> result<?= $total !== 1 ? 's' : '' ?>
            </span>
        </h3>
    </div>

    <?php if (empty($rows)): ?>
        <div style="padding:40px;text-align:center;color:#9ca3af;">
            <i class="fas fa-history" style="font-size:32px;margin-bottom:12px;display:block;"></i>
            No activity events found for the selected filters.
        </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table class="admin-table" style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f9fafb;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">
                    <th style="padding:10px 16px;text-align:left;">Date</th>
                    <th style="padding:10px 16px;text-align:left;">Driver</th>
                    <th style="padding:10px 16px;text-align:left;">Action</th>
                    <th style="padding:10px 16px;text-align:left;">Type</th>
                    <th style="padding:10px 16px;text-align:left;">Reference</th>
                    <th style="padding:10px 16px;text-align:left;">Reason</th>
                    <th style="padding:10px 16px;text-align:center;">View</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr style="border-top:1px solid #f3f4f6;font-size:13px;" class="table-row-hover">
                    <td style="padding:10px 16px;color:#374151;white-space:nowrap;">
                        <?= date('M j, Y', strtotime($row['created_at'])) ?>
                        <div style="font-size:11px;color:#9ca3af;"><?= date('g:i A', strtotime($row['created_at'])) ?></div>
                    </td>
                    <td style="padding:10px 16px;">
                        <div style="font-weight:500;color:#111827;"><?= htmlspecialchars($row['driver_name']) ?></div>
                        <div style="font-size:11px;color:#9ca3af;"><?= htmlspecialchars($row['driver_email']) ?></div>
                    </td>
                    <td style="padding:10px 16px;">
                        <?php if ($row['action'] === 'accepted'): ?>
                            <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#dcfce7;color:#15803d;">
                                <i class="fas fa-check"></i> Accepted
                            </span>
                        <?php else: ?>
                            <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#fee2e2;color:#dc2626;">
                                <i class="fas fa-times"></i> Declined
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px 16px;">
                        <?php if ($row['order_type'] === 'marketplace'): ?>
                            <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#ede9fe;color:#7c3aed;">
                                <i class="fas fa-shopping-cart"></i> Marketplace
                            </span>
                        <?php else: ?>
                            <span style="display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;background:#fef3c7;color:#92400e;">
                                <i class="fas fa-boxes"></i> Distribution
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px 16px;font-family:monospace;font-size:12px;color:#374151;">
                        <?= htmlspecialchars($row['reference_number'] ?? '—') ?>
                    </td>
                    <td style="padding:10px 16px;color:#6b7280;font-size:12px;max-width:200px;">
                        <?= htmlspecialchars($row['reason'] ?? '') ?: '—' ?>
                    </td>
                    <td style="padding:10px 16px;text-align:center;">
                        <?php
                        $viewLink = null;
                        if ($row['order_type'] === 'marketplace' && !empty($row['order_id'])) {
                            $viewLink = url('admin/orders/view?id=' . (int)$row['order_id']);
                        } elseif ($row['order_type'] === 'distribution') {
                            if (!empty($row['distribution_request_id'])) {
                                $viewLink = url('admin/distribution/view?id=' . (int)$row['distribution_request_id']);
                            } elseif (!empty($row['po_id'])) {
                                $viewLink = url('admin/purchase-orders/view?id=' . (int)$row['po_id']);
                            }
                        }
                        ?>
                        <?php if ($viewLink): ?>
                            <a href="<?= $viewLink ?>" class="btn btn-secondary btn-xs" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        <?php else: ?>
                            <span style="color:#d1d5db;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- ── Pagination ─────────────────────────────────────────────────────────── -->
<?php if ($totalPages > 1): ?>
<div style="display:flex;justify-content:center;align-items:center;gap:8px;margin-bottom:32px;flex-wrap:wrap;">
    <?php
    $baseUrl     = url('admin/driver-activity');
    $queryParams = array_filter([
        'driver_id'  => $driverId  ?: null,
        'action'     => $action    ?: null,
        'order_type' => $orderType ?: null,
        'date_from'  => $dateFrom  ?: null,
        'date_to'    => $dateTo    ?: null,
        'search'     => $search    ?: null,
    ]);

    $prevPage = max(1, $page - 1);
    $nextPage = min($totalPages, $page + 1);

    $buildUrl = function(int $p) use ($baseUrl, $queryParams): string {
        return $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $p]));
    };
    ?>
    <a href="<?= $buildUrl($prevPage) ?>" class="btn btn-secondary btn-sm" <?= $page <= 1 ? 'style="opacity:.4;pointer-events:none;"' : '' ?>>
        <i class="fas fa-chevron-left"></i>
    </a>
    <?php
    $start = max(1, $page - 2);
    $end   = min($totalPages, $page + 2);
    if ($start > 1): ?>
        <a href="<?= $buildUrl(1) ?>" class="btn btn-secondary btn-sm">1</a>
        <?php if ($start > 2): ?><span style="color:#9ca3af;">…</span><?php endif; ?>
    <?php endif;
    for ($i = $start; $i <= $end; $i++): ?>
        <a href="<?= $buildUrl($i) ?>"
           class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-secondary' ?>">
            <?= $i ?>
        </a>
    <?php endfor;
    if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?><span style="color:#9ca3af;">…</span><?php endif; ?>
        <a href="<?= $buildUrl($totalPages) ?>" class="btn btn-secondary btn-sm"><?= $totalPages ?></a>
    <?php endif; ?>
    <a href="<?= $buildUrl($nextPage) ?>" class="btn btn-secondary btn-sm" <?= $page >= $totalPages ? 'style="opacity:.4;pointer-events:none;"' : '' ?>>
        <i class="fas fa-chevron-right"></i>
    </a>
    <span style="font-size:12px;color:#9ca3af;margin-left:8px;">
        Page <?= $page ?> of <?= $totalPages ?> &mdash; <?= number_format($total) ?> records
    </span>
</div>
<?php endif; ?>

<!-- ── Per-Driver Summary ─────────────────────────────────────────────────── -->
<?php if (!empty($driverStats)): ?>
<div class="data-card" style="background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.08);margin-bottom:32px;">
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
        <h3 style="margin:0;font-size:15px;font-weight:600;">Per-Driver Summary</h3>
        <p style="margin:4px 0 0;font-size:12px;color:#9ca3af;">All-time totals, sorted by decline count</p>
    </div>
    <div style="overflow-x:auto;">
        <table class="admin-table" style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f9fafb;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">
                    <th style="padding:10px 16px;text-align:left;">Driver</th>
                    <th style="padding:10px 16px;text-align:center;">Accepts</th>
                    <th style="padding:10px 16px;text-align:center;">Declines</th>
                    <th style="padding:10px 16px;text-align:center;">Decline Rate</th>
                    <th style="padding:10px 16px;text-align:left;">Last Active</th>
                    <th style="padding:10px 16px;text-align:center;">Filter</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($driverStats as $ds):
                $dsTotal       = (int)$ds['accepts'] + (int)$ds['declines'];
                $dsDeclineRate = $dsTotal > 0 ? round((int)$ds['declines'] / $dsTotal * 100, 1) : 0;
                $rateColor     = $dsDeclineRate > 30 ? '#dc2626' : ($dsDeclineRate > 15 ? '#f59e0b' : '#16a34a');
            ?>
                <tr style="border-top:1px solid #f3f4f6;font-size:13px;" class="table-row-hover">
                    <td style="padding:10px 16px;font-weight:500;color:#111827;">
                        <?= htmlspecialchars($ds['driver_name']) ?>
                    </td>
                    <td style="padding:10px 16px;text-align:center;color:#16a34a;font-weight:600;">
                        <?= number_format((int)$ds['accepts']) ?>
                    </td>
                    <td style="padding:10px 16px;text-align:center;color:#dc2626;font-weight:600;">
                        <?= number_format((int)$ds['declines']) ?>
                    </td>
                    <td style="padding:10px 16px;text-align:center;">
                        <span style="font-weight:700;color:<?= $rateColor ?>;"><?= $dsDeclineRate ?>%</span>
                    </td>
                    <td style="padding:10px 16px;color:#6b7280;font-size:12px;white-space:nowrap;">
                        <?= $ds['last_active'] ? date('M j, Y g:i A', strtotime($ds['last_active'])) : '—' ?>
                    </td>
                    <td style="padding:10px 16px;text-align:center;">
                        <a href="<?= url('admin/driver-activity?driver_id=' . (int)$ds['driver_id']) ?>"
                           class="btn btn-secondary btn-xs" title="Filter by this driver">
                            <i class="fas fa-filter"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<style>
.table-row-hover:hover { background:#f9fafb; }
.btn-xs { padding:3px 8px; font-size:11px; }
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
