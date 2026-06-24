<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = ([
    'en' => [
        'page_title'      => 'Payment Successful - OCSAPP Distribution',
        'title'           => 'Payment Successful!',
        'subtitle'        => 'Thank you for your payment',
        'order_number'    => 'Order Number',
        'amount_paid'     => 'Amount Paid',
        'next_steps_title'=> 'What Happens Next?',
        'step1'           => 'Our team will begin procuring your items from our suppliers.',
        'step2'           => "You'll receive an email when your order is ready for delivery.",
        'step3'           => 'Your order will be delivered to your specified address.',
        'view_order'      => 'View Order Details',
        'go_dashboard'    => 'Return to Dashboard',
        'email_notice'    => 'A confirmation email has been sent to',
    ],
    'fr' => [
        'page_title'      => 'Paiement réussi - OCSAPP Distribution',
        'title'           => 'Paiement réussi !',
        'subtitle'        => 'Merci pour votre paiement',
        'order_number'    => 'Numéro de commande',
        'amount_paid'     => 'Montant payé',
        'next_steps_title'=> 'Que se passe-t-il ensuite ?',
        'step1'           => 'Notre équipe commencera à approvisionner vos articles auprès de nos fournisseurs.',
        'step2'           => 'Vous recevrez un courriel lorsque votre commande sera prête pour la livraison.',
        'step3'           => "Votre commande sera livrée à l'adresse spécifiée.",
        'view_order'      => 'Voir les détails de la commande',
        'go_dashboard'    => 'Retour au tableau de bord',
        'email_notice'    => 'Un courriel de confirmation a été envoyé à',
    ],
])[$currentLang] ?? [];

$currentPage = 'requests';
$pageTitle = $t['page_title'];
$_pageT = $t;
require __DIR__ . '/layout-header.php';
$t = $_pageT; unset($_pageT);
?>
    <div class="success-container">
        <div class="success-card">
            <div class="success-header">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="success-title"><?= $t['title'] ?></h1>
                <p class="success-subtitle"><?= $t['subtitle'] ?></p>
            </div>

            <div class="success-body">
                <div class="order-info">
                    <div class="order-number"><?= $t['order_number'] ?></div>
                    <div class="order-value">#<?= htmlspecialchars($request['request_number']) ?></div>

                    <div class="amount-paid">
                        <div class="amount-label"><?= $t['amount_paid'] ?></div>
                        <div class="amount-value">$<?= number_format($request['total_amount'], 2) ?> CAD</div>
                    </div>
                </div>

                <div class="next-steps">
                    <h3><i class="fas fa-clipboard-list" style="margin-right: 8px; color: var(--primary);"></i><?= $t['next_steps_title'] ?></h3>
                    <ul class="step-list">
                        <li>
                            <span class="step-number">1</span>
                            <span class="step-text"><?= $t['step1'] ?></span>
                        </li>
                        <li>
                            <span class="step-number">2</span>
                            <span class="step-text"><?= $t['step2'] ?></span>
                        </li>
                        <li>
                            <span class="step-number">3</span>
                            <span class="step-text"><?= $t['step3'] ?></span>
                        </li>
                    </ul>
                </div>

                <div style="text-align:center;margin:20px 0 8px;">
                    <p style="font-size:14px;color:#6b7280;margin-bottom:12px;">
                        <?= $currentLang === 'fr' ? 'Redirection vers votre commande dans' : 'Redirecting to your order in' ?>
                        <strong id="redirectCountdown">5</strong>
                        <?= $currentLang === 'fr' ? 'secondes…' : 'seconds…' ?>
                    </p>
                    <a href="<?= url('distribution/requests/show?id=' . $request['id']) ?>" class="btn btn-primary" id="viewOrderBtn">
                        <i class="fas fa-eye"></i>
                        <?= $t['view_order'] ?>
                    </a>
                </div>

                <div class="email-notice" style="margin-top:16px;">
                    <i class="fas fa-envelope"></i>
                    <span><?= $t['email_notice'] ?> <strong><?= htmlspecialchars($request['email']) ?></strong></span>
                </div>
            </div>
        </div>

        <div class="success-footer">
            OCSAPP Distribution &copy; <?= date('Y') ?>
        </div>
    </div>

<script>
(function() {
    var orderUrl = '<?= url('distribution/requests/show?id=' . $request['id']) ?>';
    var seconds  = 5;
    var el       = document.getElementById('redirectCountdown');
    var timer    = setInterval(function() {
        seconds--;
        if (el) el.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            window.location.href = orderUrl;
        }
    }, 1000);
})();
</script>
<?php require __DIR__ . '/layout-footer.php'; ?>
