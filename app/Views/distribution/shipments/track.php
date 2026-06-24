<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'page_title'        => 'Track Your Shipment',
        'header_title'      => 'Track Your Shipment',
        'header_sub'        => 'Enter your shipment number to see real-time status',
        'search_placeholder'=> 'Enter shipment number (e.g., SHP-20260115-ABC123)',
        'btn_track'         => 'Track',
        'shipped_by'        => 'Shipped by',
        'status_draft'      => 'Order Being Prepared',
        'status_submitted'  => 'Processing',
        'status_quoted'     => 'Awaiting Confirmation',
        'status_paid'       => 'Payment Received',
        'status_scheduled'  => 'Pickup Scheduled',
        'status_picked_up'  => 'Package Picked Up',
        'status_in_transit' => 'In Transit',
        'status_delivered'  => 'Delivered',
        'status_completed'  => 'Completed',
        'scheduled_for'     => 'Scheduled for',
        'picked_up'         => 'Picked up',
        'origin'            => 'Origin',
        'destination'       => 'Destination',
        'stops_label'       => '%d Stops',
        'delivery_stops'    => 'Delivery Stops',
        'tracking_history'  => 'Tracking History',
        'not_found_title'   => 'Shipment Not Found',
        'powered_by'        => 'Powered by',
    ],
    'fr' => [
        'page_title'        => 'Suivre votre envoi',
        'header_title'      => 'Suivre votre envoi',
        'header_sub'        => 'Entrez votre num&#233;ro d\'envoi pour voir le statut en temps r&#233;el',
        'search_placeholder'=> 'Num&#233;ro d\'envoi (ex\u00a0: SHP-20260115-ABC123)',
        'btn_track'         => 'Suivre',
        'shipped_by'        => 'Exp&#233;di&#233; par',
        'status_draft'      => 'Commande en pr&#233;paration',
        'status_submitted'  => 'En traitement',
        'status_quoted'     => 'En attente de confirmation',
        'status_paid'       => 'Paiement re&#231;u',
        'status_scheduled'  => 'Ramassage planifi&#233;',
        'status_picked_up'  => 'Colis ramass&#233;',
        'status_in_transit' => 'En transit',
        'status_delivered'  => 'Livr&#233;',
        'status_completed'  => 'Compl&#233;t&#233;',
        'scheduled_for'     => 'Planifi&#233; pour le',
        'picked_up'         => 'Ramass&#233; le',
        'origin'            => 'Origine',
        'destination'       => 'Destination',
        'stops_label'       => '%d arr&#234;ts',
        'delivery_stops'    => 'Arr&#234;ts de livraison',
        'tracking_history'  => 'Historique de suivi',
        'not_found_title'   => 'Envoi introuvable',
        'powered_by'        => 'Propuls&#233; par',
    ],
];
$currentPage = 'track';
$pageTitle = $translations[$currentLang]['page_title'] ?? $translations['en']['page_title'];
$_pageT = $translations[$currentLang] ?? $translations['en'];
require __DIR__ . '/../layout-header.php';
$t = $_pageT; unset($_pageT);
?>

        <div class="search-card">
            <form action="<?= url('distribution/shipments/track') ?>" method="GET" class="search-form">
                <input type="text" name="number" class="search-input"
                       placeholder="<?= htmlspecialchars(html_entity_decode($t['search_placeholder'], ENT_QUOTES, 'UTF-8')) ?>"
                       value="<?= htmlspecialchars($_GET['number'] ?? '') ?>">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> <?= $t['btn_track'] ?>
                </button>
            </form>
        </div>

        <?php if (isset($error)): ?>
            <div class="result-card">
                <div class="error-message">
                    <i class="fas fa-search"></i>
                    <h3><?= $t['not_found_title'] ?></h3>
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            </div>
        <?php elseif (isset($shipment)): ?>
            <?php
            $statusMessages = [
                'draft'     => $t['status_draft'],
                'submitted' => $t['status_submitted'],
                'quoted'    => $t['status_quoted'],
                'paid'      => $t['status_paid'],
                'scheduled' => $t['status_scheduled'],
                'picked_up' => $t['status_picked_up'],
                'in_transit'=> $t['status_in_transit'],
                'delivered' => $t['status_delivered'],
                'completed' => $t['status_completed'],
            ];
            ?>
            <div class="result-card">
                <div class="shipment-header">
                    <div class="shipment-number"><?= htmlspecialchars($shipment['shipment_number']) ?></div>
                    <div class="shipment-meta">
                        <?= $t['shipped_by'] ?> <?= htmlspecialchars($shipment['company_name']) ?>
                        &bull; <?= date('M j, Y', strtotime($shipment['created_at'])) ?>
                    </div>
                </div>

                <div class="status-banner <?= $shipment['status'] ?>">
                    <div class="status-text">
                        <h3><?= $statusMessages[$shipment['status']] ?? ucwords($shipment['status']) ?></h3>
                        <p>
                            <?php if ($shipment['scheduled_for']): ?>
                                <?= $t['scheduled_for'] ?> <?= date('M j, Y', strtotime($shipment['scheduled_for'])) ?>
                            <?php elseif ($shipment['actual_pickup_at']): ?>
                                <?= $t['picked_up'] ?> <?= date('M j, Y g:i A', strtotime($shipment['actual_pickup_at'])) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <span class="badge badge-<?= $shipment['status'] ?>">
                        <?= ucwords(str_replace('_', ' ', $shipment['status'])) ?>
                    </span>
                </div>

                <div class="shipment-details">
                    <div class="route-visual">
                        <div class="route-point">
                            <i class="fas fa-box"></i>
                            <h4><?= htmlspecialchars($shipment['pickup_city']) ?></h4>
                            <p><?= $t['origin'] ?></p>
                        </div>
                        <div class="route-line <?= in_array($shipment['status'], ['delivered', 'completed']) ? 'complete' : '' ?>">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="route-point">
                            <i class="fas fa-flag-checkered"></i>
                            <h4>
                                <?php if ($shipment['is_multi_drop'] && !empty($destinations)): ?>
                                    <?= sprintf($t['stops_label'], count($destinations)) ?>
                                <?php else: ?>
                                    <?= htmlspecialchars($shipment['destination_city']) ?>
                                <?php endif; ?>
                            </h4>
                            <p><?= $t['destination'] ?></p>
                        </div>
                    </div>

                    <?php if ($shipment['is_multi_drop'] && !empty($destinations)): ?>
                        <div class="destinations-list">
                            <h4><?= $t['delivery_stops'] ?></h4>
                            <?php foreach ($destinations as $dest): ?>
                                <div class="destination-item">
                                    <div class="destination-number"><?= $dest['sequence_order'] ?></div>
                                    <div class="destination-info">
                                        <h5><?= htmlspecialchars($dest['destination_name']) ?></h5>
                                        <p><?= htmlspecialchars($dest['city']) ?>, <?= htmlspecialchars($dest['province']) ?></p>
                                    </div>
                                    <span class="destination-status <?= $dest['status'] ?>">
                                        <?= ucwords($dest['status']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($statusHistory)): ?>
                    <div class="timeline">
                        <h4><?= $t['tracking_history'] ?></h4>
                        <div class="timeline-list">
                            <?php foreach ($statusHistory as $history): ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <h5><?= ucwords(str_replace('_', ' ', $history['new_status'])) ?></h5>
                                    <?php if ($history['notes']): ?>
                                        <p><?= htmlspecialchars($history['notes']) ?></p>
                                    <?php endif; ?>
                                    <time><?= date('M j, Y g:i A', strtotime($history['created_at'])) ?></time>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <footer class="track-footer">
        <p><?= $t['powered_by'] ?> <a href="<?= url('/') ?>">OCSAPP Distribution</a></p>
    </footer>
<?php require __DIR__ . '/../layout-footer.php'; ?>
