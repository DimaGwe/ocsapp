<?php
$currentPage = 'distribution';
$pageTitle = 'View Request - ' . htmlspecialchars($request['request_number'] ?? '');
ob_start();
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}

.breadcrumb {
    font-size: 14px;
    margin-bottom: 8px;
}

.breadcrumb a {
    color: #00b207;
    text-decoration: none;
}

.breadcrumb span {
    color: #666;
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
}

@media (max-width: 1200px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    padding: 24px;
    margin-bottom: 24px;
}

.card-title {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-title i {
    color: #00b207;
}

.badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-draft { background: #f3f4f6; color: #666; }
.badge-pending, .badge-submitted { background: #dbeafe; color: #1d4ed8; }
.badge-approved, .badge-quoted { background: #fef3c7; color: #b45309; }
.badge-pending_payment { background: #fee2e2; color: #dc2626; }
.badge-paid { background: #d1fae5; color: #059669; }
.badge-procurement, .badge-processing { background: #e0e7ff; color: #4f46e5; }
.badge-in_transit, .badge-ready { background: #cffafe; color: #0891b2; }
.badge-delivered, .badge-completed { background: #d1fae5; color: #059669; }
.badge-cancelled { background: #fef2f2; color: #991b1b; }
.badge-expired { background: #f3f4f6; color: #666; }

.status-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.info-item {
    margin-bottom: 16px;
}

.info-label {
    font-size: 11px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.info-value {
    font-size: 14px;
    color: #1a1a1a;
    font-weight: 500;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table th, .items-table td {
    padding: 12px;
    text-align: left;
}

.items-table th {
    background: #f8fafc;
    font-size: 11px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
}

.items-table td {
    border-top: 1px solid #f3f4f6;
    font-size: 14px;
}

.product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-image {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    object-fit: cover;
    background: #f3f4f6;
}

.product-name {
    font-weight: 500;
    color: #1a1a1a;
}

.product-sku {
    font-size: 12px;
    color: #666;
}

.price-input {
    width: 100px;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
}

.price-input:focus {
    outline: none;
    border-color: #00b207;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding: 16px 0;
    font-size: 18px;
    font-weight: 600;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: #00b207;
    color: white;
}

.btn-primary:hover {
    background: #009906;
}

.btn-secondary {
    background: #f3f4f6;
    color: #666;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: #fee2e2;
    color: #dc2626;
}

.btn-danger:hover {
    background: #fecaca;
}

.btn-block {
    width: 100%;
    justify-content: center;
}

.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #333;
    margin-bottom: 6px;
}

.form-input, .form-select {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #00b207;
}

.timeline {
    position: relative;
    padding-left: 24px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    padding-bottom: 16px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -24px;
    top: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #00b207;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #e5e7eb;
}

.timeline-content {
    font-size: 13px;
}

.timeline-status {
    font-weight: 600;
    color: #1a1a1a;
}

.timeline-note {
    color: #666;
    margin-top: 2px;
}

.timeline-date {
    font-size: 11px;
    color: #999;
    margin-top: 2px;
}

.alert {
    padding: 14px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success { background: #d1fae5; color: #065f46; }
.alert-error { background: #fee2e2; color: #991b1b; }
.alert-warning { background: #fef3c7; color: #92400e; }

.action-box {
    background: #f8fafc;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.action-box h4 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 12px;
}

.tier-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 12px;
}
.tier-badge.tier-1 { background: #fee2e2; color: #991b1b; }
.tier-badge.tier-2 { background: #fef3c7; color: #92400e; }
.tier-badge.tier-3 { background: #d1fae5; color: #065f46; }
.tier-badge.tier-4 { background: #dbeafe; color: #1e40af; }

.fee-breakdown {
    background: #f8fafc;
    border-radius: 8px;
    padding: 12px;
    margin: 12px 0;
}
.fee-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 12px;
    color: #555;
}
.fee-row.highlight {
    color: #00b207;
    font-weight: 500;
}

.tax-section {
    background: #f0fdf4;
    border-radius: 8px;
    padding: 10px 12px;
    margin: 8px 0;
}
.tax-row {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
    font-size: 12px;
    color: #666;
}

.summary-subtotal {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    border-top: 1px solid #e5e7eb;
    margin-top: 8px;
}

.summary-note {
    font-size: 12px;
    color: #666;
    padding: 12px;
    background: #fef3c7;
    border-radius: 8px;
    margin-top: 16px;
}
</style>

<!-- Breadcrumb & Header -->
<div class="breadcrumb">
    <a href="<?= url('admin/distribution') ?>">Distribution Requests</a>
    <span> / <?= htmlspecialchars($request['request_number'] ?? '') ?></span>
</div>

<div class="page-header">
    <div>
        <h1 style="font-size: 24px; font-weight: 600; color: #1a1a1a;"><?= htmlspecialchars($request['request_name'] ?? 'Unknown Request') ?></h1>
        <p style="font-size: 14px; color: #666; margin-top: 4px;"><?= htmlspecialchars($request['request_number'] ?? '') ?></p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?= url('admin/distribution') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<?php if ($flash = getFlash('success')): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>
<?php if ($flash = getFlash('error')): ?>
    <div class="alert alert-error"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="content-grid">
    <div class="main-column">
        <!-- Request Info -->
        <div class="card">
            <div class="status-header">
                <span class="badge badge-<?= $request['status'] ?? 'draft' ?>">
                    <?= ucwords(str_replace('_', ' ', $request['status'] ?? 'draft')) ?>
                </span>
                <span style="font-size: 14px; color: #666;">
                    Created <?= !empty($request['created_at']) ? date('M j, Y g:i A', strtotime($request['created_at'])) : 'N/A' ?>
                </span>
            </div>

            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <div class="info-label">Business</div>
                        <div class="info-value"><?= htmlspecialchars($request['company_name'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Contact</div>
                        <div class="info-value">
                            <?= htmlspecialchars(($request['first_name'] ?? '') . ' ' . ($request['last_name'] ?? '')) ?><br>
                            <span style="font-weight: 400; color: #666;"><?= htmlspecialchars($request['email'] ?? '') ?></span><br>
                            <span style="font-weight: 400; color: #666;"><?= htmlspecialchars($request['phone'] ?? '') ?></span>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <div class="info-label">Delivery Address</div>
                        <div class="info-value">
                            <?= htmlspecialchars($request['delivery_street'] ?? '') ?><br>
                            <?= htmlspecialchars($request['delivery_city'] ?? '') ?>, <?= htmlspecialchars($request['delivery_province'] ?? '') ?><br>
                            <?= htmlspecialchars($request['delivery_postal_code'] ?? '') ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Preferred Delivery</div>
                        <div class="info-value">
                            <?= !empty($request['preferred_delivery_date']) ? date('F j, Y', strtotime($request['preferred_delivery_date'])) : 'Not specified' ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($request['notes'])): ?>
                <div class="info-item" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #f3f4f6;">
                    <div class="info-label">Customer Notes</div>
                    <div class="info-value" style="font-weight: 400;"><?= nl2br(htmlspecialchars($request['notes'])) ?></div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ── Order Completion Timeline ── -->
        <?php
        $tlDeadline  = $request['order_deadline'] ?? null;
        $tlSubmitted = $request['submitted_at'] ?? null;
        $tlType      = $request['delivery_type'] ?? 'scheduled';
        $tlStatus    = $request['status'] ?? 'draft';
        $tlDone      = in_array($tlStatus, ['delivered','completed']);
        $tlActive    = $tlDeadline && !in_array($tlStatus, ['draft','cancelled']);

        if ($tlActive):
            $deadlineTs  = strtotime($tlDeadline);
            $submittedTs = $tlSubmitted ? strtotime($tlSubmitted) : null;
            $totalSecs   = ($submittedTs && $deadlineTs) ? max(1, $deadlineTs - $submittedTs) : 0;
            $nowTs       = time();
            $secsLeft    = $deadlineTs - $nowTs;
            $elapsed     = $submittedTs ? $nowTs - $submittedTs : 0;
            $pct         = $totalSecs > 0 ? min(100, round($elapsed / $totalSecs * 100)) : 0;

            $typeConfig = match($tlType) {
                'express'  => ['label' => '⚡ Express ASAP', 'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca'],
                'same_day' => ['label' => '☀️ Same Day',     'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'],
                default    => ['label' => '📅 Scheduled',    'color' => '#4f46e5', 'bg' => '#eef2ff', 'border' => '#c7d2fe'],
            };
            $tc = $typeConfig;
            $isOverdue = !$tlDone && $secsLeft < 0;
        ?>
        <div class="card" style="border:1.5px solid <?= $tc['border'] ?>;background:<?= $tc['bg'] ?>;padding:20px 24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
                <div style="font-weight:700;font-size:15px;color:<?= $tc['color'] ?>;">
                    <?= $tc['label'] ?> — Order Timeline
                </div>
                <?php if ($tlDone): ?>
                    <span style="background:#d1fae5;color:#059669;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;">✓ Completed</span>
                <?php elseif ($isOverdue): ?>
                    <span style="background:#dc2626;color:white;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;">⚠ Overdue</span>
                <?php else: ?>
                    <span id="adminTlBadge" style="background:<?= $tc['color'] ?>;color:white;padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;">In Progress</span>
                <?php endif; ?>
            </div>

            <!-- Progress bar with milestones -->
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;">
                <div style="text-align:center;flex:0 0 auto;">
                    <div style="width:28px;height:28px;border-radius:50%;background:<?= $tc['color'] ?>;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-play" style="color:white;font-size:10px;"></i>
                    </div>
                    <div style="font-size:10px;color:#6b7280;margin-top:3px;"><?= $tlSubmitted ? date('g:i A', $submittedTs) : '—' ?></div>
                </div>
                <div style="flex:1;height:8px;background:#e5e7eb;border-radius:4px;overflow:hidden;">
                    <div id="adminTlBar" style="height:100%;background:<?= $isOverdue ? '#dc2626' : $tc['color'] ?>;border-radius:4px;width:<?= $pct ?>%;transition:width 1s linear;"></div>
                </div>
                <div style="text-align:center;flex:0 0 auto;">
                    <div style="width:28px;height:28px;border-radius:50%;background:<?= $tlDone ? '#059669' : ($isOverdue ? '#dc2626' : '#e5e7eb') ?>;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-<?= $tlDone ? 'check' : ($isOverdue ? 'exclamation' : 'flag-checkered') ?>" style="color:<?= ($tlDone || $isOverdue) ? 'white' : '#9ca3af' ?>;font-size:10px;"></i>
                    </div>
                    <div style="font-size:10px;color:#6b7280;margin-top:3px;"><?= date('g:i A', $deadlineTs) ?></div>
                </div>
            </div>

            <!-- Stats row -->
            <div style="display:flex;gap:24px;flex-wrap:wrap;font-size:13px;">
                <div>
                    <span style="color:#6b7280;">Submitted:</span>
                    <strong style="margin-left:4px;"><?= $tlSubmitted ? date('M j, g:i A', $submittedTs) : '—' ?></strong>
                </div>
                <div>
                    <span style="color:#6b7280;">Deadline:</span>
                    <strong style="margin-left:4px;"><?= date('M j, g:i A', $deadlineTs) ?></strong>
                </div>
                <div>
                    <span style="color:#6b7280;">Time left:</span>
                    <strong id="adminTlCountdown" style="margin-left:4px;color:<?= $isOverdue ? '#dc2626' : $tc['color'] ?>;">
                        <?php if ($tlDone): ?>Completed
                        <?php elseif ($isOverdue): ?>Overdue by <?= gmdate('H:i:s', abs($secsLeft)) ?>
                        <?php else: echo gmdate('H:i:s', $secsLeft); ?>
                        <?php endif; ?>
                    </strong>
                </div>
            </div>

            <?php
            // ── Helper: format a duration between two timestamps ───────────
            $fmtMileDur = function(?string $fromTs, ?string $toTs): ?string {
                if (!$fromTs || !$toTs) return null;
                $secs = abs(strtotime($toTs) - strtotime($fromTs));
                if ($secs < 60)    return $secs . 's';
                if ($secs < 3600)  return round($secs / 60) . 'm';
                if ($secs < 86400) return round($secs / 3600, 1) . 'h';
                return round($secs / 86400, 1) . 'd';
            };

            // ── Fallback: find timestamp from status_history when column is NULL ──
            $statusHistoryTs = [];
            foreach ($statusHistory as $_sh) {
                $s = $_sh['new_status'] ?? '';
                if ($s && empty($statusHistoryTs[$s])) {
                    $statusHistoryTs[$s] = $_sh['created_at'];
                }
            }
            $tsOrHistory = function(?string $col, string ...$statuses) use ($statusHistoryTs): ?string {
                if (!empty($col)) return $col;
                foreach ($statuses as $s) {
                    if (!empty($statusHistoryTs[$s])) return $statusHistoryTs[$s];
                }
                return null;
            };

            // Resolve timestamps — use column if set, fall back to status_history
            $miProcurementAt = $tsOrHistory($request['procurement_started_at'] ?? null, 'procurement', 'processing');
            $miInTransitAt   = $tsOrHistory($request['in_transit_at'] ?? null, 'in_transit', 'ready');
            $miDeliveredAt   = $tsOrHistory($request['delivered_at'] ?? null, 'delivered', 'completed');
            $miPickedUpAt          = $daTiming['picked_up_at'] ?? null; // no status fallback for this one
            $miAssignedAt          = $daTiming['assigned_at'] ?? null;
            $miHeadingToSupplierAt = $daTiming['heading_to_supplier_at'] ?? null;
            $miEnRouteToCustomerAt = $daTiming['en_route_to_customer_at'] ?? null;

            // ── Build ordered milestone steps ──────────────────────────────
            // Each step: label, at (timestamp|null), done, icon, color
            // prevTs tracks the last completed timestamp so every step gets
            // a "duration since previous step" pill.
            $milestones = [];
            $prevMileTs = $request['submitted_at'] ?? $request['created_at'] ?? null;

            $addMile = function(string $label, ?string $at, bool $done, string $icon, string $color,
                                ?string $durFrom = null, string $durSuffix = '') use (&$milestones, &$prevMileTs, $fmtMileDur) {
                $from = $durFrom ?? $prevMileTs;
                $dur  = $fmtMileDur($from, $at);
                $milestones[] = compact('label','at','done','icon','color','dur','durSuffix');
                if ($done && $at) $prevMileTs = $at;
            };

            // 1. Request Approved
            $addMile('Request Approved',
                $request['approved_at'] ?? null,
                !empty($request['approved_at']),
                'fa-clipboard-check', '#0284c7',
                $request['submitted_at'] ?? $request['created_at'],
                'after submission'
            );

            // 2. Payment Received
            $addMile('Payment Received',
                $request['paid_at'] ?? null,
                !empty($request['paid_at']),
                'fa-credit-card', '#16a34a',
                $request['approved_at'] ?? null,
                'after approval'
            );

            // 3. PO Sent + Supplier response — one row per PO
            foreach ($linkedPOs as $lpo) {
                $supp = htmlspecialchars($lpo['supplier_company'] ?: $lpo['supplier_name'] ?: 'Supplier');
                $poSentAt = $lpo['created_at'] ?? null;

                // PO Sent row
                $addMile('PO Sent → ' . $supp,
                    $poSentAt,
                    !empty($poSentAt),
                    'fa-file-invoice', '#7c3aed',
                    $request['paid_at'] ?? null,
                    'after payment'
                );

                // Supplier response row
                if (!empty($lpo['supplier_accepted_at'])) {
                    $addMile($supp . ' Accepted',
                        $lpo['supplier_accepted_at'],
                        true,
                        'fa-store', '#16a34a',
                        $poSentAt,
                        'to respond'
                    );
                } elseif (!empty($lpo['supplier_declined_at'])) {
                    $addMile($supp . ' Declined',
                        $lpo['supplier_declined_at'],
                        true,
                        'fa-times-circle', '#dc2626',
                        $poSentAt,
                        'to respond'
                    );
                } else {
                    $waiting = $fmtMileDur($poSentAt, date('Y-m-d H:i:s'));
                    $milestones[] = [
                        'label'      => $supp . ' — Awaiting Confirmation',
                        'at'         => null,
                        'done'       => false,
                        'icon'       => 'fa-hourglass-half',
                        'color'      => '#d97706',
                        'dur'        => $waiting,
                        'durSuffix'  => 'waiting',
                    ];
                }
            }

            // 4. Procurement Started
            $addMile('Procurement Started',
                $miProcurementAt,
                !empty($miProcurementAt),
                'fa-boxes', '#d97706',
                null,
                'after PO confirmed'
            );

            // 5. Driver Assigned
            $driverMileName = trim(($daTiming['driver_first'] ?? '') . ' ' . ($daTiming['driver_last'] ?? '')) ?: null;
            $addMile('Driver Assigned' . ($driverMileName ? ' — ' . htmlspecialchars($driverMileName) : ''),
                $miAssignedAt,
                !empty($miAssignedAt),
                'fa-id-badge', '#0891b2',
                null,
                'after procurement'
            );

            // 6. Driver Heading to Suppliers
            // Use heading_to_supplier_at if available, fall back to in_transit_at
            $miHeadingTs = $miHeadingToSupplierAt ?? $miInTransitAt;
            $addMile('Driver Heading to Suppliers',
                $miHeadingTs,
                !empty($miHeadingTs),
                'fa-route', '#0891b2',
                $miAssignedAt,
                'after assigned'
            );

            // 7. Order Picked Up — only show if recorded OR order not yet delivered
            // (hide if null and already delivered — it was skipped in that flow)
            $skipPickup = empty($miPickedUpAt) && !empty($miDeliveredAt);
            if (!$skipPickup) {
                $addMile('Order Picked Up',
                    $miPickedUpAt,
                    !empty($miPickedUpAt),
                    'fa-box-open', '#0891b2',
                    $miHeadingTs,
                    'en route to suppliers'
                );
            }

            // 8. En Route to Customer
            $addMile('En Route to Customer',
                $miEnRouteToCustomerAt,
                !empty($miEnRouteToCustomerAt),
                'fa-truck', '#7c3aed',
                $miPickedUpAt ?? $miHeadingTs,
                'after pickup'
            );

            // 9. Delivered
            $addMile('Delivered',
                $miDeliveredAt,
                !empty($miDeliveredAt),
                'fa-check-double', '#16a34a',
                $miEnRouteToCustomerAt ?? $miPickedUpAt ?? $miHeadingTs,
                'transit time'
            );
            ?>

            <?php if (!empty($milestones)): ?>
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid <?= $tc['border'] ?>;">
                <div style="display:flex;flex-direction:column;gap:0;">
                    <?php foreach ($milestones as $mi): ?>
                    <div style="display:flex;align-items:center;gap:10px;padding:6px 0;border-bottom:1px solid <?= $tc['border'] ?>30;">
                        <div style="width:22px;height:22px;border-radius:50%;background:<?= $mi['done'] ? $mi['color'] : '#e5e7eb' ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas <?= $mi['icon'] ?>" style="color:<?= $mi['done'] ? 'white' : '#9ca3af' ?>;font-size:9px;"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <span style="font-size:12px;font-weight:<?= $mi['done'] ? '600' : '400' ?>;color:<?= $mi['done'] ? '#111' : '#b0b8c1' ?>;">
                                <?= $mi['label'] ?>
                            </span>
                            <?php if (!empty($mi['at'])): ?>
                                <span style="font-size:11px;font-weight:400;color:#b0b8c1;margin-left:5px;"><?= date('g:i A', strtotime($mi['at'])) ?></span>
                            <?php elseif (!$mi['done']): ?>
                                <span style="font-size:10px;color:#c9d0d8;margin-left:5px;font-style:italic;">pending</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($mi['dur'])): ?>
                        <div style="flex-shrink:0;font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;background:<?= $mi['color'] ?>18;color:<?= $mi['color'] ?>;white-space:nowrap;">
                            <?= $mi['dur'] ?><?= !empty($mi['durSuffix']) ? ' ' . $mi['durSuffix'] : '' ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!$tlDone && !$isOverdue): ?>
            <script>
            (function(){
                const deadline  = <?= $deadlineTs * 1000 ?>;
                const submitted = <?= ($submittedTs ?? $nowTs) * 1000 ?>;
                const total     = <?= $totalSecs * 1000 ?>;
                const color     = '<?= $tc['color'] ?>';
                const el    = document.getElementById('adminTlCountdown');
                const bar   = document.getElementById('adminTlBar');
                const badge = document.getElementById('adminTlBadge');
                if (!el) return;
                function tick() {
                    const now      = Date.now();
                    const secsLeft = Math.floor((deadline - now) / 1000);
                    const elapsed  = now - submitted;
                    const pct      = total > 0 ? Math.min(100, Math.round(elapsed / total * 100)) : 100;
                    if (bar) bar.style.width = pct + '%';
                    if (secsLeft <= 0) {
                        el.textContent = 'Overdue';
                        el.style.color = '#dc2626';
                        if (bar)   bar.style.background = '#dc2626';
                        if (badge) { badge.textContent = '⚠ Overdue'; badge.style.background = '#dc2626'; }
                        return;
                    }
                    const h = Math.floor(secsLeft / 3600);
                    const m = Math.floor((secsLeft % 3600) / 60);
                    const s = secsLeft % 60;
                    el.textContent = (h > 0 ? h + 'h ' : '') + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
                    if (pct >= 80) {
                        el.style.color = '#dc2626';
                        if (bar)   bar.style.background = '#dc2626';
                        if (badge) badge.style.background = '#dc2626';
                    }
                    setTimeout(tick, 1000);
                }
                tick();
            })();
            </script>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Catalog Items grouped by supplier -->
        <?php if (!empty($catalogItems)):
            $bySupplier = [];
            foreach ($catalogItems as $item) {
                $key = $item['supplier_company'] ?: $item['supplier_name'] ?: 'Unknown Supplier';
                $bySupplier[$key][] = $item;
            }
        ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-box"></i> Catalog Items</h3>
                <?php foreach ($bySupplier as $supplierName => $items): ?>
                    <div style="margin-bottom:20px;">
                        <div style="display:flex;align-items:center;gap:8px;padding:8px 0 10px;border-bottom:2px solid #e5e7eb;">
                            <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:#f0fdf4;border-radius:50%;flex-shrink:0;">
                                <i class="fas fa-store" style="color:#00b207;font-size:12px;"></i>
                            </span>
                            <span style="font-size:14px;font-weight:700;color:#111827;"><?= htmlspecialchars($supplierName) ?></span>
                            <span style="font-size:12px;color:#6b7280;">(<?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?>)</span>
                        </div>
                        <table class="items-table" style="margin-top:0;">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="product-cell">
                                                <img src="<?= !empty($item['image']) ? asset(str_starts_with($item['image'], 'uploads/') ? $item['image'] : 'uploads/supplier-products/' . $item['image']) : asset('images/logo.png') ?>"
                                                     alt="<?= htmlspecialchars($item['product_name'] ?? '') ?>" class="product-image"
                                                     onerror="this.src='<?= asset('images/logo.png') ?>'"
                                                     loading="lazy">
                                                <div>
                                                    <div class="product-name"><?= htmlspecialchars($item['product_name'] ?? '') ?></div>
                                                    <div class="product-sku">SKU: <?= htmlspecialchars($item['sku'] ?? '') ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= $item['quantity'] ?? 0 ?></td>
                                        <td>$<?= number_format($item['unit_price'] ?? 0, 2) ?></td>
                                        <td><strong>$<?= number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0), 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
                <div style="display:flex;justify-content:flex-end;padding:10px 0 4px;border-top:2px solid #e5e7eb;font-size:14px;">
                    <span style="font-weight:600;color:#374151;margin-right:24px;">Catalog Subtotal:</span>
                    <strong>$<?= number_format($catalogTotal ?? 0, 2) ?></strong>
                </div>
            </div>
        <?php endif; ?>

        <!-- Shopping List Items -->
        <?php if (!empty($shoppingItems)): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-list"></i> Shopping List Items</h3>

                <?php if (($request['status'] ?? '') === 'submitted'): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Please set actual prices for all shopping list items before creating an invoice.
                    </div>
                    <form method="POST" action="<?= url('admin/distribution/update-prices') ?>">
                        <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?? 0 ?>">
                <?php endif; ?>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Est. Price</th>
                            <th>Actual Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shoppingItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="product-name"><?= htmlspecialchars($item['item_description'] ?? '') ?></div>
                                    <?php if (!empty($item['notes'])): ?>
                                        <div class="product-sku"><?= htmlspecialchars($item['notes']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $item['quantity'] ?? 0 ?></td>
                                <td><?= htmlspecialchars(ucfirst($item['unit'] ?? '')) ?></td>
                                <td><?= !empty($item['estimated_price']) ? '$' . number_format($item['estimated_price'], 2) : '--' ?></td>
                                <td>
                                    <?php if (($request['status'] ?? '') === 'submitted'): ?>
                                        <input type="number" step="0.01" min="0" name="prices[<?= $item['id'] ?>]"
                                               value="<?= $item['unit_price'] ?? '' ?>"
                                               class="price-input" placeholder="0.00">
                                    <?php else: ?>
                                        <?php if (!empty($item['unit_price'])): ?>
                                            <strong>$<?= number_format($item['unit_price'], 2) ?></strong>
                                        <?php else: ?>
                                            <span style="color: #999;">Pending</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php if (($shoppingTotal ?? 0) > 0): ?>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right; font-weight: 600;">Shopping Subtotal:</td>
                                <td><strong>$<?= number_format($shoppingTotal ?? 0, 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>

                <?php if (($request['status'] ?? '') === 'submitted'): ?>
                        <button type="submit" class="btn btn-primary" style="margin-top: 16px;">
                            <i class="fas fa-save"></i> Save Prices
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="sidebar-column">
        <!-- Summary -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-receipt"></i> Summary</h3>

            <?php if (!empty($invoice)): ?>
                <!-- Invoice exists - show invoice totals -->
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>$<?= number_format($invoice['subtotal'] ?? 0, 2) ?></span>
                </div>
                <?php if (($invoice['tax_amount'] ?? 0) > 0): ?>
                    <div class="summary-item">
                        <span>Tax (<?= $invoice['tax_rate'] ?? 0 ?>%)</span>
                        <span>$<?= number_format($invoice['tax_amount'] ?? 0, 2) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (($invoice['delivery_fee'] ?? 0) > 0): ?>
                    <div class="summary-item">
                        <span>Delivery Fee</span>
                        <span>$<?= number_format($invoice['delivery_fee'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <div class="summary-total">
                    <span>Total</span>
                    <span>$<?= number_format($invoice['total_amount'] ?? 0, 2) ?></span>
                </div>
            <?php else: ?>
                <!-- No invoice - show detailed breakdown -->

                <!-- Tier Badge -->
                <?php if (isset($summary['tier'])): ?>
                    <span class="tier-badge tier-<?= $summary['tier'] ?>">
                        <i class="fas fa-layer-group"></i> Tier <?= $summary['tier'] ?> - <?= htmlspecialchars($summary['tier_vehicle']) ?>
                    </span>
                <?php endif; ?>

                <!-- Items Summary -->
                <?php if (($catalogTotal ?? 0) > 0): ?>
                    <div class="summary-item">
                        <span>Catalog Items (<?= count($catalogItems ?? []) ?>)</span>
                        <span>$<?= number_format($catalogTotal ?? 0, 2) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($shoppingItems)): ?>
                    <div class="summary-item">
                        <span>Shopping Items (<?= count($shoppingItems) ?>)</span>
                        <span><?= ($shoppingEstimate ?? 0) > 0 ? '$' . number_format($shoppingEstimate, 2) : 'TBD' ?></span>
                    </div>
                <?php endif; ?>

                <!-- Fee Breakdown -->
                <?php if (isset($summary) && (($summary['items_total'] ?? 0) > 0 || ($catalogTotal ?? 0) > 0)): ?>
                    <div class="fee-breakdown">
                        <div class="fee-row">
                            <span>Items Total</span>
                            <span>$<?= number_format($summary['items_total'] ?: $catalogTotal, 2) ?><?= !empty($shoppingItems) ? '+' : '' ?></span>
                        </div>
                        <div class="fee-row">
                            <span>Service Fee (<?= $summary['service_fee_percent'] ?>%)</span>
                            <span>$<?= number_format($summary['service_fee'] ?? 0, 2) ?></span>
                        </div>
                        <div class="fee-row">
                            <span>Handling (<?= number_format($summary['total_weight_kg'] ?? 0, 1) ?> kg × $0.20/kg)</span>
                            <span>$<?= number_format($summary['handling_fee'] ?? 0, 2) ?></span>
                        </div>
                        <div class="fee-row">
                            <span>Delivery (<?= ($summary['delivery_distance'] ?? 0) <= ($summary['free_delivery_km'] ?? 15) ? 'Free ≤' . ($summary['free_delivery_km'] ?? 15) . 'km' : (($summary['delivery_distance'] ?? 0) - ($summary['free_delivery_km'] ?? 15)) . 'km × $' . number_format($summary['per_km_rate'] ?? 0, 2) ?>)</span>
                            <span>$<?= number_format($summary['delivery_fee'] ?? 0, 2) ?></span>
                        </div>
                    </div>

                    <!-- Subtotal -->
                    <div class="summary-subtotal">
                        <span>Subtotal</span>
                        <span>$<?= number_format($summary['subtotal'] ?? 0, 2) ?><?= !empty($shoppingItems) ? '+' : '' ?></span>
                    </div>

                    <!-- Tax Section -->
                    <div class="tax-section">
                        <div class="tax-row">
                            <span>GST (5%)</span>
                            <span>$<?= number_format($summary['gst_amount'] ?? 0, 2) ?></span>
                        </div>
                        <div class="tax-row">
                            <span>QST (9.975%)</span>
                            <span>$<?= number_format($summary['qst_amount'] ?? 0, 2) ?></span>
                        </div>
                    </div>

                    <?php if (!empty($summary['tip_amount']) && $summary['tip_amount'] > 0): ?>
                    <div class="fee-breakdown" style="margin-top: 8px;">
                        <div class="fee-row">
                            <span>Tip <?= (int)($summary['tip_percentage'] ?? 0) > 0 ? '(' . (int)$summary['tip_percentage'] . '%)' : '(Custom)' ?></span>
                            <span>$<?= number_format($summary['tip_amount'], 2) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Total -->
                    <div class="summary-total">
                        <span>Estimated Total</span>
                        <span>$<?= number_format($summary['total_amount'] ?? 0, 2) ?><?= !empty($shoppingItems) ? '+' : '' ?></span>
                    </div>
                <?php else: ?>
                    <div class="summary-total">
                        <span>Estimated Total</span>
                        <span>$<?= number_format(($catalogTotal ?? 0) + ($shoppingEstimate ?? 0), 2) ?><?= !empty($shoppingItems) ? '+' : '' ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($shoppingItems)): ?>
                    <div class="summary-note">
                        <i class="fas fa-info-circle"></i>
                        Shopping list items will be quoted after review. Final total may vary.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Status-Based Actions -->
        <?php $currentStatus = $request['status'] ?? 'draft'; ?>

        <!-- PENDING/SUBMITTED: Approve or Cancel -->
        <?php if (in_array($currentStatus, ['pending', 'submitted'])): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-clipboard-check"></i> Review Request</h3>
                <p style="font-size: 13px; color: #666; margin-bottom: 16px;">
                    Review the items and delivery details. Approve if you can fulfill this request, or cancel with a reason.
                </p>

                <!-- Approve Form -->
                <form method="POST" action="<?= url('admin/distribution/approve') ?>" style="margin-bottom: 16px;" id="approveForm">
                    <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?? 0 ?>">

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label">Delivery Type <span style="color:#dc2626;">*</span></label>
                        <select name="delivery_type" class="form-input" id="deliveryTypeSelect" onchange="toggleScheduleFields()" required>
                            <option value="scheduled" <?= ($request['delivery_type'] ?? 'scheduled') === 'scheduled' ? 'selected' : '' ?>>📅 Scheduled Delivery</option>
                            <option value="same_day" <?= ($request['delivery_type'] ?? '') === 'same_day' ? 'selected' : '' ?>>☀️ Same Day</option>
                            <option value="express" <?= ($request['delivery_type'] ?? '') === 'express' ? 'selected' : '' ?>>⚡ Express ASAP</option>
                        </select>
                    </div>

                    <div id="scheduleFields" style="display:none; background:#f0f9ff; border-radius:8px; padding:12px; margin-bottom:16px; border:1px solid #bae6fd;">
                        <div class="form-group" style="margin-bottom:10px;" id="scheduleDateRow">
                            <label class="form-label" style="font-size:13px;">Delivery Date</label>
                            <input type="date" name="scheduled_date" id="adminScheduledDate" class="form-input" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                        <div id="sameDayDateNote" style="display:none;background:#f0fdf4;border:1px solid #86efac;border-radius:6px;padding:8px 12px;font-size:12px;color:#166534;margin-bottom:10px;">
                            ☀️ <strong>Same Day</strong> — delivery date is automatically set to today (<?= date('Y-m-d') ?>).
                        </div>
                        <div style="display:flex; gap:12px;">
                            <div class="form-group" style="flex:1;margin-bottom:0;">
                                <label class="form-label" style="font-size:13px;">Window Start</label>
                                <input type="time" name="scheduled_time_from" class="form-input" value="09:00">
                            </div>
                            <div class="form-group" style="flex:1;margin-bottom:0;">
                                <label class="form-label" style="font-size:13px;">Window End</label>
                                <input type="time" name="scheduled_time_to" class="form-input" value="17:00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:14px;">
                        <label class="form-label">Approval Notes (optional)</label>
                        <textarea name="notes" class="form-input" rows="2" placeholder="Any notes about the approval..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-check-circle"></i> Approve & Notify Suppliers
                    </button>
                </form>
                <script>
                function toggleScheduleFields() {
                    const type = document.getElementById('deliveryTypeSelect').value;
                    const isSameDay   = type === 'same_day';
                    const isScheduled = type === 'scheduled';
                    document.getElementById('scheduleFields').style.display     = (isScheduled || isSameDay) ? 'block' : 'none';
                    document.getElementById('scheduleDateRow').style.display    = isScheduled ? 'block' : 'none';
                    document.getElementById('sameDayDateNote').style.display    = isSameDay   ? 'block' : 'none';
                    // For same_day, force the hidden date to today server-side; set a read-only value for display
                    const dateInput = document.getElementById('adminScheduledDate');
                    if (isSameDay) {
                        dateInput.value = '<?= date('Y-m-d') ?>';
                    } else if (isScheduled && !dateInput.value) {
                        dateInput.value = '';
                    }
                }
                toggleScheduleFields();
                </script>

                <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">

                <!-- Cancel Form -->
                <form method="POST" action="<?= url('admin/distribution/cancel') ?>">
                    <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?? 0 ?>">
                    <div class="form-group">
                        <label class="form-label">Cancellation Reason <span style="color: #dc2626;">*</span></label>
                        <textarea name="cancellation_reason" class="form-input" rows="2" placeholder="Explain why this request cannot be fulfilled..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to cancel this request?');">
                        <i class="fas fa-times-circle"></i> Cancel Request
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- APPROVED: Awaiting supplier confirmation -->
        <?php if ($currentStatus === 'approved'): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-hourglass-half" style="color:#4f46e5;"></i> Awaiting Supplier Confirmation</h3>
                <div style="background:#e0e7ff;border-radius:8px;padding:14px;font-size:13px;color:#3730a3;margin-bottom:14px;">
                    <i class="fas fa-spinner fa-spin"></i>
                    Purchase orders have been sent to suppliers. Awaiting confirmation from all parties.<br>
                    <?php
                        $confirmationType = $request['delivery_type'] ?? 'scheduled';
                        $typeLabel = match($confirmationType) {
                            'express'  => '⚡ Express ASAP — delivered within 2 hours',
                            'same_day' => '☀️ Same Day — delivered during business hours today',
                            default    => '📅 Scheduled — delivered on chosen date & window',
                        };
                        echo '<strong>Delivery type:</strong> ' . $typeLabel;
                    ?>
                </div>

                <?php if (!empty($linkedPOs)): ?>
                    <?php
                        $poStatusColors = [
                            'sent'             => ['bg' => '#f3f4f6', 'color' => '#374151', 'icon' => 'clock',          'label' => 'Awaiting'],
                            'accepted'         => ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'check-circle',   'label' => 'Confirmed'],
                            'preparing'        => ['bg' => '#e0e7ff', 'color' => '#3730a3', 'icon' => 'box-open',       'label' => 'Preparing'],
                            'ready_for_pickup' => ['bg' => '#fef3c7', 'color' => '#92400e', 'icon' => 'truck',          'label' => 'Ready'],
                            'declined'         => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'times-circle',   'label' => 'Declined'],
                            'cancelled'        => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'ban',            'label' => 'Cancelled'],
                        ];
                    ?>
                    <?php foreach ($linkedPOs as $lpo): ?>
                        <?php
                            $poS = $lpo['status'] ?? 'sent';
                            $sc  = $poStatusColors[$poS] ?? ['bg' => '#f3f4f6', 'color' => '#374151', 'icon' => 'circle', 'label' => ucfirst($poS)];
                        ?>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;margin-bottom:8px;border-radius:8px;background:<?= $sc['bg'] ?>;border:1px solid rgba(0,0,0,0.06);">
                            <div style="min-width:0;flex:1;">
                                <div style="font-weight:600;font-size:13px;color:#111827;"><?= htmlspecialchars($lpo['supplier_company'] ?: $lpo['supplier_name'] ?: 'Supplier') ?></div>
                                <div style="font-size:12px;color:#6b7280;">PO #<?= htmlspecialchars($lpo['po_number']) ?></div>
                            </div>
                            <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:<?= $sc['color'] ?>;flex-shrink:0;margin-left:10px;">
                                <i class="fas fa-<?= $sc['icon'] ?>"></i> <?= $sc['label'] ?>
                            </span>
                        </div>

                        <?php if (in_array($poS, ['declined', 'cancelled'])): ?>
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px 14px;margin-bottom:10px;font-size:13px;color:#991b1b;">
                            <i class="fas fa-exclamation-triangle" style="margin-right:6px;"></i>
                            <strong><?= htmlspecialchars($lpo['supplier_company'] ?: $lpo['supplier_name'] ?: 'Supplier') ?></strong> declined PO #<?= htmlspecialchars($lpo['po_number']) ?>.
                            You need to reassign this PO to another supplier or cancel the request.
                            <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">
                                <a href="<?= url('admin/purchase-orders/view?id=' . $lpo['id']) ?>"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:white;border:1px solid #dc2626;border-radius:6px;color:#dc2626;text-decoration:none;font-size:12px;font-weight:600;">
                                    <i class="fas fa-eye"></i> View PO
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- AWAITING PAYMENT: All suppliers confirmed -->
        <?php if (in_array($currentStatus, ['awaiting_payment', 'quoted', 'pending_payment'])): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-clock"></i> Awaiting Payment</h3>

                <div style="background: #fef3c7; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                    <p style="font-size: 13px; color: #92400e; margin: 0;">
                        <i class="fas fa-hourglass-half"></i>
                        Payment link sent to customer.<br>
                        <?php if (!empty($request['payment_link_expires_at'])): ?>
                            <strong>Expires:</strong> <?= date('M j, Y g:i A', strtotime($request['payment_link_expires_at'])) ?>
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Resend Payment Link -->
                <form method="POST" action="<?= url('admin/distribution/resend-payment-link') ?>" style="margin-bottom: 16px;">
                    <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?? 0 ?>">
                    <button type="submit" class="btn btn-secondary btn-block">
                        <i class="fas fa-paper-plane"></i> Resend Payment Link
                    </button>
                </form>

                <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">

                <!-- Cancel Form -->
                <form method="POST" action="<?= url('admin/distribution/cancel') ?>">
                    <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?? 0 ?>">
                    <div class="form-group">
                        <label class="form-label">Cancellation Reason <span style="color: #dc2626;">*</span></label>
                        <textarea name="cancellation_reason" class="form-input" rows="2" placeholder="Reason for cancellation..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to cancel this request?');">
                        <i class="fas fa-times-circle"></i> Cancel Request
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- PAID: Pay Suppliers -->
        <?php if ($currentStatus === 'paid'): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> Pay Suppliers</h3>

                <div style="background:#d1fae5;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
                    <p style="font-size:13px;color:#065f46;margin:0;">
                        <i class="fas fa-check-circle"></i>
                        <strong>Business payment received</strong><?php if (!empty($request['paid_at'])): ?> on <?= date('M j, Y g:i A', strtotime($request['paid_at'])) ?><?php endif; ?><br>
                        Send payment to each supplier and mark them below. Suppliers will be notified and their invoice updated automatically.
                    </p>
                </div>

                <?php if (!empty($linkedPOs)): ?>
                    <?php foreach ($linkedPOs as $lpo): ?>
                    <div id="po-row-<?= $lpo['id'] ?>" style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;margin-bottom:8px;border-radius:8px;border:1px solid <?= $lpo['admin_paid_at'] ? '#d1fae5' : '#e5e7eb' ?>;background:<?= $lpo['admin_paid_at'] ? '#f0fdf4' : '#fafafa' ?>;">
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;font-size:13px;color:#111827;"><?= htmlspecialchars($lpo['supplier_company'] ?: $lpo['supplier_name'] ?: 'Supplier') ?></div>
                            <div style="font-size:12px;color:#6b7280;margin-top:2px;">
                                PO #<?= htmlspecialchars($lpo['po_number']) ?> &nbsp;·&nbsp; <?= $lpo['item_count'] ?> item<?= $lpo['item_count'] != 1 ? 's' : '' ?>
                                &nbsp;·&nbsp; <strong>$<?= number_format((float)$lpo['total_amount'], 2) ?> CAD</strong>
                            </div>
                        </div>
                        <div style="margin-left:12px;flex-shrink:0;">
                            <?php if ($lpo['admin_paid_at']): ?>
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#059669;font-weight:600;">
                                    <i class="fas fa-check-circle"></i> Paid <?= date('M j', strtotime($lpo['admin_paid_at'])) ?>
                                </span>
                            <?php else: ?>
                                <button
                                    onclick="openPayModal(<?= $lpo['id'] ?>, <?= $request['id'] ?>, '<?= htmlspecialchars(addslashes($lpo['supplier_company'] ?: $lpo['supplier_name'] ?: 'Supplier')) ?>', '<?= htmlspecialchars($lpo['po_number']) ?>', <?= (float)$lpo['total_amount'] ?>)"
                                    id="pay-btn-<?= $lpo['id'] ?>"
                                    class="btn btn-sm"
                                    style="background:#10b981;color:white;border:none;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                                    <i class="fas fa-money-bill"></i> Mark as Paid
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#6b7280;font-size:13px;">No supplier POs linked yet.</p>
                <?php endif; ?>
            </div>

            <!-- Payment modal -->
            <div id="supplierPayModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
                <div style="background:white;border-radius:12px;padding:28px;width:360px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
                    <h3 style="margin:0 0 4px;font-size:16px;color:#111827;">Confirm Supplier Payment</h3>
                    <p id="spModalSubtitle" style="margin:0 0 20px;font-size:13px;color:#6b7280;"></p>
                    <div style="margin-bottom:14px;">
                        <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">Payment Method</label>
                        <select id="spMethodSelect" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                            <option value="interac">Interac e-Transfer</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">Reference / Confirmation # <span style="font-weight:400;color:#9ca3af;">(optional)</span></label>
                        <input type="text" id="spRefInput" placeholder="e.g. e-Transfer confirmation number" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;box-sizing:border-box;">
                    </div>
                    <div style="display:flex;gap:10px;">
                        <button onclick="closePayModal()" style="flex:1;padding:9px;border:1px solid #d1d5db;background:white;border-radius:6px;font-size:13px;cursor:pointer;color:#374151;">Cancel</button>
                        <button onclick="submitPayment()" id="spConfirmBtn" style="flex:1;padding:9px;background:#10b981;color:white;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">Confirm Payment</button>
                    </div>
                </div>
            </div>

            <script>
            var _spPoId = null, _spDrId = null;

            function openPayModal(poId, drId, supplier, poNum, amount) {
                _spPoId = poId; _spDrId = drId;
                document.getElementById('spModalSubtitle').textContent =
                    supplier + ' · PO #' + poNum + ' · $' + amount.toFixed(2) + ' CAD';
                document.getElementById('spMethodSelect').value = 'interac';
                document.getElementById('spRefInput').value = '';
                var m = document.getElementById('supplierPayModal');
                m.style.display = 'flex';
            }

            function closePayModal() {
                document.getElementById('supplierPayModal').style.display = 'none';
                _spPoId = null; _spDrId = null;
                var confirmBtn = document.getElementById('spConfirmBtn');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm Payment';
            }

            function submitPayment() {
                if (!_spPoId || !_spDrId) return;
                var btn = document.getElementById('pay-btn-' + _spPoId);
                var confirmBtn = document.getElementById('spConfirmBtn');
                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Saving…';
                fetch('<?= url('admin/distribution/mark-supplier-paid') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        _csrf_token: '<?= generateCsrfToken() ?>',
                        po_id: _spPoId,
                        distribution_request_id: _spDrId,
                        payment_method: document.getElementById('spMethodSelect').value,
                        reference: document.getElementById('spRefInput').value
                    })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    closePayModal();
                    if (data.success) {
                        var row = document.getElementById('po-row-' + _spPoId);
                        if (row) {
                            row.style.background = '#f0fdf4';
                            row.style.borderColor = '#d1fae5';
                            var d = new Date();
                            var mon = d.toLocaleString('en', {month:'short'});
                            row.querySelector('div[style*="margin-left"]').innerHTML =
                                '<span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#059669;font-weight:600;"><i class="fas fa-check-circle"></i> Paid ' + mon + ' ' + d.getDate() + (data.payment_number ? ' · ' + data.payment_number : '') + '</span>';
                        }
                        if (btn) { btn.style.display = 'none'; }
                        if (data.advanced_to_processing) {
                            setTimeout(function() { location.reload(); }, 900);
                        }
                    } else {
                        if (btn) { btn.disabled = false; }
                        confirmBtn.disabled = false;
                        confirmBtn.textContent = 'Confirm Payment';
                        alert(data.error || 'Error saving payment.');
                    }
                })
                .catch(function() {
                    closePayModal();
                    if (btn) { btn.disabled = false; }
                    alert('Network error. Please try again.');
                });
            }
            </script>
        <?php endif; ?>

        <!-- PROCUREMENT/PROCESSING: Mark In Transit -->
        <?php if (in_array($currentStatus, ['procurement', 'processing'])): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-boxes"></i> Procurement in Progress</h3>

                <div style="background: #e0e7ff; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                    <p style="font-size: 13px; color: #4f46e5; margin: 0;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <strong>Purchasing items from suppliers...</strong><br>
                        <?php if (!empty($request['procurement_started_at'])): ?>
                            Started <?= date('M j, Y g:i A', strtotime($request['procurement_started_at'])) ?>
                        <?php endif; ?>
                    </p>
                </div>

                <?php if (!empty($linkedPOs)): ?>
                <div style="background:#fef3c7;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#92400e;">
                    <i class="fas fa-file-invoice" style="margin-right:5px;"></i>
                    <?php $draftPos = array_filter($linkedPOs, fn($p) => $p['status'] === 'draft'); ?>
                    <?php if (count($draftPos) > 0): ?>
                        <strong><?= count($draftPos) ?> PO(s) still draft</strong> — send them to suppliers before marking in transit.
                    <?php else: ?>
                        All linked POs have been sent to suppliers.
                    <?php endif; ?>
                    <div style="margin-top:6px;">
                        <?php foreach ($linkedPOs as $lpo): ?>
                        <a href="<?= url('admin/purchase-orders/view?id=' . $lpo['id']) ?>"
                           style="display:inline-block;margin:3px 4px 0 0;padding:3px 10px;background:white;border:1px solid #d97706;border-radius:4px;color:#92400e;text-decoration:none;font-size:12px;">
                            <?= htmlspecialchars($lpo['po_number']) ?>
                            <span style="color:#999;">(<?= $lpo['status'] ?>)</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('admin/distribution/mark-in-transit') ?>">
                    <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?? 0 ?>">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-truck"></i> Mark as In Transit
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- READY: Driver assigned, heading to suppliers -->
        <?php if ($currentStatus === 'ready'): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-motorcycle"></i> Driver En Route to Suppliers</h3>
                <div style="background:#cffafe;border-radius:8px;padding:14px;font-size:13px;color:#0e7490;">
                    <i class="fas fa-route"></i>
                    <strong>Driver assigned</strong> and heading to collect goods from suppliers.<br>
                    This will automatically advance to <strong>In Transit</strong> once all items are picked up.
                </div>
            </div>
        <?php endif; ?>

        <!-- IN TRANSIT: Mark Delivered -->
        <?php if ($currentStatus === 'in_transit'): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-truck"></i> In Transit</h3>

                <div style="background: #dbeafe; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                    <p style="font-size: 13px; color: #1e40af; margin: 0;">
                        <i class="fas fa-shipping-fast"></i>
                        <strong>En route to customer</strong><br>
                        <?php if (!empty($request['in_transit_at'])): ?>
                            Departed <?= date('M j, Y g:i A', strtotime($request['in_transit_at'])) ?>
                        <?php endif; ?>
                    </p>
                </div>

                <form method="POST" action="<?= url('admin/distribution/mark-delivered') ?>">
                    <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?? 0 ?>">
                    <div class="form-group">
                        <label class="form-label">Received By</label>
                        <input type="text" name="confirmed_by" class="form-input" placeholder="Name of person who received delivery">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-check-double"></i> Mark as Delivered
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- DELIVERED/COMPLETED: Complete -->
        <?php if (in_array($currentStatus, ['delivered', 'completed'])): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-check-circle" style="color: #00b207;"></i> Delivered</h3>
                <div style="background: #d1fae5; border-radius: 8px; padding: 16px; text-align: center;">
                    <p style="font-size: 16px; color: #065f46; margin: 0; font-weight: 600;">
                        Order Complete!
                    </p>
                    <?php if (!empty($request['delivered_at'])): ?>
                        <p style="font-size: 12px; color: #666; margin: 8px 0 0;">
                            Delivered on <?= date('F j, Y g:i A', strtotime($request['delivered_at'])) ?>
                            <?php if (!empty($request['delivery_confirmed_by'])): ?>
                                <br>Received by: <?= htmlspecialchars($request['delivery_confirmed_by']) ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- CANCELLED: Show Reason -->
        <?php if ($currentStatus === 'cancelled'): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-ban" style="color: #dc2626;"></i> Cancelled</h3>
                <div style="background: #fee2e2; border-radius: 8px; padding: 16px;">
                    <?php if (!empty($request['cancellation_reason'])): ?>
                        <p style="font-size: 13px; color: #991b1b; margin: 0;">
                            <strong>Reason:</strong><br>
                            <?= nl2br(htmlspecialchars($request['cancellation_reason'])) ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($request['cancelled_at'])): ?>
                        <p style="font-size: 12px; color: #666; margin: 8px 0 0;">
                            Cancelled on <?= date('F j, Y g:i A', strtotime($request['cancelled_at'])) ?>
                            <?php if (!empty($request['cancelled_by'])): ?>
                                by <?= ucfirst($request['cancelled_by']) ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Documents (shown for paid and later statuses) -->
        <?php if (in_array($currentStatus, ['paid', 'procurement', 'processing', 'in_transit', 'ready', 'delivered', 'completed'])): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-file-pdf"></i> Documents</h3>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="<?= url('distribution/documents/invoice?id=' . ($request['id'] ?? 0)) ?>" class="btn btn-secondary btn-block" target="_blank">
                        <i class="fas fa-file-invoice"></i> Download Invoice
                    </a>
                    <a href="<?= url('distribution/documents/purchase-order?id=' . ($request['id'] ?? 0)) ?>" class="btn btn-secondary btn-block" target="_blank">
                        <i class="fas fa-file-alt"></i> Download Purchase Order
                    </a>
                    <a href="<?= url('distribution/documents/sales-order?id=' . ($request['id'] ?? 0)) ?>" class="btn btn-secondary btn-block" target="_blank">
                        <i class="fas fa-clipboard-list"></i> Download Sales Order
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Linked Purchase Orders -->
        <?php if (!empty($linkedPOs)): ?>
        <div class="card">
            <h3 class="card-title"><i class="fas fa-file-invoice"></i> Purchase Orders</h3>
            <?php foreach ($linkedPOs as $lpo): ?>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f3f4f6;">
                <div style="min-width:0;">
                    <a href="<?= url('admin/purchase-orders/view?id=' . $lpo['id']) ?>"
                       style="font-weight:600;color:#00b207;font-size:13px;text-decoration:none;">
                        <?= htmlspecialchars($lpo['po_number']) ?>
                    </a>
                    <div style="font-size:11px;color:#888;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <?= htmlspecialchars($lpo['supplier_company'] ?: $lpo['supplier_name']) ?>
                        &middot; <?= $lpo['item_count'] ?> item(s)
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;margin-left:8px;">
                    <span class="badge badge-<?= $lpo['status'] ?>" style="font-size:10px;">
                        <?= ucfirst($lpo['status']) ?>
                    </span>
                    <div style="font-size:11px;color:#444;margin-top:2px;">
                        $<?= number_format($lpo['total_amount'], 2) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <div style="margin-top:10px;">
                <a href="<?= url('admin/purchase-orders') ?>" style="font-size:12px;color:#00b207;">
                    View all purchase orders &rarr;
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Driver Assignment (for delivery-relevant statuses) -->
        <?php if (in_array($currentStatus, ['paid', 'processing', 'ready', 'in_transit']) && !empty($availableDrivers)): ?>
            <div class="card">
                <h3 class="card-title"><i class="fas fa-motorcycle"></i> Delivery Driver</h3>

                <?php if (!empty($deliveryAssignment)): ?>
                    <!-- Currently assigned driver -->
                    <div style="background: #d1fae5; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #00b207; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                <?= strtoupper(substr($deliveryAssignment['driver_first_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #065f46;">
                                    <?= htmlspecialchars(($deliveryAssignment['driver_first_name'] ?? '') . ' ' . ($deliveryAssignment['driver_last_name'] ?? '')) ?>
                                </div>
                                <div style="font-size: 12px; color: #666;">
                                    <?= htmlspecialchars($deliveryAssignment['driver_phone'] ?? '') ?>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 12px; font-size: 12px;">
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 12px; background: white; color: #065f46; font-weight: 600;">
                                <?= ucwords(str_replace('_', ' ', $deliveryAssignment['status'] ?? 'assigned')) ?>
                            </span>
                            <?php if (!empty($deliveryAssignment['assigned_at'])): ?>
                                <span style="color: #666; margin-left: 8px;">
                                    Assigned <?= date('M j, g:i A', strtotime($deliveryAssignment['assigned_at'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p style="font-size: 12px; color: #666; margin-bottom: 12px;">Reassign to a different driver:</p>
                <?php else: ?>
                    <div style="background: #fef3c7; border-radius: 8px; padding: 12px; margin-bottom: 16px; font-size: 13px; color: #92400e;">
                        <i class="fas fa-info-circle"></i> No driver assigned yet. Assign a driver to handle the delivery.
                    </div>
                <?php endif; ?>

                <div style="display: flex; gap: 8px;">
                    <select id="distDriverSelect" class="form-select" style="flex: 1;">
                        <option value="">Select driver...</option>
                        <?php foreach ($availableDrivers as $driver): ?>
                            <option value="<?= $driver['id'] ?>"
                                <?= (!empty($deliveryAssignment) && ($deliveryAssignment['driver_id'] ?? 0) == $driver['id']) ? 'disabled' : '' ?>>
                                <?= htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']) ?>
                                (<?= ucfirst($driver['availability_status'] ?? 'offline') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" id="assignDriverBtn" class="btn btn-primary" onclick="assignDistributionDriver()">
                        <i class="fas fa-user-check"></i> Assign
                    </button>
                </div>
                <div id="assignDriverMsg" style="display: none; margin-top: 8px; font-size: 13px; padding: 8px 12px; border-radius: 6px;"></div>
            </div>

            <script>
            function assignDistributionDriver() {
                const driverId = document.getElementById('distDriverSelect').value;
                if (!driverId) {
                    alert('Please select a driver');
                    return;
                }

                const btn = document.getElementById('assignDriverBtn');
                const msg = document.getElementById('assignDriverMsg');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';

                fetch('<?= url("admin/delivery/assign-distribution-driver") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        distribution_request_id: <?= $request['id'] ?? 0 ?>,
                        driver_id: parseInt(driverId)
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        msg.style.display = 'block';
                        msg.style.background = '#d1fae5';
                        msg.style.color = '#065f46';
                        msg.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        msg.style.display = 'block';
                        msg.style.background = '#fee2e2';
                        msg.style.color = '#991b1b';
                        msg.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.error || 'Assignment failed');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-user-check"></i> Assign';
                    }
                })
                .catch(() => {
                    msg.style.display = 'block';
                    msg.style.background = '#fee2e2';
                    msg.style.color = '#991b1b';
                    msg.innerHTML = '<i class="fas fa-exclamation-circle"></i> Network error';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-user-check"></i> Assign';
                });
            }
            </script>
        <?php endif; ?>

        <!-- Activity History -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-history"></i> Activity</h3>
            <?php
            // ── Helper: format duration between two timestamps ──────────────
            $fmtDur = function(?string $fromTs, ?string $toTs): ?string {
                if (!$fromTs || !$toTs) return null;
                $secs = abs(strtotime($toTs) - strtotime($fromTs));
                if ($secs < 60)   return $secs . 's';
                if ($secs < 3600) return round($secs/60) . 'm';
                if ($secs < 86400) return round($secs/3600, 1) . 'h';
                return round($secs/86400, 1) . 'd';
            };

            // ── Build chronological event list ──────────────────────────────
            $tlEvents = [];

            // 1. Request submitted
            if (!empty($request['created_at'])) {
                $tlEvents[] = [
                    'ts'    => strtotime($request['created_at']),
                    'time'  => $request['created_at'],
                    'color' => '#6b7280',
                    'bg'    => '#f3f4f6',
                    'icon'  => 'fa-plus-circle',
                    'title' => 'Request Submitted',
                    'sub'   => !empty($request['request_number']) ? 'Ref: ' . htmlspecialchars($request['request_number']) : null,
                    'by'    => 'Business',
                    'dur'   => null,
                ];
            }

            // 2. Admin approved
            if (!empty($request['approved_at'])) {
                $tlEvents[] = [
                    'ts'    => strtotime($request['approved_at']),
                    'time'  => $request['approved_at'],
                    'color' => '#0284c7',
                    'bg'    => '#e0f2fe',
                    'icon'  => 'fa-check-circle',
                    'title' => 'Request Approved',
                    'sub'   => null,
                    'by'    => 'Admin',
                    'dur'   => $fmtDur($request['created_at'], $request['approved_at'])
                                ? ($fmtDur($request['created_at'], $request['approved_at']) . ' after submission') : null,
                ];
            }

            // 3. Business payment received
            if (!empty($request['paid_at'])) {
                $tlEvents[] = [
                    'ts'    => strtotime($request['paid_at']),
                    'time'  => $request['paid_at'],
                    'color' => '#16a34a',
                    'bg'    => '#dcfce7',
                    'icon'  => 'fa-credit-card',
                    'title' => 'Business Payment Received',
                    'sub'   => !empty($request['payment_method']) ? 'via ' . htmlspecialchars($request['payment_method']) : null,
                    'by'    => 'Business',
                    'dur'   => $fmtDur($request['approved_at'], $request['paid_at'])
                                ? ($fmtDur($request['approved_at'], $request['paid_at']) . ' after approval') : null,
                ];
            }

            // 4. Per-PO events
            foreach ($linkedPOs as $po) {
                $supplierLabel = htmlspecialchars($po['supplier_company'] ?: $po['supplier_name'] ?: 'Supplier');

                // PO created/sent to supplier
                if (!empty($po['created_at'])) {
                    $tlEvents[] = [
                        'ts'    => strtotime($po['created_at']),
                        'time'  => $po['created_at'],
                        'color' => '#7c3aed',
                        'bg'    => '#ede9fe',
                        'icon'  => 'fa-file-invoice',
                        'title' => 'PO Sent to ' . $supplierLabel,
                        'sub'   => htmlspecialchars($po['po_number']) . ' · ' . number_format($po['total_amount'], 2) . ' CAD'
                                   . (!empty($po['confirmation_deadline']) ? ' · Must confirm by ' . date('M j g:i A', strtotime($po['confirmation_deadline'])) : ''),
                        'by'    => 'System',
                        'dur'   => null,
                    ];
                }

                // Supplier accepted
                if (!empty($po['supplier_accepted_at'])) {
                    $dur = $fmtDur($po['created_at'], $po['supplier_accepted_at']);
                    $tlEvents[] = [
                        'ts'    => strtotime($po['supplier_accepted_at']),
                        'time'  => $po['supplier_accepted_at'],
                        'color' => '#16a34a',
                        'bg'    => '#dcfce7',
                        'icon'  => 'fa-store',
                        'title' => $supplierLabel . ' Accepted PO',
                        'sub'   => !empty($po['ready_by_time']) ? 'Ready by: ' . date('M j g:i A', strtotime($po['ready_by_time'])) : null,
                        'by'    => 'Supplier',
                        'dur'   => $dur ? 'Responded in ' . $dur : null,
                    ];
                }

                // Supplier declined
                if (!empty($po['supplier_declined_at'])) {
                    $dur = $fmtDur($po['created_at'], $po['supplier_declined_at']);
                    $tlEvents[] = [
                        'ts'    => strtotime($po['supplier_declined_at']),
                        'time'  => $po['supplier_declined_at'],
                        'color' => '#dc2626',
                        'bg'    => '#fee2e2',
                        'icon'  => 'fa-times-circle',
                        'title' => $supplierLabel . ' Declined PO',
                        'sub'   => (($po['escalation_attempt'] ?? 0) > 0) ? 'Escalation attempt #' . $po['escalation_attempt'] : null,
                        'by'    => 'Supplier',
                        'dur'   => $dur ? 'Responded in ' . $dur : null,
                    ];
                }

                // Admin paid supplier
                if (!empty($po['admin_paid_at'])) {
                    $tlEvents[] = [
                        'ts'    => strtotime($po['admin_paid_at']),
                        'time'  => $po['admin_paid_at'],
                        'color' => '#0284c7',
                        'bg'    => '#e0f2fe',
                        'icon'  => 'fa-money-bill-wave',
                        'title' => 'Paid ' . $supplierLabel,
                        'sub'   => number_format($po['total_amount'], 2) . ' CAD · ' . htmlspecialchars($po['po_number']),
                        'by'    => 'Admin',
                        'dur'   => null,
                    ];
                }
            }

            // 5. Procurement started
            if (!empty($request['procurement_started_at'])) {
                $tlEvents[] = [
                    'ts'    => strtotime($request['procurement_started_at']),
                    'time'  => $request['procurement_started_at'],
                    'color' => '#d97706',
                    'bg'    => '#fef3c7',
                    'icon'  => 'fa-boxes',
                    'title' => 'Procurement Started',
                    'sub'   => 'Suppliers are preparing items',
                    'by'    => 'System',
                    'dur'   => null,
                ];
            }

            // 6. Driver assigned
            if (!empty($daTiming['assigned_at'])) {
                $driverName = trim(($daTiming['driver_first'] ?? '') . ' ' . ($daTiming['driver_last'] ?? '')) ?: 'Driver';
                $tlEvents[] = [
                    'ts'    => strtotime($daTiming['assigned_at']),
                    'time'  => $daTiming['assigned_at'],
                    'color' => '#0891b2',
                    'bg'    => '#cffafe',
                    'icon'  => 'fa-truck',
                    'title' => 'Driver Assigned',
                    'sub'   => htmlspecialchars($driverName),
                    'by'    => 'Admin',
                    'dur'   => null,
                ];
            }

            // 7. In transit
            if (!empty($request['in_transit_at'])) {
                $tlEvents[] = [
                    'ts'    => strtotime($request['in_transit_at']),
                    'time'  => $request['in_transit_at'],
                    'color' => '#0891b2',
                    'bg'    => '#cffafe',
                    'icon'  => 'fa-shipping-fast',
                    'title' => 'Order In Transit',
                    'sub'   => !empty($daTiming['assigned_at']) ? $fmtDur($daTiming['assigned_at'], $request['in_transit_at']) . ' after driver assigned' : null,
                    'by'    => 'Driver',
                    'dur'   => null,
                ];
            }

            // 8. Driver picked up (from delivery assignment)
            if (!empty($daTiming['picked_up_at'])) {
                $tlEvents[] = [
                    'ts'    => strtotime($daTiming['picked_up_at']),
                    'time'  => $daTiming['picked_up_at'],
                    'color' => '#0891b2',
                    'bg'    => '#cffafe',
                    'icon'  => 'fa-box-open',
                    'title' => 'Driver Picked Up Order',
                    'sub'   => null,
                    'by'    => 'Driver',
                    'dur'   => null,
                ];
            }

            // 9. Delivered
            if (!empty($request['delivered_at'])) {
                $deliveryDur = null;
                if (!empty($request['in_transit_at'])) {
                    $deliveryDur = $fmtDur($request['in_transit_at'], $request['delivered_at']) . ' transit time';
                }
                $tlEvents[] = [
                    'ts'    => strtotime($request['delivered_at']),
                    'time'  => $request['delivered_at'],
                    'color' => '#16a34a',
                    'bg'    => '#dcfce7',
                    'icon'  => 'fa-check-double',
                    'title' => 'Order Delivered',
                    'sub'   => !empty($request['delivery_confirmed_by']) ? 'Confirmed by: ' . htmlspecialchars($request['delivery_confirmed_by']) : null,
                    'by'    => 'Driver',
                    'dur'   => $deliveryDur,
                ];
            }

            // 10. Status history notes (admin manual changes / notes only)
            foreach ($statusHistory as $sh) {
                if (empty($sh['notes'])) continue; // skip bare status changes already covered
                // Check if this event is already represented by a timestamp field
                $shTs = strtotime($sh['created_at'] ?? '');
                $duplicate = false;
                foreach ($tlEvents as $ev) {
                    if (abs($ev['ts'] - $shTs) < 5) { $duplicate = true; break; }
                }
                if ($duplicate) continue;
                $tlEvents[] = [
                    'ts'    => $shTs,
                    'time'  => $sh['created_at'],
                    'color' => '#6b7280',
                    'bg'    => '#f3f4f6',
                    'icon'  => 'fa-comment-alt',
                    'title' => ucwords(str_replace('_', ' ', $sh['new_status'] ?? 'Update')),
                    'sub'   => htmlspecialchars($sh['notes']),
                    'by'    => !empty($sh['changed_by_type']) ? ucfirst($sh['changed_by_type']) : null,
                    'dur'   => null,
                ];
            }

            // Sort chronologically (oldest first)
            usort($tlEvents, fn($a, $b) => $a['ts'] <=> $b['ts']);
            ?>

            <?php if (empty($tlEvents)): ?>
                <p style="color: #666; font-size: 14px;">No activity recorded.</p>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach ($tlEvents as $ev): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:<?= $ev['color'] ?>;box-shadow:0 0 0 3px <?= $ev['bg'] ?>;"></div>
                        <div class="timeline-content">
                            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
                                <div class="timeline-status" style="color:<?= $ev['color'] ?>;">
                                    <i class="fas <?= $ev['icon'] ?>" style="margin-right:5px;font-size:11px;"></i><?= $ev['title'] ?>
                                </div>
                                <?php if (!empty($ev['dur'])): ?>
                                    <span style="font-size:11px;padding:1px 8px;border-radius:10px;background:<?= $ev['bg'] ?>;color:<?= $ev['color'] ?>;font-weight:600;"><?= $ev['dur'] ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($ev['sub'])): ?>
                                <div class="timeline-note" style="margin-top:3px;"><?= $ev['sub'] ?></div>
                            <?php endif; ?>
                            <div class="timeline-date">
                                <?= date('M j, Y g:i A', $ev['ts']) ?>
                                <?php if (!empty($ev['by'])): ?> · <?= $ev['by'] ?><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
            <?php endif; ?>

            <?php if (!empty($performanceScores)): ?>
                <div style="margin-top:24px;padding-top:20px;border-top:2px solid #f3f4f6;">
                    <div style="font-size:13px;font-weight:700;color:#6366f1;text-transform:uppercase;letter-spacing:.6px;margin-bottom:14px;">
                        <i class="fas fa-chart-bar" style="margin-right:6px;"></i> Performance Review
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;">
                    <?php foreach ($performanceScores as $score):
                        $label = \App\Services\ScoringService::scoreLabel($score['total_score']);
                        $details = is_string($score['details']) ? json_decode($score['details'], true) : ($score['details'] ?? []);
                        $entityIcon = $score['entity_type'] === 'supplier' ? 'fa-store' : ($score['entity_type'] === 'driver' ? 'fa-truck' : 'fa-building');
                    ?>
                    <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px;background:#fafafa;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:<?= $label['bg'] ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas <?= $entityIcon ?>" style="color:<?= $label['color'] ?>;font-size:13px;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:600;font-size:13px;color:#111;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($score['entity_name'] ?? ucfirst($score['entity_type'])) ?></div>
                                <div style="font-size:10px;color:#999;text-transform:uppercase;letter-spacing:.4px;"><?= $score['entity_type'] ?></div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <span style="display:block;font-weight:700;font-size:18px;color:<?= $label['color'] ?>;"><?= $score['total_score'] ?></span>
                                <span style="font-size:10px;color:<?= $label['color'] ?>;font-weight:600;"><?= $label['label'] ?></span>
                            </div>
                        </div>
                        <div style="height:6px;background:#f0f0f0;border-radius:3px;margin-bottom:10px;overflow:hidden;">
                            <div style="height:100%;width:<?= $score['total_score'] ?>%;background:<?= $label['color'] ?>;border-radius:3px;"></div>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            <?php
                            $components = [
                                'response_score'    => 'Response',
                                'timeliness_score'  => 'Timeliness',
                                'completion_score'  => 'Completion',
                            ];
                            foreach ($components as $key => $compLabel):
                                $compVal = $score[$key] ?? 0;
                            ?>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div style="width:70px;font-size:11px;color:#666;"><?= $compLabel ?></div>
                                <div style="flex:1;height:4px;background:#f0f0f0;border-radius:2px;overflow:hidden;">
                                    <div style="height:100%;width:<?= $compVal ?>%;background:<?= $label['color'] ?>;opacity:.7;border-radius:2px;"></div>
                                </div>
                                <div style="width:24px;text-align:right;font-size:11px;font-weight:600;color:#444;"><?= $compVal ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!empty($details)): ?>
                        <div style="margin-top:8px;padding-top:8px;border-top:1px solid #f0f0f0;font-size:10px;color:#999;line-height:1.6;">
                            <?php foreach ($details as $k => $v): if (is_array($v)) continue; ?>
                            <span><?= htmlspecialchars(str_replace('_', ' ', ucfirst($k))) ?>: <strong style="color:#666;"><?= htmlspecialchars($v) ?></strong></span>&nbsp;
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!in_array($request['status'] ?? '', ['delivered', 'cancelled'])): ?>
<style>
#drAdminToast {
    position: fixed;
    bottom: 28px;
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: #1a1a1a;
    color: #fff;
    padding: 11px 22px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    opacity: 0;
    transition: opacity .3s, transform .3s;
    z-index: 9999;
    white-space: nowrap;
}
#drAdminToast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
</style>
<div id="drAdminToast">🔄 <span id="drAdminToastMsg">Updated — reloading…</span></div>
<script>
(function () {
    var requestId  = <?= (int)($request['id'] ?? 0) ?>;
    var curDrStatus = <?= json_encode($request['status'] ?? '') ?>;
    var curPoMap    = <?php
        $poSnapshot = [];
        foreach ($linkedPOs as $lpo) {
            $poSnapshot[$lpo['id']] = [
                'status'               => $lpo['status'],
                'admin_paid_at'        => $lpo['admin_paid_at'] ?? null,
                'supplier_accepted_at' => $lpo['supplier_accepted_at'] ?? null,
                'supplier_declined_at' => $lpo['supplier_declined_at'] ?? null,
            ];
        }
        echo json_encode($poSnapshot);
    ?>;
    var curDa = <?= json_encode($daTiming ? [
        'status'                  => $daTiming['status'] ?? null,
        'picked_up_at'            => $daTiming['picked_up_at'] ?? null,
        'delivered_at'            => $daTiming['delivered_at'] ?? null,
        'heading_to_supplier_at'  => $daTiming['heading_to_supplier_at'] ?? null,
        'en_route_to_customer_at' => $daTiming['en_route_to_customer_at'] ?? null,
    ] : null) ?>;
    var endpoint   = '<?= url('api/admin/distribution/status') ?>?id=' + requestId;
    var reloading  = false;

    function showToast(msg) {
        var t = document.getElementById('drAdminToast');
        document.getElementById('drAdminToastMsg').textContent = msg;
        t.classList.add('show');
    }

    function poll() {
        if (reloading) return;
        fetch(endpoint)
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data || !data.success) return;

                var changed = false;

                // DR status changed
                if (data.dr_status !== curDrStatus) {
                    changed = true;
                }

                // Any PO status, admin_paid_at, or supplier response changed
                if (!changed && data.po_statuses) {
                    var ids = Object.keys(data.po_statuses);
                    for (var i = 0; i < ids.length; i++) {
                        var id = ids[i];
                        var fresh = data.po_statuses[id];
                        var prev  = curPoMap[id];
                        if (!prev
                            || fresh.status !== prev.status
                            || (fresh.admin_paid_at        || '') !== (prev.admin_paid_at        || '')
                            || (fresh.supplier_accepted_at || '') !== (prev.supplier_accepted_at || '')
                            || (fresh.supplier_declined_at || '') !== (prev.supplier_declined_at || '')) {
                            changed = true;
                            break;
                        }
                    }
                    // New PO added
                    if (!changed && ids.length !== Object.keys(curPoMap).length) {
                        changed = true;
                    }
                }

                // Delivery assignment changed (driver step recorded or delivered via app)
                if (!changed && data.da) {
                    if (!curDa
                        || data.da.status      !== (curDa.status       || null)
                        || (data.da.picked_up_at            || '') !== (curDa.picked_up_at            || '')
                        || (data.da.delivered_at             || '') !== (curDa.delivered_at             || '')
                        || (data.da.heading_to_supplier_at  || '') !== (curDa.heading_to_supplier_at  || '')
                        || (data.da.en_route_to_customer_at || '') !== (curDa.en_route_to_customer_at || '')) {
                        changed = true;
                    }
                } else if (!changed && !data.da && curDa) {
                    // assignment was cancelled/removed
                    changed = true;
                }

                if (changed) {
                    reloading = true;
                    showToast('Updated — reloading…');
                    setTimeout(function () { location.reload(); }, 1800);
                }
            })
            .catch(function () {});
    }

    setInterval(poll, 5000);
})();
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
