<?php include dirname(__DIR__) . '/layout.php'; ?>

<div class="content-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="<?= url('admin/shipments') ?>" style="color: #666; text-decoration: none; font-size: 14px; margin-bottom: 8px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Back to Shipments
            </a>
            <h1><i class="fas fa-truck"></i> <?= htmlspecialchars($shipment['shipment_number']) ?></h1>
            <p>
                <?php
                $typeLabels = ['parcel' => 'Parcel Delivery', 'product_fulfillment' => 'Product Fulfillment', 'multi_drop' => 'Multi-Drop Route'];
                echo $typeLabels[$shipment['shipment_type']] ?? 'Shipment';
                ?>
                &bull; <?= htmlspecialchars($shipment['company_name']) ?>
            </p>
        </div>
        <span class="badge badge-<?= $shipment['status'] ?>" style="font-size: 14px; padding: 8px 16px;">
            <?= ucwords(str_replace('_', ' ', $shipment['status'])) ?>
        </span>
    </div>
</div>

<div class="content-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <div>
        <!-- Business Info -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header"><i class="fas fa-building"></i> Business Information</div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Company</label>
                        <span><?= htmlspecialchars($shipment['company_name']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Contact</label>
                        <span><?= htmlspecialchars($shipment['first_name'] . ' ' . $shipment['last_name']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <span><?= htmlspecialchars($shipment['email']) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Phone</label>
                        <span><?= htmlspecialchars($shipment['phone'] ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Route Details -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header"><i class="fas fa-route"></i> Route Details</div>
            <div class="card-body">
                <div class="address-boxes">
                    <div class="address-box">
                        <h4><i class="fas fa-box" style="color: #00b207;"></i> Pickup</h4>
                        <p>
                            <?= htmlspecialchars($shipment['pickup_street']) ?><br>
                            <?= htmlspecialchars($shipment['pickup_city']) ?>, <?= htmlspecialchars($shipment['pickup_province']) ?> <?= htmlspecialchars($shipment['pickup_postal_code']) ?>
                        </p>
                        <?php if ($shipment['pickup_contact_name']): ?>
                            <p style="margin-top: 8px; font-size: 13px; color: #666;">
                                <i class="fas fa-user"></i> <?= htmlspecialchars($shipment['pickup_contact_name']) ?>
                                <?= $shipment['pickup_contact_phone'] ? ' - ' . htmlspecialchars($shipment['pickup_contact_phone']) : '' ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($shipment['requested_pickup_date']): ?>
                            <p style="margin-top: 8px; font-size: 13px;">
                                <i class="fas fa-calendar"></i> Requested: <?= date('M j, Y', strtotime($shipment['requested_pickup_date'])) ?>
                                <?php if ($shipment['requested_pickup_time_start']): ?>
                                    (<?= date('g:i A', strtotime($shipment['requested_pickup_time_start'])) ?> - <?= date('g:i A', strtotime($shipment['requested_pickup_time_end'])) ?>)
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if ($shipment['is_multi_drop'] && !empty($destinations)): ?>
                        <div class="address-box" style="flex: 2;">
                            <h4><i class="fas fa-flag-checkered" style="color: #00b207;"></i> Destinations (<?= count($destinations) ?>)</h4>
                            <div class="destinations-list">
                                <?php foreach ($destinations as $dest): ?>
                                    <div class="destination-row">
                                        <span class="stop-number"><?= $dest['sequence_order'] ?></span>
                                        <div class="stop-info">
                                            <strong><?= htmlspecialchars($dest['destination_name']) ?></strong>
                                            <span><?= htmlspecialchars($dest['city']) ?>, <?= htmlspecialchars($dest['province']) ?></span>
                                        </div>
                                        <span class="badge badge-<?= $dest['status'] ?>"><?= ucwords($dest['status']) ?></span>
                                        <?php if ($shipment['status'] !== 'draft' && $shipment['status'] !== 'submitted'): ?>
                                            <form action="<?= url('admin/shipments/destination-status') ?>" method="POST" style="display: inline;">
                                                <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                                                <input type="hidden" name="destination_id" value="<?= $dest['id'] ?>">
                                                <select name="status" class="form-control" style="width: auto; padding: 4px 8px; font-size: 12px;" onchange="this.form.submit()">
                                                    <option value="">Update</option>
                                                    <option value="in_transit">In Transit</option>
                                                    <option value="delivered">Delivered</option>
                                                    <option value="failed">Failed</option>
                                                </select>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="address-box">
                            <h4><i class="fas fa-flag-checkered" style="color: #00b207;"></i> Delivery</h4>
                            <p>
                                <?= htmlspecialchars($shipment['destination_street']) ?><br>
                                <?= htmlspecialchars($shipment['destination_city']) ?>, <?= htmlspecialchars($shipment['destination_province']) ?> <?= htmlspecialchars($shipment['destination_postal_code']) ?>
                            </p>
                            <?php if ($shipment['destination_contact_name']): ?>
                                <p style="margin-top: 8px; font-size: 13px; color: #666;">
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($shipment['destination_contact_name']) ?>
                                    <?= $shipment['destination_contact_phone'] ? ' - ' . htmlspecialchars($shipment['destination_contact_phone']) : '' ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Package Details -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header"><i class="fas fa-boxes"></i> Package Details</div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Total Packages</label>
                        <span><?= $shipment['total_packages'] ?? 1 ?></span>
                    </div>
                    <div class="info-item">
                        <label>Total Weight</label>
                        <span><?= $shipment['total_weight_kg'] ? $shipment['total_weight_kg'] . ' kg' : 'Not specified' ?></span>
                    </div>
                </div>
                <?php if ($shipment['package_description']): ?>
                    <div style="margin-top: 16px;">
                        <label style="font-size: 12px; color: #666; text-transform: uppercase;">Description</label>
                        <p style="margin-top: 4px;"><?= nl2br(htmlspecialchars($shipment['package_description'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Items -->
        <?php if (!empty($items)): ?>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header"><i class="fas fa-list-alt"></i> Items (<?= count($items) ?>)</div>
                <div class="card-body" style="padding: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>SKU</th>
                                <th>Qty</th>
                                <th>Value</th>
                                <th>Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($item['item_name']) ?>
                                        <?php if ($item['is_fragile']): ?>
                                            <span style="color: #f59e0b; font-size: 11px;"><i class="fas fa-exclamation-triangle"></i> Fragile</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($item['item_sku'] ?? '-') ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= $item['unit_value'] ? '$' . number_format($item['unit_value'], 2) : '-' ?></td>
                                    <td><?= $item['weight_kg'] ? $item['weight_kg'] . ' kg' : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Create Quote Form -->
        <?php if ($shipment['status'] === 'submitted'): ?>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header" style="background: #fef3c7;"><i class="fas fa-calculator"></i> Create Quote</div>
                <div class="card-body">
                    <form action="<?= url('admin/shipments/quote') ?>" method="POST">
                        <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="shipment_id" value="<?= $shipment['id'] ?>">

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Base Rate ($) *</label>
                                <input type="number" name="base_rate" class="form-control" step="0.01" required value="25.00">
                            </div>
                            <div class="form-group">
                                <label>Per Stop Rate ($)</label>
                                <input type="number" name="per_stop_rate" class="form-control" step="0.01" value="<?= $shipment['is_multi_drop'] ? '10.00' : '0' ?>">
                            </div>
                            <div class="form-group">
                                <label>Weight Surcharge ($)</label>
                                <input type="number" name="weight_surcharge" class="form-control" step="0.01" value="0">
                            </div>
                            <div class="form-group">
                                <label>Distance Surcharge ($)</label>
                                <input type="number" name="distance_surcharge" class="form-control" step="0.01" value="0">
                            </div>
                            <div class="form-group">
                                <label>Rush Surcharge ($)</label>
                                <input type="number" name="rush_surcharge" class="form-control" step="0.01" value="0">
                            </div>
                            <div class="form-group">
                                <label>Tax Rate (%)</label>
                                <input type="number" name="tax_rate" class="form-control" step="0.001" value="14.975">
                            </div>
                            <div class="form-group">
                                <label>Valid For (Days)</label>
                                <select name="valid_days" class="form-control">
                                    <option value="7">7 days</option>
                                    <option value="14">14 days</option>
                                    <option value="30">30 days</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 16px;">
                            <label>Notes for Customer</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Any notes about this quote..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="margin-top: 16px;">
                            <i class="fas fa-paper-plane"></i> Send Quote
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <!-- Quote Summary -->
        <?php if ($quote): ?>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header"><i class="fas fa-file-invoice-dollar"></i> Quote</div>
                <div class="card-body">
                    <div class="quote-summary">
                        <div class="quote-row">
                            <span>Base Rate</span>
                            <span>$<?= number_format($quote['base_rate'], 2) ?></span>
                        </div>
                        <?php if ($quote['stops_total'] > 0): ?>
                            <div class="quote-row">
                                <span>Stops (<?= $quote['stops_count'] ?>)</span>
                                <span>$<?= number_format($quote['stops_total'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($quote['weight_surcharge'] > 0): ?>
                            <div class="quote-row">
                                <span>Weight</span>
                                <span>$<?= number_format($quote['weight_surcharge'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($quote['distance_surcharge'] > 0): ?>
                            <div class="quote-row">
                                <span>Distance</span>
                                <span>$<?= number_format($quote['distance_surcharge'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($quote['rush_surcharge'] > 0): ?>
                            <div class="quote-row">
                                <span>Rush</span>
                                <span>$<?= number_format($quote['rush_surcharge'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="quote-row">
                            <span>Subtotal</span>
                            <span>$<?= number_format($quote['subtotal'], 2) ?></span>
                        </div>
                        <div class="quote-row">
                            <span>Tax (<?= $quote['tax_rate'] ?>%)</span>
                            <span>$<?= number_format($quote['tax_amount'], 2) ?></span>
                        </div>
                        <div class="quote-row total">
                            <span>Total</span>
                            <span>$<?= number_format($quote['total_amount'], 2) ?></span>
                        </div>
                    </div>
                    <p style="font-size: 12px; color: #666; margin-top: 12px; text-align: center;">
                        Valid until <?= date('M j, Y', strtotime($quote['valid_until'])) ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Status Update -->
        <?php if (!in_array($shipment['status'], ['draft', 'submitted', 'cancelled'])): ?>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header"><i class="fas fa-edit"></i> Update Status</div>
                <div class="card-body">
                    <form action="<?= url('admin/shipments/status') ?>" method="POST">
                        <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="shipment_id" value="<?= $shipment['id'] ?>">

                        <div class="form-group">
                            <label>New Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">Select...</option>
                                <?php if ($shipment['status'] === 'quoted'): ?>
                                    <option value="paid">Mark as Paid</option>
                                <?php endif; ?>
                                <?php if ($shipment['status'] === 'paid'): ?>
                                    <option value="scheduled">Schedule Pickup</option>
                                <?php endif; ?>
                                <?php if ($shipment['status'] === 'scheduled'): ?>
                                    <option value="picked_up">Mark Picked Up</option>
                                <?php endif; ?>
                                <?php if ($shipment['status'] === 'picked_up'): ?>
                                    <option value="in_transit">In Transit</option>
                                <?php endif; ?>
                                <?php if ($shipment['status'] === 'in_transit'): ?>
                                    <option value="delivered">Delivered</option>
                                <?php endif; ?>
                                <?php if ($shipment['status'] === 'delivered'): ?>
                                    <option value="completed">Complete</option>
                                <?php endif; ?>
                                <option value="cancelled">Cancel</option>
                            </select>
                        </div>

                        <div class="form-group" id="scheduleDateGroup" style="display: none;">
                            <label>Scheduled Date</label>
                            <input type="date" name="scheduled_for" class="form-control" min="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Mark as Paid -->
        <?php if (in_array($shipment['status'], ['quoted', 'pending_payment'])): ?>
            <div class="card" style="margin-bottom: 24px;">
                <div class="card-header"><i class="fas fa-money-bill-wave"></i> Manual Payment</div>
                <div class="card-body">
                    <form action="<?= url('admin/shipments/mark-paid') ?>" method="POST">
                        <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="shipment_id" value="<?= $shipment['id'] ?>">

                        <div class="form-group">
                            <label>Reference #</label>
                            <input type="text" name="reference" class="form-control" placeholder="Bank transfer ref...">
                        </div>

                        <button type="submit" class="btn btn-success" onclick="return confirm('Mark this shipment as paid?');">
                            <i class="fas fa-check"></i> Mark as Paid
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Status History -->
        <div class="card">
            <div class="card-header"><i class="fas fa-history"></i> History</div>
            <div class="card-body">
                <?php if (empty($statusHistory)): ?>
                    <p style="color: #666; text-align: center; padding: 20px;">No status updates yet.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($statusHistory as $h): ?>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <strong><?= ucwords(str_replace('_', ' ', $h['new_status'])) ?></strong>
                                    <?php if ($h['notes']): ?>
                                        <p><?= htmlspecialchars($h['notes']) ?></p>
                                    <?php endif; ?>
                                    <time><?= date('M j, g:i A', strtotime($h['created_at'])) ?></time>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .info-item label { font-size: 12px; color: #666; text-transform: uppercase; display: block; margin-bottom: 4px; }
    .info-item span { font-size: 14px; color: #1a1a1a; }
    .address-boxes { display: flex; gap: 20px; }
    .address-box { flex: 1; background: #f8fafc; border-radius: 10px; padding: 16px; }
    .address-box h4 { font-size: 13px; color: #374151; margin-bottom: 12px; }
    .address-box p { font-size: 14px; color: #1a1a1a; line-height: 1.6; }
    .destinations-list { max-height: 300px; overflow-y: auto; }
    .destination-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
    .destination-row:last-child { border-bottom: none; }
    .stop-number { width: 24px; height: 24px; background: #00b207; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; }
    .stop-info { flex: 1; }
    .stop-info strong { display: block; font-size: 14px; }
    .stop-info span { font-size: 12px; color: #666; }
    .quote-summary { background: #f0fdf4; border-radius: 8px; padding: 16px; }
    .quote-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
    .quote-row.total { border-top: 1px solid #d1fae5; margin-top: 8px; padding-top: 12px; font-weight: 600; font-size: 16px; }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .form-group { margin-bottom: 0; }
    .form-group label { font-size: 13px; color: #374151; margin-bottom: 6px; display: block; }
    .timeline { position: relative; padding-left: 20px; }
    .timeline::before { content: ''; position: absolute; left: 6px; top: 0; bottom: 0; width: 2px; background: #e5e7eb; }
    .timeline-item { position: relative; padding-bottom: 16px; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-dot { position: absolute; left: -20px; top: 0; width: 14px; height: 14px; border-radius: 50%; background: #00b207; border: 2px solid white; box-shadow: 0 0 0 2px #00b207; }
    .timeline-content strong { font-size: 14px; color: #1a1a1a; }
    .timeline-content p { font-size: 13px; color: #666; margin: 4px 0; }
    .timeline-content time { font-size: 11px; color: #999; }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .badge-pending { background: #f3f4f6; color: #666; }
    .badge-in_transit { background: #cffafe; color: #0891b2; }
    .badge-delivered { background: #d1fae5; color: #059669; }
    .badge-failed { background: #fee2e2; color: #dc2626; }
    .btn-success { background: #10b981; color: white; }
    .btn-success:hover { background: #059669; }
    @media (max-width: 768px) { .content-grid { grid-template-columns: 1fr; } .address-boxes { flex-direction: column; } .form-grid { grid-template-columns: 1fr; } }
</style>

<script>
    document.querySelector('select[name="status"]')?.addEventListener('change', function() {
        const scheduleGroup = document.getElementById('scheduleDateGroup');
        if (this.value === 'scheduled') {
            scheduleGroup.style.display = 'block';
        } else {
            scheduleGroup.style.display = 'none';
        }
    });
</script>

<?php $content = ob_get_clean(); ?>
