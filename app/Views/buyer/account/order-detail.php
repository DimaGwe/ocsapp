<?php
/**
 * Buyer order detail (/account/orders/detail?id=, OrderController::orderDetail)
 * Updated 2026-09-28: Marché Central theme via the shared account partials; fr-CA money/dates;
 * translated statuses. Fixes: activity list read `status` (NULL, checkout writes `new_status`);
 * pickup orders showed "Not available" for the address; claim label missing its accent. Adds the
 * payment status and the Founding Buyer delivery waiver line. Cancel, claim, rating and live-status
 * polling behave as before.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$order = $order ?? [];
$items = $items ?? [];
$statusHistory = $statusHistory ?? [];
$delivery = $delivery ?? null;
$ratingDriverId = $ratingDriverId ?? null;
$driverName = $driverName ?? null;
$driverRating = $driverRating ?? null;
$user = user() ?? [];
$accountActive = 'orders';

$orderNo  = (string) ($order['order_number'] ?? $order['id'] ?? '');
$status   = (string) ($order['status'] ?? '');
$payState = (string) ($order['payment_status'] ?? '');
require __DIR__ . '/partials/account-helpers.php';
$addr     = acct_plain_row(json_decode($order['delivery_address'] ?? '', true));
$isPickup = ($order['fulfillment_type'] ?? '') === 'pickup' || (is_array($addr) && ($addr['type'] ?? '') === 'pickup');
$payLabels = [
    'pending'  => ['Paiement en attente', 'Payment pending'],
    'paid'     => ['Payée', 'Paid'],
    'failed'   => ['Paiement refusé', 'Payment failed'],
    'refunded' => ['Remboursée', 'Refunded'],
];
$rateLabels = $fr ? ['', 'Très mauvais', 'Mauvais', 'Correct', 'Bien', 'Excellent'] : ['', 'Terrible', 'Poor', 'Okay', 'Good', 'Excellent'];

// Price breakdown rows: [label, amount]; zero rows are skipped
$m = fn($k) => (float) ($order[$k] ?? 0);
$rows = [
    [$fr ? 'Sous-total' : 'Subtotal', (float) ($order['subtotal'] ?? $order['total'] ?? 0)],
    [$fr ? 'Taxes (TPS + TVQ)' : 'Taxes (GST + QST)', $m('tax')],
    // A Founding Buyer waiver zeroes delivery_fee; show the waived fee here and the waiver below so the lines add up
    [$fr ? 'Frais de livraison' : 'Delivery fee', $m('delivery_fee') + $m('founding_buyer_delivery_waived')],
    [$fr ? 'Frais de multi-arrêt' : 'Additional-stop fee', $m('additional_stop_fee')],
    [$fr ? 'Surcharge surdimensionnement' : 'Oversize surcharge', $m('oversize_base_surcharge')],
    [($fr ? 'Surdimensionnement suppl.' : 'Oversize increment') . ' (' . (int) ($order['oversize_increment_count'] ?? 0) . ' x 10 kg)', $m('oversize_increment_surcharge')],
    [$fr ? 'Surcharge longue distance' : 'Long-distance surcharge', $m('long_distance_base_surcharge')],
    [($fr ? 'Longue distance suppl.' : 'Long-distance increment') . ' (' . (int) ($order['long_distance_increment_count'] ?? 0) . ' x 4 km)', $m('long_distance_increment_surcharge')],
];

$acctTitle   = ($fr ? 'Commande ' : 'Order ') . '#' . $orderNo;
$acctHeading = $acctTitle;
$acctCrumb   = [$fr ? 'Mes commandes' : 'My orders', 'fa-box'];
$acctSub     = ($fr ? 'Passée le ' : 'Placed on ') . acct_datetime($order['created_at'] ?? 'now', $fr);
$acctAction  = '<a href="' . url('account/orders') . '" class="acct-btn acct-btn-ghost"><i class="fas fa-arrow-left"></i> ' . ($fr ? 'Retour aux commandes' : 'Back to orders') . '</a>';
require __DIR__ . '/partials/account-top.php';
?>
                    <section class="acct-card acct-panel">
                        <div class="acct-od-status">
                            <span class="acct-badge acct-badge-lg acct-badge-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars(acct_status($status, $fr)) ?></span>
                            <?php if (isset($payLabels[$payState])): ?>
                                <span class="acct-badge acct-badge-lg acct-pay-<?= htmlspecialchars($payState) ?>"><i class="fas fa-credit-card"></i> <?= $payLabels[$payState][$fr ? 0 : 1] ?></span>
                            <?php endif; ?>
                            <?php if ($isPickup): ?>
                                <span class="acct-badge acct-badge-lg"><i class="fas fa-store"></i> <?= $fr ? 'Ramassage en boutique' : 'Store pickup' ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="acct-info-grid">
                            <div class="acct-info">
                                <h3><i class="fas fa-store"></i> <?= $fr ? 'Commerce' : 'Shop' ?></h3>
                                <p class="acct-strong"><?= htmlspecialchars($order['shop_name'] ?? ($fr ? 'Non disponible' : 'Not available')) ?></p>
                                <?php if (!empty($order['shop_phone'])): ?>
                                    <p><a href="tel:<?= htmlspecialchars($order['shop_phone']) ?>"><?= htmlspecialchars($order['shop_phone']) ?></a></p>
                                <?php endif; ?>
                            </div>
                            <div class="acct-info">
                                <?php if ($isPickup): ?>
                                    <h3><i class="fas fa-location-dot"></i> <?= $fr ? 'Adresse de ramassage' : 'Pickup address' ?></h3>
                                    <?php $pickupAt = $addr['shop_address'] ?? $order['shop_address'] ?? ''; ?>
                                    <p><?= $pickupAt !== '' ? htmlspecialchars($pickupAt) : '<span class="acct-dim">' . ($fr ? 'Non disponible' : 'Not available') . '</span>' ?></p>
                                <?php else: ?>
                                    <h3><i class="fas fa-location-dot"></i> <?= $fr ? 'Adresse de livraison' : 'Delivery address' ?></h3>
                                    <?php if (is_array($addr) && !empty($addr)): ?>
                                        <?php if (!empty($addr['name'])): ?><p class="acct-strong"><?= htmlspecialchars($addr['name']) ?></p><?php endif; ?>
                                        <p><?= htmlspecialchars($addr['address_line_1'] ?? $addr['street'] ?? '') ?></p>
                                        <?php if (!empty($addr['address_line_2'])): ?><p><?= htmlspecialchars($addr['address_line_2']) ?></p><?php endif; ?>
                                        <p><?= htmlspecialchars(trim(($addr['city'] ?? '') . ', ' . ($addr['state'] ?? $addr['province'] ?? '') . ' ' . ($addr['postal_code'] ?? ''), ', ')) ?></p>
                                    <?php else: ?>
                                        <p class="acct-dim"><?= $fr ? 'Non disponible' : 'Not available' ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($delivery)): ?>
                                <div class="acct-info">
                                    <h3><i class="fas fa-truck"></i> <?= $fr ? 'Livreur' : 'Delivery driver' ?></h3>
                                    <p class="acct-strong"><?= htmlspecialchars(trim(($delivery['driver_first_name'] ?? '') . ' ' . ($delivery['driver_last_name'] ?? ''))) ?></p>
                                    <?php if (!empty($delivery['driver_phone'])): ?>
                                        <p><a href="tel:<?= htmlspecialchars($delivery['driver_phone']) ?>"><?= htmlspecialchars($delivery['driver_phone']) ?></a></p>
                                    <?php endif; ?>
                                    <?php if (!empty($delivery['tracking_code'])): ?>
                                        <p class="acct-dim"><?= $fr ? 'Suivi : ' : 'Tracking: ' ?><strong><?= htmlspecialchars($delivery['tracking_code']) ?></strong></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (in_array($status, ['pending', 'processing'], true)): ?>
                            <form class="acct-od-actions" method="POST" action="<?= url('account/orders/cancel') ?>" onsubmit="return confirm(<?= htmlspecialchars(json_encode($fr ? 'Annuler cette commande ?' : 'Cancel this order?')) ?>);">
                                <?= csrfField() ?>
                                <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                <button type="submit" class="acct-btn acct-btn-danger"><i class="fas fa-xmark"></i> <?= $fr ? 'Annuler la commande' : 'Cancel order' ?></button>
                            </form>
                        <?php endif; ?>
                        <?php if ($status === 'delivered'): ?>
                            <div class="acct-od-actions">
                                <a href="<?= url('account/orders/claim?order_id=' . (int) $order['id']) ?>" class="acct-btn acct-btn-ghost"><i class="fas fa-rotate-left"></i> <?= $fr ? 'Demander un retour ou faire une réclamation' : 'Request a return or file a claim' ?></a>
                            </div>
                        <?php endif; ?>
                    </section>

                    <section class="acct-card acct-panel">
                        <h2><?= $fr ? 'Articles commandés' : 'Items ordered' ?></h2>
                        <div class="acct-items">
                            <?php foreach ($items as $item): ?>
                                <?php $qty = (int) $item['quantity']; $price = (float) $item['price']; ?>
                                <div class="acct-item">
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img src="<?= htmlspecialchars(url(ltrim($item['image_path'], '/'))) ?>" alt="" class="acct-item-img" loading="lazy">
                                    <?php else: ?>
                                        <div class="acct-item-img acct-item-noimg"><i class="fas fa-image"></i></div>
                                    <?php endif; ?>
                                    <div class="acct-item-main">
                                        <strong><?= htmlspecialchars($item['product_name'] ?? $item['name'] ?? ($fr ? 'Produit' : 'Product')) ?></strong>
                                        <?php if (!empty($item['variant_name'])): ?><span><?= htmlspecialchars($item['variant_name']) ?></span><?php endif; ?>
                                        <span><?= $qty ?> x <?= acct_money($price, $fr) ?></span>
                                    </div>
                                    <span class="acct-item-total"><?= acct_money($price * $qty, $fr) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="acct-totals">
                            <?php foreach ($rows as $i => [$label, $amount]): ?>
                                <?php if ($i === 0 || $amount > 0): ?>
                                    <div><span><?= htmlspecialchars($label) ?></span><span><?= acct_money($amount, $fr) ?></span></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if ($m('founding_buyer_delivery_waived') > 0): ?>
                                <div class="acct-totals-perk"><span><i class="fa-solid fa-star"></i> <?= $fr ? 'Livraison offerte (Acheteur fondateur)' : 'Free delivery (Founding Buyer)' ?></span><span>-<?= acct_money($m('founding_buyer_delivery_waived'), $fr) ?></span></div>
                            <?php endif; ?>
                            <div class="acct-totals-grand"><span><?= $fr ? 'Total' : 'Total' ?></span><span><?= acct_money($order['total'] ?? 0, $fr) ?></span></div>
                        </div>
                    </section>

                    <?php if ($status === 'delivered' && $ratingDriverId): ?>
                        <section class="acct-card acct-panel">
                            <h2><i class="fas fa-star acct-star-ico"></i> <?= $fr ? 'Évaluez votre livreur' : 'Rate your delivery driver' ?></h2>
                            <?php if ($driverName): ?>
                                <p class="acct-muted"><?= $fr ? 'Livré par' : 'Delivered by' ?> <strong><?= htmlspecialchars($driverName) ?></strong></p>
                            <?php endif; ?>
                            <?php if ($driverRating): ?>
                                <div class="acct-stars-done">
                                    <span><?php for ($i = 1; $i <= 5; $i++): ?><?= $i <= $driverRating['rating'] ? '★' : '☆' ?><?php endfor; ?></span>
                                    <strong><?= (int) $driverRating['rating'] ?>/5</strong>
                                </div>
                                <?php if (!empty($driverRating['comment'])): ?>
                                    <p class="acct-muted"><em>"<?= htmlspecialchars($driverRating['comment']) ?>"</em></p>
                                <?php endif; ?>
                                <p class="acct-dim"><?= $fr ? 'Vous avez déjà évalué cette livraison. Merci !' : 'You already rated this delivery. Thank you!' ?></p>
                            <?php else: ?>
                                <form method="POST" action="<?= url('account/orders/rate-driver') ?>">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                    <input type="hidden" name="rating" id="ratingValue" value="0">
                                    <div class="acct-stars" id="starRow">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <button type="button" class="acct-star" data-val="<?= $i ?>" aria-label="<?= $i ?>/5">★</button>
                                        <?php endfor; ?>
                                    </div>
                                    <p id="ratingLabel" class="acct-dim acct-rating-label"></p>
                                    <textarea name="comment" class="acct-textarea" placeholder="<?= htmlspecialchars($fr ? 'Laissez un commentaire (facultatif)' : 'Leave a comment (optional)') ?>" maxlength="500"></textarea>
                                    <button type="submit" class="acct-btn acct-btn-primary" id="submitRating" disabled><?= $fr ? "Soumettre l'évaluation" : 'Submit rating' ?></button>
                                </form>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>

                    <?php if (!empty($statusHistory)): ?>
                        <section class="acct-card acct-panel">
                            <h2><?= $fr ? 'Activité de la commande' : 'Order activity' ?></h2>
                            <ol class="acct-timeline">
                                <?php foreach ($statusHistory as $h): ?>
                                    <?php $hs = (string) ($h['new_status'] ?? $h['status'] ?? ''); ?>
                                    <li>
                                        <strong><?= htmlspecialchars($hs !== '' ? acct_status($hs, $fr) : ($fr ? 'Mise à jour' : 'Update')) ?></strong>
                                        <?php if (!empty($h['notes'])): ?><span><?= htmlspecialchars($h['notes']) ?></span><?php endif; ?>
                                        <time><?= acct_datetime($h['created_at'], $fr) ?></time>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endif; ?>
<?php require __DIR__ . '/partials/account-bottom.php'; ?>

<?php if ($status === 'delivered' && $ratingDriverId && !$driverRating): ?>
<script>
(function () {
    var labels = <?= json_encode($rateLabels, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS) ?>;
    var stars = document.querySelectorAll('.acct-star');
    stars.forEach(function (b) {
        b.addEventListener('click', function () {
            var val = +b.dataset.val;
            document.getElementById('ratingValue').value = val;
            document.getElementById('ratingLabel').textContent = labels[val];
            document.getElementById('submitRating').disabled = false;
            stars.forEach(function (s, i) { s.classList.toggle('active', i < val); });
        });
        b.addEventListener('mouseenter', function () {
            var v = +b.dataset.val;
            stars.forEach(function (s, i) { s.classList.toggle('hover', i < v); });
        });
        b.addEventListener('mouseleave', function () {
            stars.forEach(function (s) { s.classList.remove('hover'); });
        });
    });
})();
</script>
<?php endif; ?>
<?php if (!in_array($status, ['delivered', 'cancelled', 'refunded'], true)): ?>
<script>
// Live status tracking: poll while the order is active, reload on any change
(function () {
    const orderId = <?= (int) ($order['id'] ?? 0) ?>;
    const currentStatus = <?= json_encode($status) ?>;
    const currentDriverStatus = <?= json_encode($order['driver_status'] ?? null) ?>;
    if (!orderId) return;
    setInterval(function () {
        fetch('<?= url('api/buyer/order/status') ?>?id=' + orderId, { cache: 'no-store' })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (!data || !data.success) return;
                if (data.status !== currentStatus || data.driver_status !== currentDriverStatus) {
                    window.location.reload();
                }
            })
            .catch(() => {});
    }, 15000);
})();
</script>
<?php endif; ?>
</body>
</html>
