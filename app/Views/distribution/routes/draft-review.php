<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$currentPage = 'routes';
$pageTitle = $currentLang === 'fr' ? 'Révision du brouillon' : 'Draft Review';
require __DIR__ . '/../layout-header.php';
?>
        <a href="<?= url('distribution/routes') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Routes
        </a>

        <div class="page-header">
            <h1 class="page-title">Review Draft Shipment</h1>
            <p class="page-subtitle">From route: <?= htmlspecialchars($shipment['route_name'] ?? 'Recurring Route') ?></p>
        </div>

        <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            This shipment was auto-generated from your recurring route. Review the details below and approve to submit it for processing.
        </div>

        <!-- Shipment Details -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-box"></i> Shipment Details</div>
            <div class="info-grid">
                <div class="info-group">
                    <div class="info-label">Shipment Number</div>
                    <div class="info-value"><?= htmlspecialchars($shipment['shipment_number']) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Status</div>
                    <div class="info-value"><span class="badge badge-draft">Draft</span></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Type</div>
                    <div class="info-value"><?= ucfirst(str_replace('_', ' ', $shipment['shipment_type'] ?? 'parcel')) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Created</div>
                    <div class="info-value"><?= date('M j, Y g:i A', strtotime($shipment['created_at'])) ?></div>
                </div>
                <?php if (!empty($shipment['requested_pickup_date'])): ?>
                    <div class="info-group">
                        <div class="info-label">Requested Pickup</div>
                        <div class="info-value"><?= date('M j, Y', strtotime($shipment['requested_pickup_date'])) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pickup Location -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-map-marker-alt"></i> Pickup Location</div>
            <div class="address-block">
                <h4>Pickup Address</h4>
                <p>
                    <?= htmlspecialchars($shipment['pickup_street'] ?? '') ?><br>
                    <?= htmlspecialchars($shipment['pickup_city'] ?? '') ?>, <?= htmlspecialchars($shipment['pickup_province'] ?? '') ?> <?= htmlspecialchars($shipment['pickup_postal_code'] ?? '') ?>
                </p>
            </div>
            <?php if (!empty($shipment['pickup_contact_name'])): ?>
                <div class="info-grid">
                    <div class="info-group">
                        <div class="info-label">Contact</div>
                        <div class="info-value"><?= htmlspecialchars($shipment['pickup_contact_name']) ?></div>
                    </div>
                    <?php if (!empty($shipment['pickup_contact_phone'])): ?>
                        <div class="info-group">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><?= htmlspecialchars($shipment['pickup_contact_phone']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Destinations -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-flag-checkered"></i> Delivery Destinations</div>
            <?php if (!empty($destinations)): ?>
                <?php foreach ($destinations as $i => $dest): ?>
                    <div class="destination-item">
                        <div class="destination-number"><?= ($dest['sequence_order'] ?? $i) + 1 ?></div>
                        <div class="destination-details">
                            <div class="destination-name"><?= htmlspecialchars($dest['contact_name'] ?? 'Destination ' . ($i + 1)) ?></div>
                            <div class="destination-address">
                                <?= htmlspecialchars($dest['street'] ?? '') ?>,
                                <?= htmlspecialchars($dest['city'] ?? '') ?>,
                                <?= htmlspecialchars($dest['province'] ?? '') ?>
                                <?= htmlspecialchars($dest['postal_code'] ?? '') ?>
                            </div>
                            <?php if (!empty($dest['contact_phone'])): ?>
                                <div style="font-size: 12px; color: #666; margin-top: 4px;">
                                    <i class="fas fa-phone"></i> <?= htmlspecialchars($dest['contact_phone']) ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($dest['delivery_notes'])): ?>
                                <div style="font-size: 12px; color: #999; margin-top: 4px;">
                                    <i class="fas fa-sticky-note"></i> <?= htmlspecialchars($dest['delivery_notes']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($shipment['special_instructions'])): ?>
            <div class="section-card">
                <div class="section-title"><i class="fas fa-sticky-note"></i> Special Instructions</div>
                <p style="font-size: 14px; color: #374151; line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($shipment['special_instructions'])) ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="form-actions">
            <a href="<?= url('distribution/routes') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Routes
            </a>
            <form action="<?= url('distribution/routes/approve') ?>" method="POST">
                <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="shipment_id" value="<?= $shipment['id'] ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Approve & Submit
                </button>
            </form>
        </div>
<?php require __DIR__ . '/../layout-footer.php'; ?>
