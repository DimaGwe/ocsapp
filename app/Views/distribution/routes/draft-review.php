<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = $currentLang === 'fr';
$currentPage = 'routes';
$pageTitle = $fr ? 'Révision du brouillon' : 'Draft Review';
require __DIR__ . '/../layout-header.php';

$fmtDate = fn($d) => $fr ? date('Y-m-d', strtotime($d)) : date('M j, Y', strtotime($d));
$typeLabels = $fr
    ? ['parcel' => 'Colis', 'multi_drop' => 'Arrêts multiples', 'product_fulfillment' => 'Exécution de produits']
    : ['parcel' => 'Parcel', 'multi_drop' => 'Multi-drop', 'product_fulfillment' => 'Product fulfillment'];

// Single-stop shipments keep their destination on the shipment row itself
if (empty($destinations) && !empty($shipment['destination_street'])) {
    $destinations = [[
        'sequence_order'        => 1,
        'destination_name'      => null,
        'contact_name'          => $shipment['destination_contact_name'] ?? null,
        'street'                => $shipment['destination_street'],
        'city'                  => $shipment['destination_city'] ?? '',
        'province'              => $shipment['destination_province'] ?? '',
        'postal_code'           => $shipment['destination_postal_code'] ?? '',
        'contact_phone'         => $shipment['destination_contact_phone'] ?? null,
        'delivery_instructions' => $shipment['destination_instructions'] ?? null,
        'packages_count'        => $shipment['total_packages'] ?? null,
    ]];
}
?>
        <a href="<?= url('distribution/routes') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> <?= $fr ? 'Retour aux routes' : 'Back to Routes' ?>
        </a>

        <div class="page-header">
            <h1 class="page-title"><?= $fr ? "Réviser l'envoi brouillon" : 'Review Draft Shipment' ?></h1>
            <p class="page-subtitle"><?= $fr ? 'Route :' : 'From route:' ?> <?= htmlspecialchars($shipment['route_name'] ?? ($fr ? 'Route récurrente' : 'Recurring Route')) ?></p>
        </div>

        <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            <?= $fr
                ? 'Cet envoi a été généré automatiquement à partir de votre route récurrente. Vérifiez les détails ci-dessous et approuvez-le pour le soumettre.'
                : 'This shipment was auto-generated from your recurring route. Review the details below and approve to submit it for processing.' ?>
        </div>

        <!-- Shipment Details -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-box"></i> <?= $fr ? "Détails de l'envoi" : 'Shipment Details' ?></div>
            <div class="info-grid">
                <div class="info-group">
                    <div class="info-label"><?= $fr ? "Numéro d'envoi" : 'Shipment Number' ?></div>
                    <div class="info-value"><?= htmlspecialchars($shipment['shipment_number']) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label"><?= $fr ? 'Statut' : 'Status' ?></div>
                    <div class="info-value"><span class="badge badge-draft"><?= $fr ? 'Brouillon' : 'Draft' ?></span></div>
                </div>
                <div class="info-group">
                    <div class="info-label"><?= $fr ? 'Type' : 'Type' ?></div>
                    <div class="info-value"><?= htmlspecialchars($typeLabels[$shipment['shipment_type'] ?? 'parcel'] ?? ($shipment['shipment_type'] ?? '')) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label"><?= $fr ? 'Colis' : 'Packages' ?></div>
                    <div class="info-value"><?= (int)($shipment['total_packages'] ?? 1) ?></div>
                </div>
                <?php if (!empty($shipment['requested_pickup_date'])): ?>
                    <div class="info-group">
                        <div class="info-label"><?= $fr ? 'Cueillette prévue' : 'Requested Pickup' ?></div>
                        <div class="info-value">
                            <?= $fmtDate($shipment['requested_pickup_date']) ?>
                            <?php if (!empty($shipment['requested_pickup_time_start'])): ?>
                                <?= substr($shipment['requested_pickup_time_start'], 0, 5) ?><?= !empty($shipment['requested_pickup_time_end']) ? ' - ' . substr($shipment['requested_pickup_time_end'], 0, 5) : '' ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pickup Location -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-map-marker-alt"></i> <?= $fr ? 'Lieu de cueillette' : 'Pickup Location' ?></div>
            <div class="address-block">
                <h4><?= $fr ? 'Adresse de cueillette' : 'Pickup Address' ?></h4>
                <p>
                    <?= htmlspecialchars($shipment['pickup_street'] ?? '') ?><br>
                    <?= htmlspecialchars($shipment['pickup_city'] ?? '') ?>, <?= htmlspecialchars($shipment['pickup_province'] ?? '') ?> <?= htmlspecialchars($shipment['pickup_postal_code'] ?? '') ?>
                </p>
            </div>
            <?php if (!empty($shipment['pickup_contact_name'])): ?>
                <div class="info-grid">
                    <div class="info-group">
                        <div class="info-label"><?= $fr ? 'Personne-ressource' : 'Contact' ?></div>
                        <div class="info-value"><?= htmlspecialchars($shipment['pickup_contact_name']) ?></div>
                    </div>
                    <?php if (!empty($shipment['pickup_contact_phone'])): ?>
                        <div class="info-group">
                            <div class="info-label"><?= $fr ? 'Téléphone' : 'Phone' ?></div>
                            <div class="info-value"><?= htmlspecialchars($shipment['pickup_contact_phone']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Destinations -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-flag-checkered"></i> <?= $fr ? 'Destinations de livraison' : 'Delivery Destinations' ?></div>
            <?php foreach ($destinations ?? [] as $i => $dest): ?>
                <div class="destination-item">
                    <div class="destination-number"><?= (int)($dest['sequence_order'] ?? $i + 1) ?></div>
                    <div class="destination-details">
                        <div class="destination-name"><?= htmlspecialchars(($dest['destination_name'] ?? '') ?: (($dest['contact_name'] ?? '') ?: (($fr ? 'Destination ' : 'Destination ') . ($i + 1)))) ?></div>
                        <div class="destination-address">
                            <?= htmlspecialchars($dest['street'] ?? '') ?>,
                            <?= htmlspecialchars($dest['city'] ?? '') ?>,
                            <?= htmlspecialchars($dest['province'] ?? '') ?>
                            <?= htmlspecialchars($dest['postal_code'] ?? '') ?>
                        </div>
                        <?php if (!empty($dest['packages_count'])): ?>
                            <div style="font-size: 12px; color: #666; margin-top: 4px;">
                                <i class="fas fa-box"></i> <?= (int)$dest['packages_count'] ?> <?= $fr ? 'colis' : ((int)$dest['packages_count'] === 1 ? 'package' : 'packages') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($dest['contact_phone'])): ?>
                            <div style="font-size: 12px; color: #666; margin-top: 4px;">
                                <i class="fas fa-phone"></i> <?= htmlspecialchars($dest['contact_phone']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($dest['delivery_instructions'])): ?>
                            <div style="font-size: 12px; color: #999; margin-top: 4px;">
                                <i class="fas fa-sticky-note"></i> <?= htmlspecialchars($dest['delivery_instructions']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <a href="<?= url('distribution/routes') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> <?= $fr ? 'Retour aux routes' : 'Back to Routes' ?>
            </a>
            <form action="<?= url('distribution/routes/approve') ?>" method="POST">
                <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="shipment_id" value="<?= $shipment['id'] ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> <?= $fr ? 'Approuver et soumettre' : 'Approve & Submit' ?>
                </button>
            </form>
        </div>
<?php require __DIR__ . '/../layout-footer.php'; ?>
