<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$pageTitle   = $currentLang === 'fr' ? 'Optimiseur d\'itinéraire' : 'Route Optimizer';
$currentPage = 'route-optimizer';

$t = [
    'en' => [
        'title'         => 'Route Optimizer',
        'subtitle'      => 'Plan optimized delivery routes for maximum efficiency',
        'stat_total'    => 'Total Pending Deliveries',
        'stat_gps'      => 'With GPS Coordinates',
        'stat_nogps'    => 'Without GPS',
        'leg_pickup'    => 'Pickup Location',
        'leg_dropoff'   => 'Drop-off Location',
        'leg_route'     => 'Optimized Route',
        'select_del'    => 'Select Deliveries',
        'select_hint'   => 'Select multiple deliveries to create an optimized route',
        'no_pending'    => 'No pending deliveries available',
        'btn_optimize'  => 'Optimize Route',
        'results_title' => 'Route Results',
        'total_dist'    => 'Total Distance',
        'est_time'      => 'Est. Time',
        'stop_order'    => 'Optimized Stop Order',
        'no_results'    => 'Select deliveries and click "Optimize Route" to see results',
        'assign_title'  => 'Assign to Driver',
        'select_driver' => 'Select a driver...',
        'no_drivers'    => 'No drivers available',
        'btn_assign'    => 'Assign All to Driver',
        'js_no_driver'  => 'Please select a driver first',
        'js_no_route'   => 'No optimized route available. Please optimize first.',
        'js_optimizing' => 'Optimizing...',
        'js_assigning'  => 'Assigning...',
        'js_assigned'   => 'deliveries assigned to driver successfully!',
        'js_fail_opt'   => 'Failed to optimize route. Please try again.',
        'js_fail_asgn'  => 'Failed to assign deliveries. Please try again.',
        'js_err_opt'    => 'Error optimizing route: ',
        'stop_lbl'      => 'Stop',
        'order_lbl'     => 'Order #',
    ],
    'fr' => [
        'title'         => "Optimiseur d'itinéraire",
        'subtitle'      => "Planifiez des itinéraires de livraison optimisés pour un maximum d'efficacité",
        'stat_total'    => 'Livraisons en attente',
        'stat_gps'      => 'Avec coordonnées GPS',
        'stat_nogps'    => 'Sans GPS',
        'leg_pickup'    => 'Lieu de ramassage',
        'leg_dropoff'   => 'Lieu de livraison',
        'leg_route'     => "Itinéraire optimisé",
        'select_del'    => 'Sélectionner les livraisons',
        'select_hint'   => 'Sélectionnez plusieurs livraisons pour créer un itinéraire optimisé',
        'no_pending'    => 'Aucune livraison en attente disponible',
        'btn_optimize'  => "Optimiser l'itinéraire",
        'results_title' => "Résultats de l'itinéraire",
        'total_dist'    => 'Distance totale',
        'est_time'      => 'Temps estimé',
        'stop_order'    => 'Ordre des arrêts optimisé',
        'no_results'    => 'Sélectionnez des livraisons et cliquez sur « Optimiser l\'itinéraire » pour voir les résultats',
        'assign_title'  => 'Assigner à un livreur',
        'select_driver' => 'Choisir un livreur...',
        'no_drivers'    => 'Aucun livreur disponible',
        'btn_assign'    => 'Tout assigner au livreur',
        'js_no_driver'  => 'Veuillez d\'abord sélectionner un livreur',
        'js_no_route'   => 'Aucun itinéraire optimisé disponible. Veuillez d\'abord optimiser.',
        'js_optimizing' => 'Optimisation...',
        'js_assigning'  => 'Assignation...',
        'js_assigned'   => 'livraisons assignées au livreur avec succès !',
        'js_fail_opt'   => "Échec de l'optimisation de l'itinéraire. Veuillez réessayer.",
        'js_fail_asgn'  => 'Échec de l\'assignation des livraisons. Veuillez réessayer.',
        'js_err_opt'    => "Erreur lors de l'optimisation : ",
        'stop_lbl'      => 'Arrêt',
        'order_lbl'     => 'Commande #',
    ],
];
$t = $t[$currentLang] ?? $t['en'];

ob_start();
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        color: #333;
    }

    .route-optimizer-container {
        padding: 30px;
        max-width: 1600px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 32px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
    }

    .page-header p {
        font-size: 16px;
        color: #666;
    }

    .stats-bar {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        flex: 1;
    }

    .stat-card .label {
        font-size: 14px;
        color: #666;
        margin-bottom: 8px;
    }

    .stat-card .value {
        font-size: 28px;
        font-weight: 600;
        color: #00b207;
    }

    .stat-card.warning .value {
        color: #ff6b35;
    }

    .main-layout {
        display: grid;
        grid-template-columns: 60% 40%;
        gap: 30px;
    }

    .map-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }

    #routeMap {
        height: 600px;
        border-radius: 8px;
        border: 2px solid #e0e0e0;
    }

    .map-legend {
        margin-top: 15px;
        display: flex;
        gap: 20px;
        font-size: 13px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .legend-marker {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .legend-marker.pickup {
        background-color: #00b207;
    }

    .legend-marker.dropoff {
        background-color: #2196F3;
    }

    .legend-line {
        width: 30px;
        height: 3px;
        background: repeating-linear-gradient(
            to right,
            #2196F3 0,
            #2196F3 5px,
            transparent 5px,
            transparent 10px
        );
    }

    .controls-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .control-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }

    .control-card h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #1a1a1a;
    }

    .deliveries-list {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 15px;
    }

    .delivery-item {
        padding: 12px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        transition: all 0.2s;
    }

    .delivery-item:hover {
        border-color: #00b207;
        background-color: #f0fdf4;
    }

    .delivery-item.selected {
        border-color: #00b207;
        background-color: #f0fdf4;
    }

    .delivery-item.no-gps {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .delivery-item input[type="checkbox"] {
        margin-top: 4px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .delivery-info {
        flex: 1;
    }

    .delivery-info .order-number {
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 4px;
    }

    .delivery-info .shop-name {
        font-size: 13px;
        color: #00b207;
        margin-bottom: 4px;
    }

    .delivery-info .address {
        font-size: 12px;
        color: #666;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        margin-top: 4px;
    }

    .badge.no-gps {
        background-color: #ffebee;
        color: #c62828;
    }

    .badge.pending {
        background-color: #fff3e0;
        color: #e65100;
    }

    .badge.assigned {
        background-color: #e3f2fd;
        color: #1565c0;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Poppins', sans-serif;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-primary {
        background-color: #00b207;
        color: white;
        width: 100%;
    }

    .btn-primary:hover:not(:disabled) {
        background-color: #009106;
    }

    .btn-secondary {
        background-color: #2196F3;
        color: white;
        width: 100%;
    }

    .btn-secondary:hover:not(:disabled) {
        background-color: #1976D2;
    }

    .results-panel {
        display: none;
        padding-top: 15px;
        border-top: 1px solid #e0e0e0;
        margin-top: 15px;
    }

    .results-panel.visible {
        display: block;
    }

    .result-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 15px;
    }

    .result-stat {
        text-align: center;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .result-stat .label {
        font-size: 12px;
        color: #666;
        margin-bottom: 4px;
    }

    .result-stat .value {
        font-size: 20px;
        font-weight: 600;
        color: #00b207;
    }

    .ordered-stops {
        margin-top: 15px;
    }

    .ordered-stops h4 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #1a1a1a;
    }

    .stop-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        background-color: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .stop-number {
        width: 24px;
        height: 24px;
        background-color: #00b207;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 12px;
    }

    .driver-select {
        width: 100%;
        padding: 10px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 13px;
    }

    .alert-info {
        background-color: #e3f2fd;
        color: #1565c0;
        border-left: 4px solid #2196F3;
    }

    .alert-success {
        background-color: #f0fdf4;
        color: #15803d;
        border-left: 4px solid #00b207;
    }

    .alert-error {
        background-color: #ffebee;
        color: #c62828;
        border-left: 4px solid #f44336;
    }

    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spinner 0.6s linear infinite;
        margin-left: 8px;
    }

    @keyframes spinner {
        to { transform: rotate(360deg); }
    }

    /* Custom scrollbar */
    .deliveries-list::-webkit-scrollbar {
        width: 8px;
    }

    .deliveries-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .deliveries-list::-webkit-scrollbar-thumb {
        background: #00b207;
        border-radius: 4px;
    }

    .deliveries-list::-webkit-scrollbar-thumb:hover {
        background: #009106;
    }
</style>

<div class="route-optimizer-container">
    <div class="page-header">
        <h1><?= $t['title'] ?></h1>
        <p><?= $t['subtitle'] ?></p>
    </div>

    <?php
    $totalDeliveries = count($deliveries ?? []);
    $withGPS = 0;
    $withoutGPS = 0;

    foreach ($deliveries as $delivery) {
        if (!empty($delivery['shop_lat']) && !empty($delivery['shop_lng'])) {
            $withGPS++;
        } else {
            $withoutGPS++;
        }
    }
    ?>

    <div class="stats-bar">
        <div class="stat-card">
            <div class="label"><?= $t['stat_total'] ?></div>
            <div class="value"><?= $totalDeliveries ?></div>
        </div>
        <div class="stat-card">
            <div class="label"><?= $t['stat_gps'] ?></div>
            <div class="value"><?= $withGPS ?></div>
        </div>
        <div class="stat-card warning">
            <div class="label"><?= $t['stat_nogps'] ?></div>
            <div class="value"><?= $withoutGPS ?></div>
        </div>
    </div>

    <div class="main-layout">
        <!-- Left Column: Map -->
        <div class="map-container">
            <div id="routeMap"></div>
            <div class="map-legend">
                <div class="legend-item">
                    <div class="legend-marker pickup"></div>
                    <span><?= $t['leg_pickup'] ?></span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker dropoff"></div>
                    <span><?= $t['leg_dropoff'] ?></span>
                </div>
                <div class="legend-item">
                    <div class="legend-line"></div>
                    <span><?= $t['leg_route'] ?></span>
                </div>
            </div>
        </div>

        <!-- Right Column: Controls -->
        <div class="controls-container">
            <!-- Deliveries Selection -->
            <div class="control-card">
                <h3><?= $t['select_del'] ?></h3>
                <div class="alert alert-info">
                    <?= $t['select_hint'] ?>
                </div>
                <div class="deliveries-list">
                    <?php if (empty($deliveries)): ?>
                        <p style="text-align: center; color: #666; padding: 20px;"><?= $t['no_pending'] ?></p>
                    <?php else: ?>
                        <?php foreach ($deliveries as $delivery): ?>
                            <?php
                            $hasGPS = !empty($delivery['shop_lat']) && !empty($delivery['shop_lng']);
                            $itemClass = 'delivery-item';
                            if (!$hasGPS) {
                                $itemClass .= ' no-gps';
                            }
                            ?>
                            <div class="<?= $itemClass ?>" data-delivery-id="<?= htmlspecialchars($delivery['id']) ?>"
                                 <?php if ($hasGPS): ?>
                                 data-shop-lat="<?= htmlspecialchars($delivery['shop_lat']) ?>"
                                 data-shop-lng="<?= htmlspecialchars($delivery['shop_lng']) ?>"
                                 data-address="<?= htmlspecialchars($delivery['delivery_address']) ?>"
                                 data-shop-name="<?= htmlspecialchars($delivery['shop_name']) ?>"
                                 data-order-number="<?= htmlspecialchars($delivery['order_number']) ?>"
                                 <?php endif; ?>
                            >
                                <?php if ($hasGPS): ?>
                                    <input type="checkbox" class="delivery-checkbox" value="<?= htmlspecialchars($delivery['id']) ?>">
                                <?php endif; ?>
                                <div class="delivery-info">
                                    <div class="order-number">Order #<?= htmlspecialchars($delivery['order_number']) ?></div>
                                    <div class="shop-name"><?= htmlspecialchars($delivery['shop_name']) ?></div>
                                    <div class="address"><?= htmlspecialchars($delivery['delivery_address']) ?></div>
                                    <?php if (!$hasGPS): ?>
                                        <span class="badge no-gps">No GPS</span>
                                    <?php else: ?>
                                        <span class="badge <?= strtolower($delivery['status']) ?>">
                                            <?= htmlspecialchars(ucfirst($delivery['status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button class="btn btn-primary" id="optimizeBtn" disabled>
                    <?= $t['btn_optimize'] ?>
                </button>
            </div>

            <!-- Results Panel -->
            <div class="control-card">
                <h3><?= $t['results_title'] ?></h3>
                <div class="results-panel" id="resultsPanel">
                    <div class="result-stats">
                        <div class="result-stat">
                            <div class="label"><?= $t['total_dist'] ?></div>
                            <div class="value" id="totalDistance">-</div>
                        </div>
                        <div class="result-stat">
                            <div class="label"><?= $t['est_time'] ?></div>
                            <div class="value" id="totalTime">-</div>
                        </div>
                    </div>
                    <div class="ordered-stops">
                        <h4><?= $t['stop_order'] ?></h4>
                        <div id="stopsList"></div>
                    </div>
                </div>
                <div id="noResultsMessage" style="text-align: center; color: #666; padding: 20px;">
                    <?= $t['no_results'] ?>
                </div>
            </div>

            <!-- Driver Assignment -->
            <div class="control-card">
                <h3><?= $t['assign_title'] ?></h3>
                <select class="driver-select" id="driverSelect">
                    <option value=""><?= $t['select_driver'] ?></option>
                    <?php if (!empty($drivers)): ?>
                        <?php foreach ($drivers as $driver): ?>
                            <?php
                            $available = ($driver['active_deliveries'] ?? 0) < ($driver['max_deliveries'] ?? 10);
                            ?>
                            <option value="<?= htmlspecialchars($driver['driver_id']) ?>" <?= $available ? '' : 'disabled' ?>>
                                <?= htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']) ?>
                                (<?= $driver['active_deliveries'] ?? 0 ?>/<?= $driver['max_deliveries'] ?? 10 ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled><?= $t['no_drivers'] ?></option>
                    <?php endif; ?>
                </select>
                <button class="btn btn-secondary" id="assignBtn" disabled>
                    <?= $t['btn_assign'] ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const _T = {
        noDriver:   <?= json_encode($t['js_no_driver']) ?>,
        noRoute:    <?= json_encode($t['js_no_route']) ?>,
        optimizing: <?= json_encode($t['js_optimizing']) ?>,
        assigning:  <?= json_encode($t['js_assigning']) ?>,
        assigned:   <?= json_encode($t['js_assigned']) ?>,
        failOpt:    <?= json_encode($t['js_fail_opt']) ?>,
        failAsgn:   <?= json_encode($t['js_fail_asgn']) ?>,
        errOpt:     <?= json_encode($t['js_err_opt']) ?>,
        stopLbl:    <?= json_encode($t['stop_lbl']) ?>,
        orderLbl:   <?= json_encode($t['order_lbl']) ?>,
        btnOptimize:<?= json_encode($t['btn_optimize']) ?>,
        btnAssign:  <?= json_encode($t['btn_assign']) ?>,
    };

    // Initialize map
    const map = L.map('routeMap').setView([45.5017, -73.5673], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // State
    let selectedDeliveries = [];
    let markers = [];
    let routeLine = null;
    let orderedDeliveryIds = [];

    // Custom marker icons
    const pickupIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #00b207; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    const dropoffIcon = (number) => L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: #2196F3; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">${number}</div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    // Handle checkbox changes
    document.querySelectorAll('.delivery-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const deliveryItem = this.closest('.delivery-item');

            if (this.checked) {
                deliveryItem.classList.add('selected');
                selectedDeliveries.push({
                    id: deliveryItem.dataset.deliveryId,
                    lat: parseFloat(deliveryItem.dataset.shopLat),
                    lng: parseFloat(deliveryItem.dataset.shopLng),
                    address: deliveryItem.dataset.address,
                    shopName: deliveryItem.dataset.shopName,
                    orderNumber: deliveryItem.dataset.orderNumber
                });
            } else {
                deliveryItem.classList.remove('selected');
                selectedDeliveries = selectedDeliveries.filter(d => d.id !== deliveryItem.dataset.deliveryId);
            }

            updateMapMarkers();
            document.getElementById('optimizeBtn').disabled = selectedDeliveries.length < 2;
        });
    });

    // Update map markers
    function updateMapMarkers() {
        // Clear existing markers
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        // Clear route line
        if (routeLine) {
            map.removeLayer(routeLine);
            routeLine = null;
        }

        // Add markers for selected deliveries
        selectedDeliveries.forEach((delivery, index) => {
            const marker = L.marker([delivery.lat, delivery.lng], {
                icon: pickupIcon
            }).addTo(map);

            marker.bindPopup(`
                <strong>Order #${delivery.orderNumber}</strong><br>
                <em>${delivery.shopName}</em><br>
                ${delivery.address}
            `);

            markers.push(marker);
        });

        // Fit bounds if markers exist
        if (markers.length > 0) {
            const group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    // Optimize route
    document.getElementById('optimizeBtn').addEventListener('click', async function() {
        const btn = this;
        const originalText = btn.textContent;

        btn.disabled = true;
        btn.innerHTML = _T.optimizing + '<span class="loading-spinner"></span>';

        try {
            const deliveryIds = selectedDeliveries.map(d => d.id);

            const response = await fetch('/admin/delivery/optimize-route', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ delivery_ids: deliveryIds })
            });

            const result = await response.json();

            if (result.success) {
                // Store ordered IDs
                orderedDeliveryIds = result.ordered_delivery_ids || [];

                // Clear existing markers and route
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];
                if (routeLine) {
                    map.removeLayer(routeLine);
                }

                // Draw route line
                if (result.route && result.route.length > 0) {
                    const routeCoords = result.route.map(point => [point.lat, point.lng]);
                    routeLine = L.polyline(routeCoords, {
                        color: '#2196F3',
                        weight: 3,
                        opacity: 0.7,
                        dashArray: '10, 10'
                    }).addTo(map);
                }

                // Add numbered markers in optimized order
                orderedDeliveryIds.forEach((deliveryId, index) => {
                    const delivery = selectedDeliveries.find(d => d.id == deliveryId);
                    if (delivery) {
                        const marker = L.marker([delivery.lat, delivery.lng], {
                            icon: dropoffIcon(index + 1)
                        }).addTo(map);

                        marker.bindPopup(`
                            <strong>Stop ${index + 1}</strong><br>
                            Order #${delivery.orderNumber}<br>
                            <em>${delivery.shopName}</em><br>
                            ${delivery.address}
                        `);

                        markers.push(marker);
                    }
                });

                // Fit bounds to route
                if (routeLine) {
                    map.fitBounds(routeLine.getBounds().pad(0.1));
                }

                // Update results panel
                document.getElementById('totalDistance').textContent =
                    (result.total_distance_km || 0).toFixed(2) + ' km';
                document.getElementById('totalTime').textContent =
                    Math.round(result.estimated_time_min || 0) + ' min';

                // Build stops list
                const stopsList = document.getElementById('stopsList');
                stopsList.innerHTML = '';
                orderedDeliveryIds.forEach((deliveryId, index) => {
                    const delivery = selectedDeliveries.find(d => d.id == deliveryId);
                    if (delivery) {
                        const stopItem = document.createElement('div');
                        stopItem.className = 'stop-item';
                        stopItem.innerHTML = `
                            <div class="stop-number">${index + 1}</div>
                            <div>
                                <strong>${_T.orderLbl}${delivery.orderNumber}</strong><br>
                                <small>${delivery.shopName}</small>
                            </div>
                        `;
                        stopsList.appendChild(stopItem);
                    }
                });

                // Show results panel
                document.getElementById('resultsPanel').classList.add('visible');
                document.getElementById('noResultsMessage').style.display = 'none';
                document.getElementById('assignBtn').disabled = false;

            } else {
                alert(_T.errOpt + (result.message || ''));
            }
        } catch (error) {
            console.error('Error:', error);
            alert(_T.failOpt);
        } finally {
            btn.disabled = false;
            btn.textContent = _T.btnOptimize;
        }
    });

    // Assign to driver
    document.getElementById('assignBtn').addEventListener('click', async function() {
        const driverSelect = document.getElementById('driverSelect');
        const driverId = driverSelect.value;

        if (!driverId) {
            alert(_T.noDriver);
            return;
        }

        if (orderedDeliveryIds.length === 0) {
            alert(_T.noRoute);
            return;
        }

        const btn = this;
        const originalText = btn.textContent;

        btn.disabled = true;
        btn.innerHTML = _T.assigning + '<span class="loading-spinner"></span>';

        try {
            let successCount = 0;
            let failCount = 0;

            // Assign each delivery in order
            for (const deliveryId of orderedDeliveryIds) {
                try {
                    const response = await fetch('/admin/delivery/assign-driver', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            delivery_id: deliveryId,
                            driver_id: driverId
                        })
                    });

                    const result = await response.json();
                    if (result.success) {
                        successCount++;
                    } else {
                        failCount++;
                    }
                } catch (error) {
                    console.error('Error assigning delivery:', deliveryId, error);
                    failCount++;
                }
            }

            if (successCount > 0) {
                alert(successCount + ' ' + _T.assigned);
                window.location.reload();
            } else {
                alert(_T.failAsgn);
            }

        } catch (error) {
            console.error('Error:', error);
            alert(_T.failAsgn);
        } finally {
            btn.disabled = false;
            btn.textContent = _T.btnAssign;
        }
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
