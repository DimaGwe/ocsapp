<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);

$od = [
    'en' => [
        'nav_dashboard'  => 'Dashboard',
        'nav_orders'     => 'My Orders',
        'nav_addresses'  => 'Addresses',
        'nav_wishlist'   => 'Wishlist',
        'nav_settings'   => 'Settings',
        'back'           => 'Back to Orders',
        'order_prefix'   => 'Order #',
        'placed_on'      => 'Placed on',
        'box_shop'       => 'Shop',
        'box_address'    => 'Delivery Address',
        'box_driver'     => 'Delivery Driver',
        'tracking'       => 'Tracking:',
        'not_available'  => 'Not available',
        'btn_cancel'     => 'Cancel Order',
        'items_title'    => 'Items Ordered',
        'th_product'     => 'Product',
        'th_qty'         => 'Qty',
        'th_unit'        => 'Unit Price',
        'th_subtotal'    => 'Subtotal',
        'lbl_subtotal'   => 'Subtotal',
        'lbl_gst'        => 'GST (5%)',
        'lbl_qst'        => 'QST (9.975%)',
        'lbl_tax'        => 'Tax',
        'lbl_delivery'   => 'Delivery Fee',
        'lbl_total'      => 'Total',
        'rate_title'     => 'Rate Your Delivery Driver',
        'delivered_by'   => 'Delivered by',
        'already_rated'  => 'You already rated this delivery. Thank you!',
        'rate_labels'    => ['', 'Terrible', 'Poor', 'Okay', 'Good', 'Excellent'],
        'comment_ph'     => 'Leave a comment (optional)…',
        'btn_submit'     => 'Submit Rating',
        'activity_title' => 'Order Activity',
        'cancel_confirm' => 'Cancel this order?',
    ],
    'fr' => [
        'nav_dashboard'  => 'Tableau de bord',
        'nav_orders'     => 'Mes commandes',
        'nav_addresses'  => 'Adresses',
        'nav_wishlist'   => 'Liste de souhaits',
        'nav_settings'   => 'Paramètres',
        'back'           => 'Retour aux commandes',
        'order_prefix'   => 'Commande #',
        'placed_on'      => 'Passée le',
        'box_shop'       => 'Boutique',
        'box_address'    => 'Adresse de livraison',
        'box_driver'     => 'Livreur',
        'tracking'       => 'Suivi :',
        'not_available'  => 'Non disponible',
        'btn_cancel'     => 'Annuler la commande',
        'items_title'    => 'Articles commandés',
        'th_product'     => 'Produit',
        'th_qty'         => 'Qté',
        'th_unit'        => 'Prix unitaire',
        'th_subtotal'    => 'Sous-total',
        'lbl_subtotal'   => 'Sous-total',
        'lbl_gst'        => 'TPS (5 %)',
        'lbl_qst'        => 'TVQ (9,975 %)',
        'lbl_tax'        => 'Taxe',
        'lbl_delivery'   => 'Frais de livraison',
        'lbl_total'      => 'Total',
        'rate_title'     => 'Évaluez votre livreur',
        'delivered_by'   => 'Livré par',
        'already_rated'  => 'Vous avez déjà évalué cette livraison. Merci !',
        'rate_labels'    => ['', 'Très mauvais', 'Mauvais', 'Correct', 'Bien', 'Excellent'],
        'comment_ph'     => 'Laissez un commentaire (optionnel)…',
        'btn_submit'     => "Soumettre l'évaluation",
        'activity_title' => 'Activité de la commande',
        'cancel_confirm' => 'Annuler cette commande ?',
    ],
];
$od = $od[$currentLang] ?? $od['en'];
$order = $order ?? [];
$items = $items ?? [];
$statusHistory = $statusHistory ?? [];
$delivery = $delivery ?? null;
$ratingDriverId = $ratingDriverId ?? null;
$driverName = $driverName ?? null;
$driverRating = $driverRating ?? null;
$cartCount = $cartCount ?? 0;
$user = user();

$statusColors = [
    'pending'    => ['bg'=>'#fff3e0','color'=>'#e65100'],
    'processing' => ['bg'=>'#e3f2fd','color'=>'#1565c0'],
    'paid'       => ['bg'=>'#e8f5e9','color'=>'#2e7d32'],
    'completed'  => ['bg'=>'#e8f5e9','color'=>'#2e7d32'],
    'delivered'  => ['bg'=>'#e8f5e9','color'=>'#2e7d32'],
    'cancelled'  => ['bg'=>'#fce4ec','color'=>'#c62828'],
];
$sc = $statusColors[$order['status'] ?? ''] ?? ['bg'=>'#f5f5f5','color'=>'#555'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= htmlspecialchars($order['order_number'] ?? $order['id'] ?? '') ?> - OCS Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; color: #333; }
        .account-layout { display: flex; max-width: 1200px; margin: 40px auto; gap: 24px; padding: 0 16px; }
        .account-sidebar { width: 240px; flex-shrink: 0; }
        .account-main { flex: 1; min-width: 0; }
        .sidebar-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .sidebar-user { text-align: center; padding-bottom: 16px; border-bottom: 1px solid #f0f0f0; margin-bottom: 12px; }
        .sidebar-avatar { width: 64px; height: 64px; border-radius: 50%; background: #00b207; color: #fff; font-size: 24px; font-weight: 600; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
        .sidebar-name { font-weight: 600; font-size: 15px; }
        .sidebar-email { font-size: 12px; color: #888; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #555; font-size: 14px; font-weight: 500; transition: all .2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #e8f5e9; color: #00b207; }
        .sidebar-nav a i { width: 18px; text-align: center; }
        .section-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.06); margin-bottom: 16px; }
        .order-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
        .order-title { font-size: 20px; font-weight: 700; }
        .order-date-label { font-size: 13px; color: #888; margin-top: 2px; }
        .status-badge { padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { text-align: left; padding: 10px 12px; font-size: 12px; color: #888; font-weight: 600; border-bottom: 1px solid #f0f0f0; }
        .items-table td { padding: 12px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; font-size: 14px; }
        .items-table tr:last-child td { border-bottom: none; }
        .item-img { width: 56px; height: 56px; border-radius: 8px; object-fit: cover; background: #f5f5f5; }
        .item-name { font-weight: 500; }
        .item-sku { font-size: 12px; color: #aaa; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; color: #555; }
        .totals-row.grand { font-size: 16px; font-weight: 700; color: #333; border-top: 1px solid #f0f0f0; margin-top: 8px; padding-top: 12px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .info-box { background: #f9f9f9; border-radius: 8px; padding: 16px; }
        .info-box h4 { font-size: 13px; color: #888; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: .5px; }
        .info-box p { font-size: 14px; margin: 2px 0; }
        .history-item { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f5f5f5; }
        .history-item:last-child { border-bottom: none; }
        .history-dot { width: 10px; height: 10px; border-radius: 50%; background: #00b207; margin-top: 5px; flex-shrink: 0; }
        .history-time { font-size: 12px; color: #aaa; }
        .btn-back { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; background: #f5f5f5; color: #333; text-decoration: none; font-size: 14px; font-weight: 500; margin-bottom: 20px; transition: all .2s; }
        .btn-back:hover { background: #e8f5e9; color: #00b207; }
        .cancel-form { margin-top: 16px; }
        .btn-cancel { padding: 8px 20px; border-radius: 8px; background: #fce4ec; color: #c62828; border: none; font-size: 14px; font-weight: 500; cursor: pointer; transition: all .2s; }
        .btn-cancel:hover { background: #c62828; color: #fff; }
        @media (max-width: 768px) { .account-layout { flex-direction: column; } .account-sidebar { width: 100%; } .info-grid { grid-template-columns: 1fr; } .items-table { font-size: 12px; } }
        /* Driver rating */
        .rating-card { background:#fff; border-radius:12px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:16px; }
        .star-row { display:flex; gap:6px; margin:12px 0; }
        .star-btn { background:none; border:none; cursor:pointer; font-size:32px; color:#d1d5db; transition:color .15s; padding:0; line-height:1; }
        .star-btn.active, .star-btn:hover, .star-btn.hover { color:#f59e0b; }
        .star-display { font-size:28px; color:#f59e0b; }
        .rating-comment { width:100%; border:1px solid #e5e7eb; border-radius:8px; padding:10px 14px; font-family:inherit; font-size:14px; resize:vertical; min-height:72px; margin:8px 0 14px; }
        .btn-rate { background:#00b207; color:#fff; border:none; border-radius:8px; padding:10px 24px; font-size:14px; font-weight:600; cursor:pointer; font-family:inherit; transition:background .2s; }
        .btn-rate:hover { background:#009906; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../components/header.php'; ?>

<div class="account-layout">
    <aside class="account-sidebar">
        <div class="sidebar-card">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?></div>
                <div class="sidebar-name"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
                <div class="sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= url('account') ?>"><i class="fas fa-home"></i> <?= $od['nav_dashboard'] ?></a>
                <a href="<?= url('account/orders') ?>" class="active"><i class="fas fa-box"></i> <?= $od['nav_orders'] ?></a>
                <a href="<?= url('account/addresses') ?>"><i class="fas fa-map-marker-alt"></i> <?= $od['nav_addresses'] ?></a>
                <a href="<?= url('account/wishlist') ?>"><i class="fas fa-heart"></i> <?= $od['nav_wishlist'] ?></a>
                <a href="<?= url('account/settings') ?>"><i class="fas fa-cog"></i> <?= $od['nav_settings'] ?></a>
            </nav>
        </div>
    </aside>

    <main class="account-main">
        <?php if ($flash = getFlash('error')): ?>
            <div style="background:#fce4ec;color:#c62828;padding:12px 16px;border-radius:8px;margin-bottom:16px;"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = getFlash('success')): ?>
            <div style="background:#e8f5e9;color:#2e7d32;padding:12px 16px;border-radius:8px;margin-bottom:16px;"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <a href="<?= url('account/orders') ?>" class="btn-back"><i class="fas fa-arrow-left"></i> <?= $od['back'] ?></a>

        <!-- Order Header -->
        <div class="section-card">
            <div class="order-header">
                <div>
                    <div class="order-title"><?= $od['order_prefix'] ?><?= htmlspecialchars($order['order_number'] ?? $order['id'] ?? '') ?></div>
                    <div class="order-date-label"><?= $od['placed_on'] ?> <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'] ?? 'now')) ?></div>
                </div>
                <span class="status-badge" style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;">
                    <?= ucfirst($order['status'] ?? 'unknown') ?>
                </span>
            </div>

            <!-- Shop info -->
            <div class="info-grid" style="margin-bottom:20px;">
                <div class="info-box">
                    <h4><i class="fas fa-store"></i> <?= $od['box_shop'] ?></h4>
                    <p style="font-weight:600;"><?= htmlspecialchars($order['shop_name'] ?? 'N/A') ?></p>
                    <?php if (!empty($order['shop_phone'])): ?>
                        <p><a href="tel:<?= htmlspecialchars($order['shop_phone']) ?>" style="color:#00b207;"><?= htmlspecialchars($order['shop_phone']) ?></a></p>
                    <?php endif; ?>
                </div>
                <div class="info-box">
                    <h4><i class="fas fa-map-marker-alt"></i> <?= $od['box_address'] ?></h4>
                    <?php
                        $addr = json_decode($order['delivery_address'] ?? '{}', true);
                        if (is_array($addr) && !empty($addr)):
                    ?>
                        <p><?= htmlspecialchars($addr['name'] ?? '') ?></p>
                        <p><?= htmlspecialchars($addr['address_line_1'] ?? '') ?></p>
                        <?php if (!empty($addr['address_line_2'])): ?><p><?= htmlspecialchars($addr['address_line_2']) ?></p><?php endif; ?>
                        <p><?= htmlspecialchars(($addr['city'] ?? '') . ', ' . ($addr['state'] ?? '') . ' ' . ($addr['postal_code'] ?? '')) ?></p>
                    <?php else: ?>
                        <p style="color:#aaa;"><?= $od['not_available'] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($delivery)): ?>
                <div class="info-box" style="margin-bottom:16px;">
                    <h4><i class="fas fa-truck"></i> <?= $od['box_driver'] ?></h4>
                    <p><?= htmlspecialchars(($delivery['driver_first_name'] ?? '') . ' ' . ($delivery['driver_last_name'] ?? '')) ?></p>
                    <?php if (!empty($delivery['driver_phone'])): ?>
                        <p><a href="tel:<?= htmlspecialchars($delivery['driver_phone']) ?>" style="color:#00b207;"><?= htmlspecialchars($delivery['driver_phone']) ?></a></p>
                    <?php endif; ?>
                    <?php if (!empty($delivery['tracking_code'])): ?>
                        <p style="font-size:12px;color:#888;"><?= $od['tracking'] ?> <strong><?= htmlspecialchars($delivery['tracking_code']) ?></strong></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Cancel button -->
            <?php if (in_array($order['status'] ?? '', ['pending', 'processing'])): ?>
                <div class="cancel-form">
                    <form method="POST" action="<?= url('account/orders/cancel') ?>" onsubmit="return confirm(<?= htmlspecialchars(json_encode($od['cancel_confirm'])) ?>);">
                        <?= csrfField() ?>
                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                        <button type="submit" class="btn-cancel"><i class="fas fa-times"></i> <?= $od['btn_cancel'] ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Order Items -->
        <div class="section-card">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;"><?= $od['items_title'] ?></h3>
            <div style="overflow-x:auto;">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width:56px;"></th>
                            <th><?= $od['th_product'] ?></th>
                            <th><?= $od['th_qty'] ?></th>
                            <th><?= $od['th_unit'] ?></th>
                            <th><?= $od['th_subtotal'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img src="<?= asset('uploads/' . htmlspecialchars($item['image_path'])) ?>" alt="" class="item-img">
                                    <?php else: ?>
                                        <div class="item-img" style="display:flex;align-items:center;justify-content:center;"><i class="fas fa-image" style="color:#ddd;font-size:24px;"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="item-name"><?= htmlspecialchars($item['product_name'] ?? $item['name'] ?? 'Product') ?></div>
                                    <?php if (!empty($item['variant_name'])): ?><div class="item-sku"><?= htmlspecialchars($item['variant_name']) ?></div><?php endif; ?>
                                </td>
                                <td><?= (int)$item['quantity'] ?></td>
                                <td>$<?= number_format((float)$item['price'], 2) ?></td>
                                <td style="font-weight:600;">$<?= number_format((float)$item['price'] * (int)$item['quantity'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div style="max-width:320px;margin-left:auto;margin-top:20px;">
                <div class="totals-row"><span><?= $od['lbl_subtotal'] ?></span><span>$<?= number_format((float)($order['subtotal'] ?? $order['total']), 2) ?></span></div>
                <?php if (!empty($order['gst'])): ?>
                    <div class="totals-row"><span><?= $od['lbl_gst'] ?></span><span>$<?= number_format((float)$order['gst'], 2) ?></span></div>
                <?php endif; ?>
                <?php if (!empty($order['qst'])): ?>
                    <div class="totals-row"><span><?= $od['lbl_qst'] ?></span><span>$<?= number_format((float)$order['qst'], 2) ?></span></div>
                <?php elseif (!empty($order['tax'])): ?>
                    <div class="totals-row"><span><?= $od['lbl_tax'] ?></span><span>$<?= number_format((float)$order['tax'], 2) ?></span></div>
                <?php endif; ?>
                <?php if (!empty($order['delivery_fee'])): ?>
                    <div class="totals-row"><span><?= $od['lbl_delivery'] ?></span><span>$<?= number_format((float)$order['delivery_fee'], 2) ?></span></div>
                <?php endif; ?>
                <div class="totals-row grand"><span><?= $od['lbl_total'] ?></span><span>$<?= number_format((float)$order['total'], 2) ?></span></div>
            </div>
        </div>

        <!-- Driver Rating Card -->
        <?php if ($order['status'] === 'delivered' && $ratingDriverId): ?>
        <div class="rating-card">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:4px;">
                <i class="fas fa-star" style="color:#f59e0b;"></i>
                <?= $od['rate_title'] ?>
            </h3>
            <?php if ($driverName): ?>
                <p style="font-size:13px;color:#888;margin-bottom:8px;"><?= $od['delivered_by'] ?> <strong><?= htmlspecialchars($driverName) ?></strong></p>
            <?php endif; ?>

            <?php if ($driverRating): ?>
                <!-- Already rated — read only -->
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="star-display">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= $driverRating['rating'] ? '★' : '☆' ?>
                        <?php endfor; ?>
                    </span>
                    <span style="font-size:14px;color:#555;"><?= (int)$driverRating['rating'] ?>/5</span>
                </div>
                <?php if (!empty($driverRating['comment'])): ?>
                    <p style="font-size:13px;color:#666;margin-top:8px;font-style:italic;">"<?= htmlspecialchars($driverRating['comment']) ?>"</p>
                <?php endif; ?>
                <p style="font-size:12px;color:#aaa;margin-top:8px;"><?= $od['already_rated'] ?></p>
            <?php else: ?>
                <!-- Rating form -->
                <form method="POST" action="<?= url('account/orders/rate-driver') ?>">
                    <?= csrfField() ?>
                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                    <input type="hidden" name="rating" id="ratingValue" value="0">
                    <div class="star-row" id="starRow">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button" class="star-btn" data-val="<?= $i ?>" onclick="setRating(<?= $i ?>)">★</button>
                        <?php endfor; ?>
                    </div>
                    <p id="ratingLabel" style="font-size:13px;color:#888;height:18px;margin-bottom:8px;"></p>
                    <textarea name="comment" class="rating-comment" placeholder="<?= htmlspecialchars($od['comment_ph']) ?>" maxlength="500"></textarea>
                    <button type="submit" class="btn-rate" id="submitRating" disabled><?= $od['btn_submit'] ?></button>
                </form>
                <script>
                const labels = <?= json_encode($od['rate_labels']) ?>;
                function setRating(val) {
                    document.getElementById('ratingValue').value = val;
                    document.getElementById('ratingLabel').textContent = labels[val];
                    document.getElementById('submitRating').disabled = false;
                    document.querySelectorAll('.star-btn').forEach((b,i) => {
                        b.classList.toggle('active', i < val);
                    });
                }
                document.querySelectorAll('.star-btn').forEach(b => {
                    b.addEventListener('mouseenter', () => {
                        const v = +b.dataset.val;
                        document.querySelectorAll('.star-btn').forEach((s,i) => s.classList.toggle('hover', i < v));
                    });
                    b.addEventListener('mouseleave', () => {
                        document.querySelectorAll('.star-btn').forEach(s => s.classList.remove('hover'));
                    });
                });
                </script>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Status History -->
        <?php if (!empty($statusHistory)): ?>
            <div class="section-card">
                <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;"><?= $od['activity_title'] ?></h3>
                <?php foreach ($statusHistory as $h): ?>
                    <div class="history-item">
                        <div class="history-dot"></div>
                        <div>
                            <div style="font-size:14px;font-weight:500;"><?= ucfirst(htmlspecialchars($h['status'] ?? '')) ?></div>
                            <?php if (!empty($h['notes'])): ?><div style="font-size:13px;color:#555;"><?= htmlspecialchars($h['notes']) ?></div><?php endif; ?>
                            <div class="history-time"><?= date('M j, Y g:i A', strtotime($h['created_at'])) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
