<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = ([
    'en' => [
        'page_title'          => 'Complete Payment - OCSAPP Distribution',
        'secure_checkout'     => 'Secure Checkout',
        'title'               => 'Complete Your Payment',
        'payment_cancelled'   => 'Payment Cancelled',
        'payment_cancelled_desc' => "Your payment was cancelled. You can try again when you're ready.",
        'approved'            => 'Approved',
        'view_items'          => 'View Order Items',
        'catalog_items'       => 'Catalog Items',
        'sku'                 => 'SKU',
        'qty'                 => 'Qty',
        'shopping_items'      => 'Shopping List Items',
        'items_total'         => 'Items Total',
        'service_fee'         => 'Service Fee',
        'handling_label'      => 'Handling',
        'delivery_fee'        => 'Delivery Fee',
        'gst'                 => 'GST (5%)',
        'qst'                 => 'QST (9.975%)',
        'tip'                 => 'Tip',
        'custom'              => 'Custom',
        'total'               => 'Total',
        'delivery_info'       => 'Delivery Information',
        'delivery_address'    => 'Delivery Address',
        'preferred_date'      => 'Preferred Delivery Date',
        'amount_due'          => 'Amount Due',
        'link_expires'        => 'Payment Link Expires',
        'pay_prefix'          => 'Pay',
        'we_accept'           => 'We accept:',
        'ssl_notice'          => 'Your payment is secured with 256-bit SSL encryption',
        'rights_reserved'     => 'All Rights Reserved.',
        'error_try_again'     => 'An error occurred. Please try again.',
    ],
    'fr' => [
        'page_title'          => 'Compléter le paiement - OCSAPP Distribution',
        'secure_checkout'     => 'Paiement sécurisé',
        'title'               => 'Complétez votre paiement',
        'payment_cancelled'   => 'Paiement annulé',
        'payment_cancelled_desc' => 'Votre paiement a été annulé. Vous pouvez réessayer quand vous le souhaitez.',
        'approved'            => 'Approuvé',
        'view_items'          => 'Voir les articles',
        'catalog_items'       => 'Articles du catalogue',
        'sku'                 => 'UGS',
        'qty'                 => 'Qté',
        'shopping_items'      => "Articles de la liste d'achats",
        'items_total'         => 'Total des articles',
        'service_fee'         => 'Frais de service',
        'handling_label'      => 'Manutention',
        'delivery_fee'        => 'Frais de livraison',
        'gst'                 => 'TPS (5%)',
        'qst'                 => 'TVQ (9,975%)',
        'tip'                 => 'Pourboire',
        'custom'              => 'Personnalisé',
        'total'               => 'Total',
        'delivery_info'       => 'Informations de livraison',
        'delivery_address'    => 'Adresse de livraison',
        'preferred_date'      => 'Date de livraison préférée',
        'amount_due'          => 'Montant dû',
        'link_expires'        => 'Le lien de paiement expire',
        'pay_prefix'          => 'Payer',
        'we_accept'           => 'Nous acceptons :',
        'ssl_notice'          => 'Votre paiement est sécurisé par un chiffrement SSL 256 bits',
        'rights_reserved'     => 'Tous droits réservés.',
        'error_try_again'     => 'Une erreur est survenue. Veuillez réessayer.',
    ],
])[$currentLang] ?? [];

$currentPage = 'requests';
$pageTitle = $t['page_title'];
$_pageT = $t; // preserve before layout-header.php overwrites $t
require __DIR__ . '/layout-header.php';
$t = $_pageT; unset($_pageT); // restore page-specific translations
?>
        <div class="payment-layout">
        <!-- Order Summary Column -->
        <div class="order-column">
            <h1 class="section-title"><?= $t['title'] ?></h1>

            <?php if (isset($_GET['cancelled'])): ?>
                <div class="cancel-notice">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong><?= $t['payment_cancelled'] ?></strong><br>
                        <?= $t['payment_cancelled_desc'] ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Request Info Card -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Request #<?= htmlspecialchars($request['request_number']) ?>
                    </div>
                    <span class="status-badge status-approved">
                        <i class="fas fa-check-circle"></i>
                        <?= $t['approved'] ?>
                    </span>
                </div>
                <div class="card-body">
                <p class="pay-request-meta">
                    <strong><?= htmlspecialchars($request['request_name']) ?></strong><br>
                    <?= htmlspecialchars($request['company_name']) ?>
                </p>

                <!-- Mobile toggle -->
                <div class="items-toggle" onclick="toggleItems(this)">
                    <span><?= $t['view_items'] ?> (<?= count($catalogItems) + count($shoppingItems) ?>)</span>
                    <i class="fas fa-chevron-down"></i>
                </div>

                <div class="order-details">
                    <!-- Catalog Items grouped by supplier -->
                    <?php if (!empty($catalogItems)):
                        $bySupplier = [];
                        foreach ($catalogItems as $item) {
                            $bySupplier[$item['supplier_name'] ?? 'Unknown Supplier'][] = $item;
                        }
                    ?>
                        <h4 class="items-section-label">
                            <i class="fas fa-box" style="margin-right: 6px;"></i><?= $t['catalog_items'] ?>
                        </h4>
                        <?php foreach ($bySupplier as $supplierName => $items): ?>
                            <div style="margin-bottom:12px;">
                                <div style="display:flex;align-items:center;gap:7px;padding:6px 0 8px;border-bottom:1px solid #e5e7eb;margin-bottom:4px;">
                                    <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;background:#f0fdf4;border-radius:50%;flex-shrink:0;">
                                        <i class="fas fa-store" style="color:#00b207;font-size:10px;"></i>
                                    </span>
                                    <span style="font-size:13px;font-weight:700;color:#111827;"><?= htmlspecialchars($supplierName) ?></span>
                                    <span style="font-size:11px;color:#9ca3af;">(<?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?>)</span>
                                </div>
                                <div class="order-items">
                                    <?php foreach ($items as $item): ?>
                                        <div class="item-row">
                                            <div class="item-info">
                                                <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                                <div class="item-details">
                                                    <?= $t['sku'] ?>: <?= htmlspecialchars($item['sku'] ?? 'N/A') ?> &bull;
                                                    <?= $t['qty'] ?>: <?= (int)$item['quantity'] ?>
                                                </div>
                                            </div>
                                            <div class="item-price">
                                                $<?= number_format($item['quantity'] * $item['unit_price'], 2) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Shopping List Items -->
                    <?php if (!empty($shoppingItems)): ?>
                        <h4 class="items-section-label items-section-label--gap">
                            <i class="fas fa-shopping-basket" style="margin-right: 6px;"></i><?= $t['shopping_items'] ?>
                        </h4>
                        <div class="order-items">
                            <?php foreach ($shoppingItems as $item): ?>
                                <div class="item-row">
                                    <div class="item-info">
                                        <div class="item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                                        <div class="item-details">
                                            <?= $t['qty'] ?>: <?= (int)$item['quantity'] ?> <?= htmlspecialchars($item['unit'] ?? 'units') ?>
                                        </div>
                                    </div>
                                    <div class="item-price">
                                        $<?= number_format($item['quantity'] * ($item['unit_price'] ?? 0), 2) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Summary -->
                <div class="summary-row subtotal">
                    <span class="summary-label"><?= $t['items_total'] ?></span>
                    <span class="summary-value">$<?= number_format($request['items_total'] ?? 0, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label"><?= $t['service_fee'] ?></span>
                    <span class="summary-value">$<?= number_format($request['service_fee'] ?? 0, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label"><?= $t['handling_label'] ?> (<?= number_format($request['total_weight_kg'] ?? 0, 1) ?> kg × $0.20/kg)</span>
                    <span class="summary-value">$<?= number_format($request['handling_fee'] ?? 0, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label"><?= $t['delivery_fee'] ?></span>
                    <span class="summary-value">$<?= number_format($request['delivery_fee'] ?? 0, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label"><?= $t['gst'] ?></span>
                    <span class="summary-value">$<?= number_format($request['gst_amount'] ?? 0, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label"><?= $t['qst'] ?></span>
                    <span class="summary-value">$<?= number_format($request['qst_amount'] ?? 0, 2) ?></span>
                </div>
                <?php if (!empty($request['tip_amount']) && $request['tip_amount'] > 0): ?>
                <div class="summary-row">
                    <span class="summary-label"><?= $t['tip'] ?> <?= (int)($request['tip_percentage'] ?? 0) > 0 ? '(' . (int)$request['tip_percentage'] . '%)' : '(' . $t['custom'] . ')' ?></span>
                    <span class="summary-value">$<?= number_format($request['tip_amount'], 2) ?></span>
                </div>
                <?php endif; ?>
                <div class="summary-row total">
                    <span class="summary-label"><?= $t['total'] ?></span>
                    <span class="summary-value">$<?= number_format($request['total_amount'], 2) ?> CAD</span>
                </div>
                </div><!-- /card-body -->
            </div>

            <!-- Delivery Info -->
            <div class="card">
                <div class="card-body">
                <div class="card-title delivery-card-title">
                    <i class="fas fa-truck"></i>
                    <?= $t['delivery_info'] ?>
                </div>
                <div class="delivery-info">
                    <h4><?= $t['delivery_address'] ?></h4>
                    <p>
                        <?= htmlspecialchars($request['delivery_street']) ?><br>
                        <?= htmlspecialchars($request['delivery_city']) ?>, <?= htmlspecialchars($request['delivery_province']) ?><br>
                        <?= htmlspecialchars($request['delivery_postal_code']) ?>
                    </p>
                </div>
                <?php if (!empty($request['preferred_delivery_date'])): ?>
                    <div class="delivery-info">
                        <h4><?= $t['preferred_date'] ?></h4>
                        <p><?= date('l, F j, Y', strtotime($request['preferred_delivery_date'])) ?></p>
                    </div>
                <?php endif; ?>
                </div><!-- /card-body -->
            </div>
        </div>

        <!-- Payment Column -->
        <div class="payment-column">
            <div class="card payment-card">
                <div class="card-body">
                <div class="total-display">
                    <div class="total-label"><?= $t['amount_due'] ?></div>
                    <div class="total-amount">
                        <span class="total-currency">$</span><?= number_format($request['total_amount'], 2) ?>
                        <span class="total-currency">CAD</span>
                    </div>
                </div>

                <!-- Expiry Notice -->
                <div class="expiry-notice">
                    <i class="fas fa-clock"></i>
                    <div>
                        <strong><?= $t['link_expires'] ?></strong><br>
                        <?= date('F j, Y \a\t g:i A', strtotime($request['payment_link_expires_at'])) ?>
                    </div>
                </div>

                <!-- Pay Button -->
                <button type="button" class="btn-pay" id="payButton" onclick="handlePayment()">
                    <span class="spinner"></span>
                    <span class="btn-text">
                        <i class="fas fa-lock"></i>
                        <?= $t['pay_prefix'] ?> $<?= number_format($request['total_amount'], 2) ?> CAD
                    </span>
                </button>

                <!-- Payment Methods -->
                <div class="payment-methods">
                    <span><?= $t['we_accept'] ?></span>
                    <?php if ($activeGateway === 'stripe'): ?>
                        <i class="fab fa-cc-visa fa-2x" style="color: #1a1f71;"></i>
                        <i class="fab fa-cc-mastercard fa-2x" style="color: #eb001b;"></i>
                        <i class="fab fa-cc-amex fa-2x" style="color: #006fcf;"></i>
                    <?php elseif ($activeGateway === 'paypal'): ?>
                        <i class="fab fa-paypal fa-2x" style="color: #003087;"></i>
                        <i class="fab fa-cc-visa fa-2x" style="color: #1a1f71;"></i>
                        <i class="fab fa-cc-mastercard fa-2x" style="color: #eb001b;"></i>
                    <?php elseif ($activeGateway === 'venn'): ?>
                        <span style="font-weight: 700; font-size: 14px; color: #1a1a1a;">Interac</span>
                        <i class="fab fa-cc-visa fa-2x" style="color: #1a1f71;"></i>
                        <i class="fab fa-cc-mastercard fa-2x" style="color: #eb001b;"></i>
                    <?php endif; ?>
                </div>

                <div class="security-info">
                    <i class="fas fa-shield-alt"></i>
                    <?= $t['ssl_notice'] ?>
                </div>
                </div><!-- /card-body -->
            </div>
        </div>
        </div><!-- /payment-layout -->

    <footer class="pay-footer">
        OCSAPP Distribution &copy; <?= date('Y') ?>. <?= $t['rights_reserved'] ?>
    </footer>

    <?php if ($activeGateway === 'stripe'): ?>
    <script src="https://js.stripe.com/v3/"></script>
    <?php elseif ($activeGateway === 'paypal'): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?= htmlspecialchars($gatewayConfig['client_id']) ?>&currency=CAD"></script>
    <?php endif; ?>

    <script>
        const activeGateway = '<?= htmlspecialchars($activeGateway) ?>';
        const paymentToken = '<?= htmlspecialchars($token) ?>';
        const gatewayConfig = <?= json_encode($gatewayConfig) ?>;
        const errMsg = <?= json_encode($t['error_try_again']) ?>;

        <?php if ($activeGateway === 'stripe'): ?>
        // Initialize Stripe (only if key is configured)
        const stripe = gatewayConfig.publishable_key ? Stripe(gatewayConfig.publishable_key) : null;
        <?php endif; ?>

        function handlePayment() {
            const button = document.getElementById('payButton');
            button.classList.add('loading');
            button.disabled = true;

            // Create payment session
            fetch('<?= url('distribution/pay/create-session') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
                body: 'token=' + encodeURIComponent(paymentToken) + '&<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>=' + encodeURIComponent(document.querySelector('meta[name="csrf-token"]').content)
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    button.classList.remove('loading');
                    button.disabled = false;
                    return;
                }

                // Route to appropriate gateway
                switch (data.gateway) {
                    case 'stripe':
                        return stripe.redirectToCheckout({ sessionId: data.sessionId });

                    case 'paypal':
                        // Redirect to PayPal approval URL
                        if (data.approvalUrl) {
                            window.location.href = data.approvalUrl;
                        }
                        break;

                    case 'venn':
                        // Redirect to Venn.ca payment URL
                        if (data.paymentUrl) {
                            window.location.href = data.paymentUrl;
                        }
                        break;
                }
            })
            .then(result => {
                if (result && result.error) {
                    alert(result.error.message);
                    button.classList.remove('loading');
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(errMsg);
                button.classList.remove('loading');
                button.disabled = false;
            });
        }

        function toggleItems(element) {
            element.classList.toggle('active');
            document.querySelector('.order-details').classList.toggle('show');
        }
    </script>
<?php require __DIR__ . '/layout-footer.php'; ?>
