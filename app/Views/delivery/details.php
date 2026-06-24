<?php $currentPage = ''; include __DIR__ . '/layout-header.php'; ?>

<style>
    .details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 20px;
        transition: color 0.2s;
    }

    .back-button:hover {
        color: #2563eb;
    }

    .back-button svg {
        width: 20px;
        height: 20px;
    }

    /* GPS Status Bar */
    .gps-status-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 16px;
        border: 1px solid #e2e8f0;
    }

    .gps-status {
        font-size: 13px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
    }

    .gps-active {
        background: #dcfce7;
        color: #166534;
    }

    .gps-off {
        background: #f1f5f9;
        color: #64748b;
    }

    .gps-error {
        background: #fef2f2;
        color: #991b1b;
    }

    .gps-warning {
        background: #fef3c7;
        color: #92400e;
    }

    /* Route Map */
    .route-map-card {
        background: white;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .route-map-card h3 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #1f2937;
    }

    #routeMap {
        height: 300px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .map-nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 8px 16px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s;
    }

    .map-nav-btn:hover {
        background: #2563eb;
        color: white;
    }

    /* Status Timeline */
    .timeline-container {
        background: white;
        border-radius: 8px;
        padding: 30px 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .status-timeline {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
        margin-bottom: 10px;
    }

    .timeline-step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .timeline-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: all 0.3s;
    }

    .timeline-dot.completed {
        background: #00b207;
        color: white;
    }

    .timeline-dot.current {
        background: #3b82f6;
        color: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }

    .timeline-dot.pending {
        background: #e5e7eb;
        color: #9ca3af;
    }

    .timeline-label {
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 5px;
        color: #1f2937;
    }

    .timeline-time {
        font-size: 11px;
        color: #6b7280;
    }

    .timeline-connector {
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 3px;
        background: #e5e7eb;
        z-index: 1;
    }

    .timeline-connector-progress {
        height: 100%;
        background: #00b207;
        transition: width 0.5s ease;
    }

    /* Delivery Info Card */
    .delivery-info-card {
        background: white;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .info-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        align-items: center;
    }

    .info-left h2 {
        margin: 0 0 15px 0;
        font-size: 24px;
        color: #1f2937;
    }

    .info-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        margin-top: 3px;
    }

    .info-right {
        text-align: center;
    }

    .status-badge {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 15px;
        text-transform: capitalize;
    }

    .status-badge.assigned { background: #fef3c7; color: #92400e; }
    .status-badge.accepted { background: #dbeafe; color: #1e40af; }
    .status-badge.picked_up { background: #e0e7ff; color: #3730a3; }
    .status-badge.on_the_way { background: #ddd6fe; color: #5b21b6; }
    .status-badge.delivered { background: #d1fae5; color: #065f46; }
    .status-badge.failed { background: #fee2e2; color: #991b1b; }

    .delivery-type-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        background: #f3f4f6;
        color: #374151;
    }

    .delivery-type-badge.b2b { background: #fef3c7; color: #92400e; }

    /* Location Cards */
    .location-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .location-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .location-card h3 {
        margin: 0 0 15px 0;
        font-size: 16px;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .location-card h3 svg {
        width: 20px;
        height: 20px;
        color: #3b82f6;
    }

    .location-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .location-address {
        color: #6b7280;
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 10px;
    }

    .location-phone {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
    }

    .location-phone:hover {
        color: #2563eb;
    }

    /* Order Items Table */
    .order-items-card {
        background: white;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .order-items-card h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
        color: #1f2937;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table th {
        background: #f9fafb;
        padding: 12px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
    }

    .items-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        color: #1f2937;
    }

    .items-table tr:last-child td {
        border-bottom: none;
    }

    .order-totals {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid #e5e7eb;
        text-align: right;
    }

    .total-row {
        display: flex;
        justify-content: flex-end;
        gap: 50px;
        padding: 5px 0;
    }

    .total-row.grand-total {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin-top: 5px;
    }

    .total-label {
        color: #6b7280;
        min-width: 100px;
    }

    .total-value {
        font-weight: 600;
        color: #1f2937;
        min-width: 80px;
    }

    /* Order Notes */
    .order-notes {
        background: #fffbeb;
        border: 2px solid #fbbf24;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .order-notes h4 {
        margin: 0 0 8px 0;
        font-size: 14px;
        color: #92400e;
        font-weight: 600;
    }

    .order-notes p {
        margin: 0;
        color: #78350f;
        line-height: 1.6;
    }

    /* Action Section */
    .action-section {
        background: white;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-success {
        background: #00b207;
        color: white;
    }

    .btn-success:hover {
        background: #009206;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .proof-upload {
        margin-top: 15px;
        padding: 15px;
        background: #f9fafb;
        border-radius: 6px;
    }

    .proof-upload label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #374151;
    }

    .proof-upload input[type="file"] {
        width: 100%;
        padding: 8px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
    }

    .proof-image {
        margin-top: 15px;
        text-align: center;
    }

    .proof-image img {
        max-width: 100%;
        max-height: 400px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .delivery-complete-info {
        text-align: center;
        padding: 20px;
    }

    .delivery-complete-info h3 {
        color: #00b207;
        margin-bottom: 15px;
    }

    .complete-meta {
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    .rating-display {
        margin-top: 20px;
        padding: 15px;
        background: #f9fafb;
        border-radius: 6px;
    }

    .stars {
        color: #fbbf24;
        font-size: 20px;
        margin-bottom: 8px;
    }

    .review-text {
        color: #6b7280;
        font-style: italic;
    }

    /* Status History */
    .history-card {
        background: white;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .history-card h3 {
        margin: 0 0 20px 0;
        font-size: 18px;
        color: #1f2937;
    }

    .history-timeline {
        position: relative;
        padding-left: 30px;
    }

    .history-timeline::before {
        content: '';
        position: absolute;
        left: 6px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }

    .history-item {
        position: relative;
        padding-bottom: 20px;
    }

    .history-item:last-child {
        padding-bottom: 0;
    }

    .history-dot {
        position: absolute;
        left: -27px;
        top: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #3b82f6;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #e5e7eb;
    }

    .history-content {
        background: #f9fafb;
        padding: 12px 15px;
        border-radius: 6px;
    }

    .history-status {
        font-weight: 600;
        color: #1f2937;
        text-transform: capitalize;
        margin-bottom: 4px;
    }

    .history-meta {
        font-size: 12px;
        color: #6b7280;
    }

    .history-notes {
        margin-top: 8px;
        color: #4b5563;
        font-size: 13px;
    }

    .failure-reason {
        background: #fee2e2;
        border: 2px solid #ef4444;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }

    .failure-reason h4 {
        margin: 0 0 8px 0;
        color: #991b1b;
        font-size: 14px;
    }

    .failure-reason p {
        margin: 0;
        color: #7f1d1d;
    }

    .items-table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    /* Responsive */
    @media (max-width: 768px) {
        .details-container { padding: 15px; }
        .info-grid { grid-template-columns: 1fr; gap: 20px; }
        .info-right { text-align: left; }
        .location-cards { grid-template-columns: 1fr; }
        .status-timeline { flex-wrap: wrap; }
        .timeline-step { font-size: 12px; }
        .timeline-dot { width: 30px; height: 30px; font-size: 12px; }
        .timeline-connector { top: 15px; }
        .action-buttons { flex-direction: column; }
        .btn { width: 100%; }
        .items-table { min-width: 480px; }
        .total-row { gap: 20px; }
    }

    @media (max-width: 480px) {
        .details-container { padding: 10px; }
        .delivery-info-card, .order-items-card, .action-section, .history-card,
        .timeline-container, .route-map-card, .location-card { padding: 14px; }
        .info-left h2 { font-size: 18px; }
        .info-value { font-size: 14px; }
        .status-badge { font-size: 13px; padding: 7px 14px; }
        .timeline-step { flex: 0 0 33%; font-size: 11px; }
        .timeline-dot { width: 26px; height: 26px; font-size: 11px; }
        .timeline-connector { display: none; }
        .items-table { min-width: 360px; }
        .items-table th, .items-table td { padding: 8px 6px; font-size: 12px; }
        .order-items-card h3, .history-card h3 { font-size: 15px; }
        .total-row { gap: 12px; font-size: 13px; }
        .total-row.grand-total { font-size: 15px; }
        .complete-meta { gap: 16px; }
    }

    .loading { opacity: 0.6; pointer-events: none; }
</style>

<div class="details-container">
    <a href="/delivery/dashboard" class="back-button">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        <?php echo $fr ? 'Retour au tableau de bord' : 'Back to Dashboard'; ?>
    </a>

    <?php if (in_array($delivery['status'], ['accepted', 'picked_up', 'on_the_way'])): ?>
    <div class="gps-status-bar">
        <span>📡</span>
        <span><?php echo $fr ? 'Suivi GPS :' : 'GPS Tracking:'; ?></span>
        <span id="gps-status" class="gps-status gps-off"><?php echo $fr ? 'Initialisation...' : 'Initializing...'; ?></span>
    </div>
    <?php endif; ?>

    <!-- Status Timeline -->
    <div class="timeline-container">
        <?php
        $statuses = ['assigned', 'accepted', 'picked_up', 'on_the_way', 'delivered'];
        $currentStatus = $delivery['status'];
        $currentIndex = array_search($currentStatus, $statuses);

        $statusLabels = $fr ? [
            'assigned'   => 'Assignée',
            'accepted'   => 'Acceptée',
            'picked_up'  => 'Ramassée',
            'on_the_way' => 'En route',
            'delivered'  => 'Livrée',
        ] : [
            'assigned'   => 'Assigned',
            'accepted'   => 'Accepted',
            'picked_up'  => 'Picked Up',
            'on_the_way' => 'On the Way',
            'delivered'  => 'Delivered',
        ];

        $statusTimes = [
            'assigned' => $delivery['assigned_at'] ?? null,
            'accepted' => $delivery['accepted_at'] ?? null,
            'picked_up' => $delivery['picked_up_at'] ?? null,
            'on_the_way' => $delivery['on_the_way_at'] ?? null,
            'delivered' => $delivery['delivered_at'] ?? null
        ];

        // Calculate progress percentage
        $progress = 0;
        if ($currentStatus === 'failed') {
            $progress = 0;
        } else {
            $progress = ($currentIndex / (count($statuses) - 1)) * 100;
        }
        ?>

        <div class="status-timeline">
            <div class="timeline-connector">
                <div class="timeline-connector-progress" style="width: <?= $progress ?>%"></div>
            </div>

            <?php foreach ($statuses as $idx => $status): ?>
                <?php
                $class = 'pending';
                if ($currentStatus === 'failed') {
                    $class = 'pending';
                } elseif ($idx < $currentIndex) {
                    $class = 'completed';
                } elseif ($idx === $currentIndex) {
                    $class = 'current';
                }
                ?>
                <div class="timeline-step">
                    <div class="timeline-dot <?= $class ?>">
                        <?php if ($class === 'completed'): ?>
                            &#10003;
                        <?php else: ?>
                            <?= $idx + 1 ?>
                        <?php endif; ?>
                    </div>
                    <div class="timeline-label"><?= $statusLabels[$status] ?></div>
                    <?php if ($statusTimes[$status]): ?>
                        <div class="timeline-time">
                            <?php
                            $ts = strtotime($statusTimes[$status]);
                            if ($fr) {
                                $frMonthsTl = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                                echo (int)date('j', $ts) . ' ' . $frMonthsTl[(int)date('n', $ts)] . ' à ' . date('G', $ts) . 'h' . date('i', $ts);
                            } else {
                                echo date('M j, g:i A', $ts);
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (!empty($delivery['pickup_address']) || !empty($delivery['delivery_address'])): ?>
    <div class="route-map-card">
        <h3><?php echo $fr ? 'Carte du trajet' : 'Route Map'; ?></h3>
        <div id="routeMap"></div>
        <?php
        $navUrl = 'https://www.google.com/maps/dir/';
        if (!empty($delivery['pickup_address'])) $navUrl .= urlencode($delivery['pickup_address']) . '/';
        if (!empty($delivery['delivery_address'])) $navUrl .= urlencode($delivery['delivery_address']);
        ?>
        <a href="<?= $navUrl ?>" target="_blank" class="map-nav-btn">
            🧭 <?php echo $fr ? 'Ouvrir dans Google Maps' : 'Open in Google Maps'; ?>
        </a>
    </div>
    <?php endif; ?>

    <!-- Delivery Info Card -->
    <div class="delivery-info-card">
        <div class="info-grid">
            <div class="info-left">
                <h2><?php echo $fr ? 'Commande n°' : 'Order #'; ?><?= htmlspecialchars($delivery['order_number']) ?></h2>
                <span class="delivery-type-badge <?= $delivery['delivery_type'] === 'B2B' ? 'b2b' : '' ?>">
                    <?= htmlspecialchars($delivery['delivery_type']) ?>
                </span>

                <div class="info-meta">
                    <div class="info-item">
                        <span class="info-label"><?php echo $fr ? 'Frais de livraison' : 'Delivery Fee'; ?></span>
                        <span class="info-value">$<?= number_format($delivery['delivery_fee'], 2) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><?php echo $fr ? 'Distance' : 'Distance'; ?></span>
                        <span class="info-value"><?= number_format($delivery['distance_km'], 1) ?> km</span>
                    </div>
                    <?php if (!empty($delivery['actual_time'])): ?>
                    <div class="info-item">
                        <span class="info-label"><?php echo $fr ? 'Durée de livraison' : 'Delivery Time'; ?></span>
                        <span class="info-value"><?= $delivery['actual_time'] ?> min</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="info-right">
                <?php
                $statusBadgeLabelsEn = ['assigned'=>'Assigned','accepted'=>'Accepted','picked_up'=>'Picked Up','on_the_way'=>'On the Way','delivered'=>'Delivered','failed'=>'Failed'];
                $statusBadgeLabelsFr = ['assigned'=>'Assignée','accepted'=>'Acceptée','picked_up'=>'Ramassée','on_the_way'=>'En route','delivered'=>'Livrée','failed'=>'Échouée'];
                $badgeLabel = $fr
                    ? ($statusBadgeLabelsFr[$delivery['status']] ?? ucfirst($delivery['status']))
                    : ($statusBadgeLabelsEn[$delivery['status']] ?? ucfirst(str_replace('_',' ',$delivery['status'])));
                ?>
                <div class="status-badge <?= $delivery['status'] ?>">
                    <?= htmlspecialchars($badgeLabel) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Pickup & Dropoff Cards -->
    <div class="location-cards">
        <div class="location-card">
            <h3>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <?php echo $fr ? 'Lieu de ramassage' : 'Pickup Location'; ?>
            </h3>
            <div class="location-name"><?= htmlspecialchars($delivery['shop_name'] ?? 'Shop') ?></div>
            <div class="location-address">
                <?= nl2br(htmlspecialchars($delivery['pickup_address'] ?? $delivery['shop_address'] ?? 'N/A')) ?>
            </div>
            <?php if (!empty($delivery['shop_phone'])): ?>
                <a href="tel:<?= htmlspecialchars($delivery['shop_phone']) ?>" class="location-phone">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <?= htmlspecialchars($delivery['shop_phone']) ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="location-card">
            <h3>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <?php echo $fr ? 'Lieu de livraison' : 'Dropoff Location'; ?>
            </h3>
            <?php if ($delivery['delivery_type'] === 'B2B'): ?>
                <div class="location-name"><?= htmlspecialchars($delivery['business_name'] ?? 'Business') ?></div>
                <div class="location-address">
                    <?= nl2br(htmlspecialchars($delivery['dist_delivery_address'] ?? 'N/A')) ?>
                </div>
            <?php else: ?>
                <div class="location-name">
                    <?= htmlspecialchars($delivery['customer_first_name'] . ' ' . $delivery['customer_last_name']) ?>
                </div>
                <div class="location-address">
                    <?= nl2br(htmlspecialchars($delivery['delivery_address'])) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($delivery['customer_phone'])): ?>
                <a href="tel:<?= htmlspecialchars($delivery['customer_phone']) ?>" class="location-phone">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <?= htmlspecialchars($delivery['customer_phone']) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Notes -->
    <?php if (!empty($delivery['order_notes'])): ?>
    <div class="order-notes">
        <h4><?php echo $fr ? 'Notes de commande' : 'Order Notes'; ?></h4>
        <p><?= nl2br(htmlspecialchars($delivery['order_notes'])) ?></p>
    </div>
    <?php endif; ?>

    <!-- Order Items -->
    <div class="order-items-card">
        <h3><?php echo $fr ? 'Articles commandés' : 'Order Items'; ?></h3>
        <div class="items-table-scroll">
        <table class="items-table">
            <thead>
                <tr>
                    <th><?php echo $fr ? 'Produit' : 'Product'; ?></th>
                    <th>SKU</th>
                    <th style="text-align: center;"><?php echo $fr ? 'Quantité' : 'Quantity'; ?></th>
                    <th style="text-align: right;"><?php echo $fr ? 'Prix' : 'Price'; ?></th>
                    <th style="text-align: right;"><?php echo $fr ? 'Total' : 'Total'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name'] ?? $item['name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($item['sku'] ?? 'N/A') ?></td>
                    <td style="text-align: center;"><?= $item['quantity'] ?></td>
                    <td style="text-align: right;">$<?= number_format($item['price'] ?? $item['unit_price'] ?? 0, 2) ?></td>
                    <td style="text-align: right;">$<?= number_format(($item['price'] ?? $item['unit_price'] ?? 0) * $item['quantity'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <div class="order-totals">
            <div class="total-row">
                <span class="total-label"><?php echo $fr ? 'Sous-total :' : 'Subtotal:'; ?></span>
                <span class="total-value">$<?= number_format($delivery['subtotal'], 2) ?></span>
            </div>
            <div class="total-row">
                <span class="total-label"><?php echo $fr ? 'Taxes :' : 'Tax:'; ?></span>
                <span class="total-value">$<?= number_format($delivery['tax'], 2) ?></span>
            </div>
            <div class="total-row">
                <span class="total-label"><?php echo $fr ? 'Frais de livraison :' : 'Delivery Fee:'; ?></span>
                <span class="total-value">$<?= number_format($delivery['delivery_fee'], 2) ?></span>
            </div>
            <div class="total-row grand-total">
                <span class="total-label"><?php echo $fr ? 'Total :' : 'Total:'; ?></span>
                <span class="total-value">$<?= number_format($delivery['total'], 2) ?></span>
            </div>
        </div>
    </div>

    <!-- Action Section -->
    <div class="action-section">
        <?php if ($delivery['status'] === 'assigned'): ?>
            <div class="action-buttons">
                <button onclick="acceptDelivery()" class="btn btn-success"><?php echo $fr ? 'Accepter la livraison' : 'Accept Delivery'; ?></button>
                <button onclick="rejectDelivery()" class="btn btn-danger"><?php echo $fr ? 'Refuser la livraison' : 'Reject Delivery'; ?></button>
            </div>

        <?php elseif ($delivery['status'] === 'accepted'): ?>
            <div class="action-buttons">
                <button onclick="updateStatus('picked_up')" class="btn btn-primary"><?php echo $fr ? 'Marquer comme ramassé' : 'Mark as Picked Up'; ?></button>
            </div>

        <?php elseif ($delivery['status'] === 'picked_up'): ?>
            <div class="action-buttons">
                <button onclick="updateStatus('on_the_way')" class="btn btn-primary"><?php echo $fr ? 'Marquer en route' : 'Mark On the Way'; ?></button>
            </div>

        <?php elseif ($delivery['status'] === 'on_the_way'): ?>
            <div class="action-buttons">
                <button onclick="markAsDelivered()" class="btn btn-success"><?php echo $fr ? 'Marquer comme livré' : 'Mark as Delivered'; ?></button>
            </div>
            <div class="proof-upload">
                <label for="proof_photo"><?php echo $fr ? 'Preuve de livraison (photo facultative)' : 'Proof of Delivery (Optional Photo)'; ?></label>
                <input type="file" id="proof_photo" accept="image/*" capture="environment">
            </div>

        <?php elseif ($delivery['status'] === 'delivered'): ?>
            <div class="delivery-complete-info">
                <h3><?php echo $fr ? 'Livraison terminée' : 'Delivery Completed'; ?></h3>
                <div class="complete-meta">
                    <div class="info-item">
                        <span class="info-label"><?php echo $fr ? 'Livré le' : 'Delivered At'; ?></span>
                        <span class="info-value">
                            <?php
                            $tsDelivered = strtotime($delivery['delivered_at']);
                            if ($fr) {
                                $frMonthsDet = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                                echo (int)date('j', $tsDelivered) . ' ' . $frMonthsDet[(int)date('n', $tsDelivered)] . ' ' . date('Y', $tsDelivered) . ' à ' . date('G', $tsDelivered) . 'h' . date('i', $tsDelivered);
                            } else {
                                echo date('M j, Y g:i A', $tsDelivered);
                            }
                            ?>
                        </span>
                    </div>
                    <?php if (!empty($delivery['actual_time'])): ?>
                    <div class="info-item">
                        <span class="info-label"><?php echo $fr ? 'Durée de livraison' : 'Delivery Time'; ?></span>
                        <span class="info-value"><?= $delivery['actual_time'] ?> min</span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($delivery['proof_of_delivery'])): ?>
                <div class="proof-image">
                    <h4><?php echo $fr ? 'Preuve de livraison' : 'Proof of Delivery'; ?></h4>
                    <img src="/uploads/delivery/<?= htmlspecialchars($delivery['proof_of_delivery']) ?>" alt="<?php echo $fr ? 'Preuve de livraison' : 'Proof of Delivery'; ?>">
                </div>
                <?php endif; ?>

                <?php if (!empty($delivery['rating'])): ?>
                <div class="rating-display">
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= $delivery['rating'] ? '★' : '☆' ?>
                        <?php endfor; ?>
                    </div>
                    <?php if (!empty($delivery['review'])): ?>
                        <div class="review-text">"<?= htmlspecialchars($delivery['review']) ?>"</div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

        <?php elseif ($delivery['status'] === 'failed'): ?>
            <div class="failure-reason">
                <h4><?php echo $fr ? 'Livraison échouée' : 'Delivery Failed'; ?></h4>
                <p><?= htmlspecialchars($delivery['failure_reason'] ?? ($fr ? 'Aucune raison fournie' : 'No reason provided')) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Status History -->
    <?php if (!empty($history)): ?>
    <div class="history-card">
        <h3><?php echo $fr ? 'Historique des statuts' : 'Status History'; ?></h3>
        <div class="history-timeline">
            <?php foreach ($history as $item): ?>
            <div class="history-item">
                <div class="history-dot"></div>
                <div class="history-content">
                    <?php
                    $histStatusLabelsEn = ['assigned'=>'Assigned','accepted'=>'Accepted','picked_up'=>'Picked Up','on_the_way'=>'On the Way','delivered'=>'Delivered','failed'=>'Failed','cancelled'=>'Cancelled'];
                    $histStatusLabelsFr = ['assigned'=>'Assignée','accepted'=>'Acceptée','picked_up'=>'Ramassée','on_the_way'=>'En route','delivered'=>'Livrée','failed'=>'Échouée','cancelled'=>'Annulée'];
                    $histLabel = $fr
                        ? ($histStatusLabelsFr[$item['status']] ?? ucfirst($item['status']))
                        : ($histStatusLabelsEn[$item['status']] ?? ucfirst(str_replace('_',' ',$item['status'])));
                    $tsHist = strtotime($item['created_at']);
                    if ($fr) {
                        $frMonthsHist = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                        $histDate = (int)date('j', $tsHist) . ' ' . $frMonthsHist[(int)date('n', $tsHist)] . ' ' . date('Y', $tsHist) . ' à ' . date('G', $tsHist) . 'h' . date('i', $tsHist);
                    } else {
                        $histDate = date('M j, Y g:i A', $tsHist);
                    }
                    ?>
                    <div class="history-status"><?= htmlspecialchars($histLabel) ?></div>
                    <div class="history-meta">
                        <?= $histDate ?>
                        <?php if (!empty($item['first_name']) && !empty($item['last_name'])): ?>
                            - <?php echo $fr ? 'par' : 'by'; ?> <?= htmlspecialchars($item['first_name'] . ' ' . $item['last_name']) ?>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($item['notes'])): ?>
                        <div class="history-notes"><?= htmlspecialchars($item['notes']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($item['latitude']) && !empty($item['longitude'])): ?>
                        <div class="history-notes">
                            <?php echo $fr ? 'Position' : 'Location'; ?>: <?= number_format($item['latitude'], 6) ?>, <?= number_format($item['longitude'], 6) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const deliveryId = <?= $delivery['id'] ?>;
const csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';
const _L = {
    confirmUpdate:   <?php echo $fr ? "'Confirmer la mise à jour du statut ?'" : "'Are you sure you want to update the status?'"; ?>,
    confirmAccept:   <?php echo $fr ? "'Accepter cette livraison ?'" : "'Accept this delivery?'"; ?>,
    confirmDeliver:  <?php echo $fr ? "'Marquer cette livraison comme terminée ?'" : "'Mark this delivery as completed?'"; ?>,
    rejectPrompt:    <?php echo $fr ? "'Veuillez indiquer la raison du refus :'" : "'Please provide a reason for rejecting this delivery:'"; ?>,
    errUpdate:       <?php echo $fr ? "'Échec de la mise à jour du statut'" : "'Failed to update status'"; ?>,
    errAccept:       <?php echo $fr ? "'Échec de l\\'acceptation de la livraison'" : "'Failed to accept delivery'"; ?>,
    errReject:       <?php echo $fr ? "'Échec du refus de la livraison'" : "'Failed to reject delivery'"; ?>,
    errDeliver:      <?php echo $fr ? "'Échec du marquage comme livré'" : "'Failed to mark as delivered'"; ?>,
    errGeneric:      <?php echo $fr ? "'Une erreur est survenue. Veuillez réessayer.'" : "'An error occurred. Please try again.'"; ?>,
    mapPickup:       <?php echo $fr ? "'<b>Ramassage</b>'" : "'<b>Pickup</b>'"; ?>,
    mapDropoff:      <?php echo $fr ? "'<b>Livraison</b>'" : "'<b>Delivery</b>'"; ?>,
    mapDriver:       <?php echo $fr ? "'<b>Votre position</b>'" : "'<b>Your Location</b>'"; ?>
};

function updateStatus(status) {
    if (!confirm(_L.confirmUpdate)) {
        return;
    }

    const formData = new FormData();
    formData.append('delivery_id', deliveryId);
    formData.append('status', status);
    formData.append('csrf_token', csrfToken);

    fetch('/delivery/status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || _L.errUpdate);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(_L.errGeneric);
    });
}

function acceptDelivery() {
    if (!confirm(_L.confirmAccept)) {
        return;
    }

    const formData = new FormData();
    formData.append('delivery_id', deliveryId);
    formData.append('csrf_token', csrfToken);

    fetch('/delivery/accept', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || _L.errAccept);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(_L.errGeneric);
    });
}

function rejectDelivery() {
    const reason = prompt(_L.rejectPrompt);
    if (!reason) {
        return;
    }

    const formData = new FormData();
    formData.append('delivery_id', deliveryId);
    formData.append('reason', reason);
    formData.append('csrf_token', csrfToken);

    fetch('/delivery/reject', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/delivery/dashboard';
        } else {
            alert(data.message || _L.errReject);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(_L.errGeneric);
    });
}

function markAsDelivered() {
    if (!confirm(_L.confirmDeliver)) {
        return;
    }

    const formData = new FormData();
    formData.append('delivery_id', deliveryId);
    formData.append('status', 'delivered');
    formData.append('csrf_token', csrfToken);

    const proofPhoto = document.getElementById('proof_photo');
    if (proofPhoto && proofPhoto.files.length > 0) {
        formData.append('proof_photo', proofPhoto.files[0]);
    }

    fetch('/delivery/status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || _L.errDeliver);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(_L.errGeneric);
    });
}
</script>

<?php if (in_array($delivery['status'], ['accepted', 'picked_up', 'on_the_way'])): ?>
<script src="<?= url('assets/js/driver-gps-tracker.js') ?>"></script>
<script>DriverGPSTracker.start(<?= (int)$delivery['id'] ?>);</script>
<?php endif; ?>

<?php if (!empty($delivery['pickup_address']) || !empty($delivery['delivery_address'])): ?>
<script>
(function() {
    const deliveryId = <?= (int)$delivery['id'] ?>;
    const map = L.map('routeMap').setView([45.5017, -73.5673], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    fetch('/api/delivery/location?delivery_id=' + deliveryId)
        .then(r => r.json())
        .then(data => {
            const bounds = [];

            if (data.pickup && data.pickup.lat) {
                const m = L.circleMarker([data.pickup.lat, data.pickup.lng], {
                    radius: 8, fillColor: '#00b207', fillOpacity: 1, color: 'white', weight: 2
                }).addTo(map).bindPopup(_L.mapPickup + '<br>' + (data.pickup.name || ''));
                bounds.push([data.pickup.lat, data.pickup.lng]);
            }

            if (data.dropoff && data.dropoff.lat) {
                const m = L.circleMarker([data.dropoff.lat, data.dropoff.lng], {
                    radius: 8, fillColor: '#ef4444', fillOpacity: 1, color: 'white', weight: 2
                }).addTo(map).bindPopup(_L.mapDropoff + '<br>' + (data.dropoff.address || ''));
                bounds.push([data.dropoff.lat, data.dropoff.lng]);
            }

            if (data.driver && data.driver.lat) {
                L.circleMarker([data.driver.lat, data.driver.lng], {
                    radius: 8, fillColor: '#3b82f6', fillOpacity: 1, color: 'white', weight: 2
                }).addTo(map).bindPopup(_L.mapDriver);
                bounds.push([data.driver.lat, data.driver.lng]);
            }

            if (bounds.length >= 2) {
                L.polyline(bounds, { color: '#3b82f6', weight: 3, opacity: 0.6, dashArray: '8,8' }).addTo(map);
                map.fitBounds(bounds, { padding: [40, 40] });
            } else if (bounds.length === 1) {
                map.setView(bounds[0], 14);
            }
        })
        .catch(e => console.error('Map load error:', e));

    // Refresh every 30s for active deliveries
    <?php if (in_array($delivery['status'], ['accepted', 'picked_up', 'on_the_way'])): ?>
    setInterval(() => {
        fetch('/api/delivery/location?delivery_id=' + deliveryId)
            .then(r => r.json())
            .then(data => {
                if (data.driver && data.driver.lat) {
                    // Update driver marker position
                    map.eachLayer(l => {
                        if (l instanceof L.CircleMarker && l.options.fillColor === '#3b82f6') {
                            l.setLatLng([data.driver.lat, data.driver.lng]);
                        }
                    });
                }
            });
    }, 30000);
    <?php endif; ?>
})();
</script>
<?php endif; ?>

<?php include __DIR__ . '/layout-footer.php'; ?>
