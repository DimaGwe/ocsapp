<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?php echo $fr ? 'fr-CA' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $fr ? 'Suivre votre livraison - OCSAPP' : 'Track Your Delivery - OCSAPP'; ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
            min-height: 100vh;
            padding: 20px;
            color: #2d3748;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px 0;
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            color: #00b207;
            letter-spacing: -1px;
            margin-bottom: 10px;
        }

        .tagline {
            color: #718096;
            font-size: 14px;
        }

        /* Search Form */
        .search-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .search-title {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .search-subtitle {
            color: #718096;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .search-form {
            max-width: 500px;
            margin: 0 auto;
        }

        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        input[type="text"] {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #00b207;
            box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
        }

        .btn-track {
            padding: 14px 32px;
            background: #00b207;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-track:hover {
            background: #009606;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);
        }

        /* Progress Tracker */
        .progress-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-number {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
        }

        .delivery-badge {
            display: inline-block;
            padding: 6px 14px;
            background: #e6f7e6;
            color: #00b207;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: capitalize;
        }

        /* Progress Steps */
        .progress-tracker {
            position: relative;
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            padding: 0 10px;
        }

        .progress-line {
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 4px;
            background: #e2e8f0;
            z-index: 1;
        }

        .progress-line-active {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: #00b207;
            transition: width 0.5s ease;
            border-radius: 2px;
        }

        .progress-step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .step-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: white;
            border: 4px solid #e2e8f0;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.3s;
        }

        .step-icon.completed {
            border-color: #00b207;
            background: #00b207;
            color: white;
        }

        .step-icon.active {
            border-color: #00b207;
            background: white;
            color: #00b207;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(0, 178, 7, 0.4);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(0, 178, 7, 0);
            }
        }

        .step-label {
            font-size: 12px;
            font-weight: 600;
            color: #718096;
            margin-bottom: 4px;
        }

        .step-label.active {
            color: #00b207;
        }

        .step-time {
            font-size: 11px;
            color: #a0aec0;
        }

        /* Status Card */
        .status-card {
            background: linear-gradient(135deg, #00b207 0%, #009606 100%);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 20px;
            color: white;
            box-shadow: 0 8px 24px rgba(0, 178, 7, 0.3);
        }

        .status-card.delivered {
            background: linear-gradient(135deg, #00b207 0%, #009a06 100%);
        }

        .status-card.failed {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .status-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .status-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .status-message {
            font-size: 16px;
            opacity: 0.95;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .driver-info {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            backdrop-filter: blur(10px);
        }

        .driver-name {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .driver-phone {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            transition: all 0.3s;
        }

        .driver-phone:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .proof-image {
            margin-top: 15px;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .proof-image img {
            width: 100%;
            max-width: 300px;
            display: block;
        }

        /* Details Card */
        .details-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-row {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #f7fafc;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #4a5568;
            min-width: 140px;
            font-size: 14px;
        }

        .detail-value {
            color: #2d3748;
            flex: 1;
            font-size: 14px;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 40px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 10px;
            bottom: 10px;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 25px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -34px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #00b207;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #00b207;
        }

        .timeline-content {
            background: #f7fafc;
            padding: 12px 16px;
            border-radius: 8px;
        }

        .timeline-status {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 4px;
            text-transform: capitalize;
        }

        .timeline-time {
            font-size: 12px;
            color: #718096;
            margin-bottom: 6px;
        }

        .timeline-notes {
            font-size: 13px;
            color: #4a5568;
            line-height: 1.5;
        }

        /* Alert */
        .alert {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #856404;
        }

        .alert.error {
            background: #fee;
            border-left-color: #dc3545;
            color: #721c24;
        }

        .alert-title {
            font-weight: 700;
            margin-bottom: 5px;
        }

        /* Track Another */
        .track-another {
            text-align: center;
            margin: 30px 0;
        }

        .track-another a {
            color: #00b207;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border: 2px solid #00b207;
            border-radius: 8px;
            display: inline-block;
            transition: all 0.3s;
        }

        .track-another a:hover {
            background: #00b207;
            color: white;
        }

        /* Live Map */
        .live-map-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        #trackingMap {
            height: 400px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
        }

        .map-last-update {
            text-align: center;
            margin-top: 10px;
            color: #718096;
            font-size: 13px;
        }

        .map-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #4a5568;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.2);
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 50px;
            padding: 20px;
            color: #718096;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .search-card {
                padding: 30px 20px;
            }

            .search-title {
                font-size: 24px;
            }

            .input-group {
                flex-direction: column;
            }

            .progress-section {
                padding: 20px;
            }

            .progress-tracker {
                flex-wrap: wrap;
                gap: 30px 0;
            }

            .progress-step {
                flex: 0 0 50%;
            }

            .progress-line {
                display: none;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-number {
                font-size: 20px;
            }

            .status-title {
                font-size: 24px;
            }

            .status-card {
                padding: 25px 20px;
            }

            .details-card {
                padding: 20px;
            }

            .detail-row {
                flex-direction: column;
                gap: 5px;
            }

            .detail-label {
                min-width: auto;
            }
        }

        @media (max-width: 480px) {
            .logo {
                font-size: 28px;
            }

            .search-title {
                font-size: 22px;
            }

            .progress-step {
                flex: 0 0 100%;
            }

            .step-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">OCSAPP</div>
            <div class="tagline"><?php echo $fr ? 'Livraison d\'épicerie zéro émission' : 'Zero-Emission Grocery Delivery'; ?></div>
        </div>

        <?php if (empty($delivery) && empty($trackingCode)): ?>
            <!-- State 1: Search Form -->
            <div class="search-card">
                <div class="status-icon">📦</div>
                <h1 class="search-title"><?php echo $fr ? 'Suivre votre livraison' : 'Track Your Delivery'; ?></h1>
                <p class="search-subtitle"><?php echo $fr ? 'Entrez votre numéro de commande ou code de suivi pour voir les mises à jour en temps réel' : 'Enter your order number or tracking code to see real-time delivery updates'; ?></p>

                <form method="GET" action="<?= url('track') ?>" class="search-form">
                    <div class="input-group">
                        <input
                            type="text"
                            name="code"
                            placeholder="<?php echo $fr ? 'Entrez le numéro de commande ou le code de suivi' : 'Enter order number or tracking code'; ?>"
                            required
                            autocomplete="off"
                            autofocus
                        >
                        <button type="submit" class="btn-track"><?php echo $fr ? 'Suivre' : 'Track'; ?></button>
                    </div>
                </form>
            </div>

        <?php elseif (empty($delivery) && !empty($trackingCode)): ?>
            <!-- State 3: Not Found -->
            <div class="search-card">
                <div class="alert error">
                    <div class="alert-title"><?php echo $fr ? 'Code de suivi introuvable' : 'Tracking Code Not Found'; ?></div>
                    <p><?php echo $fr ? 'Aucune livraison trouvée pour le code :' : 'No delivery found for tracking code:'; ?> <strong><?= htmlspecialchars($trackingCode) ?></strong></p>
                    <p><?php echo $fr ? 'Veuillez vérifier le code et réessayer.' : 'Please check the code and try again.'; ?></p>
                </div>

                <form method="GET" action="<?= url('track') ?>" class="search-form">
                    <div class="input-group">
                        <input
                            type="text"
                            name="code"
                            placeholder="<?php echo $fr ? 'Entrez le numéro de commande ou le code de suivi' : 'Enter order number or tracking code'; ?>"
                            required
                            autocomplete="off"
                            autofocus
                        >
                        <button type="submit" class="btn-track"><?php echo $fr ? 'Suivre' : 'Track'; ?></button>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- State 2: Tracking Details -->

            <!-- Progress Section -->
            <div class="progress-section">
                <div class="order-header">
                    <h1 class="order-number"><?php echo $fr ? 'Commande n°' : 'Order #'; ?><?= htmlspecialchars($delivery['order_number']) ?></h1>
                    <span class="delivery-badge"><?= htmlspecialchars($delivery['delivery_type'] ?? 'standard') ?> <?php echo $fr ? 'livraison' : 'delivery'; ?></span>
                </div>

                <!-- Progress Tracker -->
                <div class="progress-tracker">
                    <div class="progress-line">
                        <div class="progress-line-active" style="width: <?php
                            // Calculate progress percentage
                            $progress = 0;
                            if (!empty($delivery['delivered_at'])) $progress = 100;
                            elseif (!empty($delivery['on_the_way_at'])) $progress = 75;
                            elseif (!empty($delivery['picked_up_at'])) $progress = 50;
                            elseif (!empty($delivery['accepted_at']) || !empty($delivery['assigned_at'])) $progress = 25;
                            echo $progress;
                        ?>%;"></div>
                    </div>

                    <?php
                    $frMonthsTrk = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                    function fmtTrackTime($ts, $fr, $frMonths) {
                        if (!$ts) return '';
                        $t = strtotime($ts);
                        return $fr
                            ? ((int)date('j', $t) . ' ' . $frMonths[(int)date('n', $t)] . ' à ' . date('G', $t) . 'h' . date('i', $t))
                            : date('M j, g:i A', $t);
                    }
                    ?>

                    <!-- Step 1: Order Placed -->
                    <div class="progress-step">
                        <div class="step-icon completed">✓</div>
                        <div class="step-label"><?php echo $fr ? 'Commande passée' : 'Order Placed'; ?></div>
                        <div class="step-time"><?= fmtTrackTime($delivery['assigned_at'] ?? '', $fr, $frMonthsTrk) ?></div>
                    </div>

                    <!-- Step 2: Driver Assigned -->
                    <div class="progress-step">
                        <div class="step-icon <?= !empty($delivery['assigned_at']) || !empty($delivery['accepted_at']) ? 'completed' : '' ?> <?= empty($delivery['picked_up_at']) && (!empty($delivery['assigned_at']) || !empty($delivery['accepted_at'])) ? 'active' : '' ?>">
                            <?= !empty($delivery['assigned_at']) || !empty($delivery['accepted_at']) ? '✓' : '2' ?>
                        </div>
                        <div class="step-label <?= empty($delivery['picked_up_at']) && (!empty($delivery['assigned_at']) || !empty($delivery['accepted_at'])) ? 'active' : '' ?>"><?php echo $fr ? 'Livreur assigné' : 'Driver Assigned'; ?></div>
                        <div class="step-time"><?= fmtTrackTime($delivery['accepted_at'] ?? '', $fr, $frMonthsTrk) ?></div>
                    </div>

                    <!-- Step 3: Picked Up -->
                    <div class="progress-step">
                        <div class="step-icon <?= !empty($delivery['picked_up_at']) ? 'completed' : '' ?> <?= !empty($delivery['picked_up_at']) && empty($delivery['on_the_way_at']) ? 'active' : '' ?>">
                            <?= !empty($delivery['picked_up_at']) ? '✓' : '3' ?>
                        </div>
                        <div class="step-label <?= !empty($delivery['picked_up_at']) && empty($delivery['on_the_way_at']) ? 'active' : '' ?>"><?php echo $fr ? 'Ramassé' : 'Picked Up'; ?></div>
                        <div class="step-time"><?= fmtTrackTime($delivery['picked_up_at'] ?? '', $fr, $frMonthsTrk) ?></div>
                    </div>

                    <!-- Step 4: On the Way -->
                    <div class="progress-step">
                        <div class="step-icon <?= !empty($delivery['on_the_way_at']) ? 'completed' : '' ?> <?= !empty($delivery['on_the_way_at']) && empty($delivery['delivered_at']) ? 'active' : '' ?>">
                            <?= !empty($delivery['on_the_way_at']) ? '✓' : '4' ?>
                        </div>
                        <div class="step-label <?= !empty($delivery['on_the_way_at']) && empty($delivery['delivered_at']) ? 'active' : '' ?>"><?php echo $fr ? 'En route' : 'On the Way'; ?></div>
                        <div class="step-time"><?= fmtTrackTime($delivery['on_the_way_at'] ?? '', $fr, $frMonthsTrk) ?></div>
                    </div>

                    <!-- Step 5: Delivered -->
                    <div class="progress-step">
                        <div class="step-icon <?= !empty($delivery['delivered_at']) ? 'completed' : '' ?> <?= !empty($delivery['delivered_at']) ? 'active' : '' ?>">
                            <?= !empty($delivery['delivered_at']) ? '✓' : '5' ?>
                        </div>
                        <div class="step-label <?= !empty($delivery['delivered_at']) ? 'active' : '' ?>"><?php echo $fr ? 'Livré' : 'Delivered'; ?></div>
                        <div class="step-time"><?= fmtTrackTime($delivery['delivered_at'] ?? '', $fr, $frMonthsTrk) ?></div>
                    </div>
                </div>
            </div>

            <!-- Current Status Card -->
            <div class="status-card <?php
                if (!empty($delivery['delivered_at'])) echo 'delivered';
                elseif ($delivery['status'] === 'failed' || $delivery['status'] === 'cancelled') echo 'failed';
            ?>">
                <div class="status-icon">
                    <?php
                    if (!empty($delivery['delivered_at'])) echo '✅';
                    elseif (!empty($delivery['on_the_way_at'])) echo '🚗';
                    elseif (!empty($delivery['picked_up_at'])) echo '📦';
                    elseif (!empty($delivery['accepted_at']) || !empty($delivery['assigned_at'])) echo '👤';
                    elseif ($delivery['status'] === 'failed' || $delivery['status'] === 'cancelled') echo '❌';
                    else echo '⏳';
                    ?>
                </div>

                <?php
                $tsDelivered = !empty($delivery['delivered_at']) ? strtotime($delivery['delivered_at']) : null;
                if ($tsDelivered && $fr) {
                    $deliveredDateStr = (int)date('j', $tsDelivered) . ' ' . $frMonthsTrk[(int)date('n', $tsDelivered)] . ' ' . date('Y', $tsDelivered) . ' à ' . date('G', $tsDelivered) . 'h' . date('i', $tsDelivered);
                } elseif ($tsDelivered) {
                    $deliveredDateStr = date('l, F j, Y \a\t g:i A', $tsDelivered);
                }
                ?>

                <?php if (!empty($delivery['delivered_at'])): ?>
                    <h2 class="status-title"><?php echo $fr ? 'Livré avec succès !' : 'Delivered Successfully!'; ?></h2>
                    <p class="status-message">
                        <?php echo $fr ? 'Votre commande a été livrée le ' . $deliveredDateStr . '.' : 'Your order was delivered on ' . $deliveredDateStr . '.'; ?>
                        <?php if (!empty($delivery['actual_time'])): ?>
                            <br><?php echo $fr ? 'Durée de livraison : ' . htmlspecialchars($delivery['actual_time']) . ' min' : 'Delivery time: ' . htmlspecialchars($delivery['actual_time']) . ' minutes'; ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($delivery['proof_of_delivery'])): ?>
                        <div class="proof-image">
                            <img src="<?= htmlspecialchars($delivery['proof_of_delivery']) ?>" alt="<?php echo $fr ? 'Preuve de livraison' : 'Proof of Delivery'; ?>">
                        </div>
                    <?php endif; ?>

                <?php elseif (!empty($delivery['on_the_way_at'])): ?>
                    <h2 class="status-title"><?php echo $fr ? 'Votre commande est en route !' : 'Your Order is On the Way!'; ?></h2>
                    <p class="status-message">
                        <?php echo $fr ? 'Votre livraison est en transit et devrait arriver bientôt.' : 'Your delivery is currently in transit and should arrive soon.'; ?>
                        <?php if (!empty($delivery['estimated_time'])): ?>
                            <br><?php echo $fr ? 'Arrivée estimée : ' . htmlspecialchars($delivery['estimated_time']) . ' min' : 'Estimated arrival: ' . htmlspecialchars($delivery['estimated_time']) . ' minutes'; ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($delivery['driver_first_name'])): ?>
                        <div class="driver-info">
                            <div class="driver-name">
                                <?php echo $fr ? 'Livreur :' : 'Driver:'; ?> <?= htmlspecialchars($delivery['driver_first_name']) ?> <?= htmlspecialchars($delivery['driver_last_name'] ?? '') ?>
                            </div>
                            <?php if (!empty($delivery['driver_phone'])): ?>
                                <a href="tel:<?= htmlspecialchars($delivery['driver_phone']) ?>" class="driver-phone">
                                    📞 <?= htmlspecialchars($delivery['driver_phone']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php elseif (!empty($delivery['picked_up_at'])): ?>
                    <h2 class="status-title"><?php echo $fr ? 'Le livreur a ramassé votre commande' : 'Driver Has Picked Up Your Order'; ?></h2>
                    <p class="status-message">
                        <?php
                        $shopName = htmlspecialchars($delivery['shop_name'] ?? ($fr ? 'la boutique' : 'the shop'));
                        echo $fr
                            ? 'Votre commande a été ramassée chez ' . $shopName . ' et sera en route bientôt.'
                            : 'Your order has been picked up from ' . $shopName . ' and will be on its way soon.';
                        ?>
                    </p>
                    <?php if (!empty($delivery['driver_first_name'])): ?>
                        <div class="driver-info">
                            <div class="driver-name">
                                <?php echo $fr ? 'Livreur :' : 'Driver:'; ?> <?= htmlspecialchars($delivery['driver_first_name']) ?> <?= htmlspecialchars($delivery['driver_last_name'] ?? '') ?>
                            </div>
                            <?php if (!empty($delivery['driver_phone'])): ?>
                                <a href="tel:<?= htmlspecialchars($delivery['driver_phone']) ?>" class="driver-phone">
                                    📞 <?= htmlspecialchars($delivery['driver_phone']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php elseif (!empty($delivery['accepted_at']) || !empty($delivery['assigned_at'])): ?>
                    <h2 class="status-title"><?php echo $fr ? 'Livreur assigné' : 'Driver Assigned'; ?></h2>
                    <p class="status-message">
                        <?php echo $fr ? 'Un livreur a été assigné à votre commande et viendra la ramasser bientôt.' : 'A driver has been assigned to your delivery and will pick up your order soon.'; ?>
                    </p>
                    <?php if (!empty($delivery['driver_first_name'])): ?>
                        <div class="driver-info">
                            <div class="driver-name">
                                <?php echo $fr ? 'Livreur :' : 'Driver:'; ?> <?= htmlspecialchars($delivery['driver_first_name']) ?> <?= htmlspecialchars($delivery['driver_last_name'] ?? '') ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php elseif ($delivery['status'] === 'failed' || $delivery['status'] === 'cancelled'): ?>
                    <h2 class="status-title"><?php echo $fr ? 'La livraison n\'a pas pu être effectuée' : 'Delivery Could Not Be Completed'; ?></h2>
                    <p class="status-message">
                        <?php
                        if ($fr) {
                            echo $delivery['status'] === 'cancelled'
                                ? 'Cette livraison a été annulée. Veuillez contacter le service client pour obtenir de l\'aide.'
                                : 'Cette livraison n\'a malheureusement pas pu être effectuée. Veuillez contacter le service client pour obtenir de l\'aide.';
                        } else {
                            echo 'Unfortunately, this delivery was ' . ($delivery['status'] === 'cancelled' ? 'cancelled' : 'unable to be completed') . '. Please contact customer support for assistance.';
                        }
                        ?>
                    </p>

                <?php else: ?>
                    <h2 class="status-title"><?php echo $fr ? 'En attente d\'un livreur' : 'Waiting for Driver Assignment'; ?></h2>
                    <p class="status-message">
                        <?php echo $fr ? 'Votre commande est en cours de préparation et nous trouvons le meilleur livreur pour vous.' : 'Your order is being prepared and we\'re finding the best driver for your delivery.'; ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Live Map (when driver is active) -->
            <?php if (in_array($delivery['status'] ?? '', ['accepted', 'picked_up', 'on_the_way'])): ?>
            <div class="live-map-card">
                <h3 class="card-title">🗺️ <?php echo $fr ? 'Suivi en direct' : 'Live Tracking'; ?></h3>
                <div id="trackingMap"></div>
                <div class="map-last-update"><?php echo $fr ? 'Dernière mise à jour :' : 'Last updated:'; ?> <span id="mapLastUpdate"><?php echo $fr ? 'Chargement...' : 'Loading...'; ?></span></div>
                <div class="map-legend">
                    <div class="legend-item"><div class="legend-dot" style="background:#3b82f6;"></div> <?php echo $fr ? 'Livreur' : 'Driver'; ?></div>
                    <div class="legend-item"><div class="legend-dot" style="background:#00b207;"></div> <?php echo $fr ? 'Ramassage' : 'Pickup'; ?></div>
                    <div class="legend-item"><div class="legend-dot" style="background:#ef4444;"></div> <?php echo $fr ? 'Livraison' : 'Delivery'; ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Delivery Details -->
            <div class="details-card">
                <h3 class="card-title">📍 <?php echo $fr ? 'Détails de la livraison' : 'Delivery Details'; ?></h3>

                <?php if (!empty($delivery['shop_name'])): ?>
                <div class="detail-row">
                    <div class="detail-label"><?php echo $fr ? 'Ramassage chez :' : 'Pickup from:'; ?></div>
                    <div class="detail-value"><?= htmlspecialchars($delivery['shop_name']) ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($delivery['delivery_address'])): ?>
                <div class="detail-row">
                    <div class="detail-label"><?php echo $fr ? 'Livraison à :' : 'Deliver to:'; ?></div>
                    <div class="detail-value"><?= htmlspecialchars($delivery['delivery_address']) ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($delivery['customer_first_name'])): ?>
                <div class="detail-row">
                    <div class="detail-label"><?php echo $fr ? 'Client :' : 'Customer:'; ?></div>
                    <div class="detail-value">
                        <?= htmlspecialchars($delivery['customer_first_name']) ?>
                        <?= htmlspecialchars($delivery['customer_last_name'] ?? '') ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($delivery['delivery_fee'])): ?>
                <div class="detail-row">
                    <div class="detail-label"><?php echo $fr ? 'Frais de livraison :' : 'Delivery Fee:'; ?></div>
                    <div class="detail-value">$<?= number_format($delivery['delivery_fee'], 2) ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($delivery['estimated_time']) && empty($delivery['delivered_at'])): ?>
                <div class="detail-row">
                    <div class="detail-label"><?php echo $fr ? 'Temps estimé :' : 'Estimated Time:'; ?></div>
                    <div class="detail-value"><?= htmlspecialchars($delivery['estimated_time']) ?> min</div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Status History Timeline -->
            <?php if (!empty($history) && is_array($history)): ?>
            <div class="details-card">
                <h3 class="card-title">📋 <?php echo $fr ? 'Historique des statuts' : 'Status History'; ?></h3>
                <?php
                $histStatusLabelsFrTrk = ['assigned'=>'Assignée','accepted'=>'Acceptée','picked_up'=>'Ramassée','on_the_way'=>'En route','delivered'=>'Livrée','failed'=>'Échouée','cancelled'=>'Annulée'];
                $histStatusLabelsEnTrk = ['assigned'=>'Assigned','accepted'=>'Accepted','picked_up'=>'Picked Up','on_the_way'=>'On the Way','delivered'=>'Delivered','failed'=>'Failed','cancelled'=>'Cancelled'];
                ?>
                <div class="timeline">
                    <?php foreach ($history as $item): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <?php
                            $s = $item['status'] ?? '';
                            $histLabel = $fr
                                ? ($histStatusLabelsFrTrk[$s] ?? ucfirst($s))
                                : ($histStatusLabelsEnTrk[$s] ?? ucfirst(str_replace('_',' ',$s)));
                            ?>
                            <div class="timeline-status"><?= htmlspecialchars($histLabel) ?></div>
                            <div class="timeline-time">
                                <?php
                                if (!empty($item['created_at'])) {
                                    $th = strtotime($item['created_at']);
                                    echo $fr
                                        ? ((int)date('j', $th) . ' ' . $frMonthsTrk[(int)date('n', $th)] . ' ' . date('Y', $th) . ' à ' . date('G', $th) . 'h' . date('i', $th))
                                        : date('F j, Y \a\t g:i A', $th);
                                }
                                ?>
                            </div>
                            <?php if (!empty($item['notes'])): ?>
                            <div class="timeline-notes"><?= htmlspecialchars($item['notes']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Track Another Order -->
            <div class="track-another">
                <a href="<?= url('track') ?>">← <?php echo $fr ? 'Suivre une autre commande' : 'Track Another Order'; ?></a>
            </div>

        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <strong>OCSAPP</strong> &copy; <?= date('Y') ?>. <?php echo $fr ? 'Livraison d\'épicerie zéro émission.' : 'Zero-Emission Grocery Delivery.'; ?>
        </div>
    </div>

    <?php if (!empty($delivery) && in_array($delivery['status'] ?? '', ['accepted', 'picked_up', 'on_the_way'])): ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    (function() {
        const deliveryId = <?= (int)$delivery['id'] ?>;
        const map = L.map('trackingMap').setView([45.5017, -73.5673], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        // Custom marker icons using circle markers for simplicity
        let driverMarker = null;
        let pickupMarker = null;
        let dropoffMarker = null;
        let routeLine = null;
        let hasFittedBounds = false;

        function createIcon(color) {
            return L.divIcon({
                html: '<div style="background:' + color + ';width:16px;height:16px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
                className: '',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });
        }

        // Pulsing driver icon
        const driverIcon = L.divIcon({
            html: '<div style="position:relative;"><div style="background:#3b82f6;width:20px;height:20px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(59,130,246,0.5);position:relative;z-index:2;"></div><div style="position:absolute;top:-4px;left:-4px;width:28px;height:28px;border-radius:50%;background:rgba(59,130,246,0.3);animation:pulse 2s infinite;"></div></div>',
            className: '',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        function updateTracking() {
            fetch('/api/delivery/location?delivery_id=' + deliveryId)
                .then(r => r.json())
                .then(data => {
                    const bounds = [];

                    // Driver marker
                    if (data.driver && data.driver.lat) {
                        const pos = [data.driver.lat, data.driver.lng];
                        if (!driverMarker) {
                            driverMarker = L.marker(pos, { icon: driverIcon, zIndexOffset: 1000 })
                                .addTo(map)
                                .bindPopup('<strong>' + (data.driver.name || '<?php echo $fr ? 'Livreur' : 'Driver'; ?>') + '</strong><br><?php echo $fr ? 'Votre livreur' : 'Your delivery driver'; ?>');
                        } else {
                            driverMarker.setLatLng(pos);
                        }
                        bounds.push(pos);

                        if (data.driver.lastUpdate) {
                            const d = new Date(data.driver.lastUpdate);
                            document.getElementById('mapLastUpdate').textContent = d.toLocaleTimeString();
                        }
                    }

                    // Pickup marker
                    if (data.pickup && data.pickup.lat) {
                        const pos = [data.pickup.lat, data.pickup.lng];
                        if (!pickupMarker) {
                            pickupMarker = L.marker(pos, { icon: createIcon('#00b207') })
                                .addTo(map)
                                .bindPopup('<strong>' + (data.pickup.name || '<?php echo $fr ? 'Ramassage' : 'Pickup'; ?>') + '</strong><br>' + (data.pickup.address || ''));
                        }
                        bounds.push(pos);
                    }

                    // Dropoff marker (if we have geocoded coords in the future)
                    if (data.dropoff && data.dropoff.lat) {
                        const pos = [data.dropoff.lat, data.dropoff.lng];
                        if (!dropoffMarker) {
                            dropoffMarker = L.marker(pos, { icon: createIcon('#ef4444') })
                                .addTo(map)
                                .bindPopup('<strong><?php echo $fr ? 'Adresse de livraison' : 'Delivery Address'; ?></strong><br>' + (data.dropoff.address || ''));
                        }
                        bounds.push(pos);
                    }

                    // Draw route line
                    if (bounds.length >= 2) {
                        if (routeLine) map.removeLayer(routeLine);
                        routeLine = L.polyline(bounds, {
                            color: '#3b82f6',
                            weight: 3,
                            opacity: 0.6,
                            dashArray: '8, 8'
                        }).addTo(map);
                    }

                    // Fit bounds on first load only
                    if (bounds.length >= 2 && !hasFittedBounds) {
                        map.fitBounds(bounds, { padding: [50, 50] });
                        hasFittedBounds = true;
                    } else if (bounds.length === 1 && !hasFittedBounds) {
                        map.setView(bounds[0], 14);
                        hasFittedBounds = true;
                    }
                })
                .catch(err => {
                    console.error('Tracking update error:', err);
                    document.getElementById('mapLastUpdate').textContent = '<?php echo $fr ? 'Impossible de charger - nouvelle tentative...' : 'Unable to load - retrying...'; ?>';
                });
        }

        // Initial load + auto-refresh
        updateTracking();
        setInterval(updateTracking, 15000);
    })();
    </script>
    <style>
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.5); opacity: 0; }
        }
    </style>
    <?php endif; ?>
</body>
</html>
