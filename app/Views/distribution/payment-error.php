<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = ([
    'en' => [
        'page_title'    => 'Payment Error - OCSAPP Distribution',
        'title'         => 'Payment Error',
        'subtitle'      => "We couldn't process your payment",
        'help_text'     => 'If you believe this is an error, please contact our support team. If your payment link has expired, you can request a new one from your account manager.',
        'go_dashboard'  => 'Go to Dashboard',
        'view_requests' => 'View My Requests',
        'need_help'     => 'Need help? Contact our support team',
    ],
    'fr' => [
        'page_title'    => 'Erreur de paiement - OCSAPP Distribution',
        'title'         => 'Erreur de paiement',
        'subtitle'      => "Nous n'avons pas pu traiter votre paiement",
        'help_text'     => "Si vous croyez qu'il s'agit d'une erreur, veuillez contacter notre équipe de support. Si votre lien de paiement a expiré, vous pouvez en demander un nouveau à votre gestionnaire de compte.",
        'go_dashboard'  => 'Aller au tableau de bord',
        'view_requests' => 'Voir mes demandes',
        'need_help'     => "Besoin d'aide? Contactez notre équipe de support",
    ],
])[$currentLang] ?? [];

$currentPage = 'requests';
$pageTitle = $t['page_title'];
$_pageT = $t; // preserve before layout-header.php overwrites $t
require __DIR__ . '/layout-header.php';
$t = $_pageT; unset($_pageT); // restore page-specific translations
?>
    <div class="error-container">
        <div class="error-card">
            <div class="error-header">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 class="error-title"><?= $t['title'] ?></h1>
                <p class="error-subtitle"><?= $t['subtitle'] ?></p>
            </div>

            <div class="error-body">
                <div class="error-message">
                    <p><?= htmlspecialchars($message ?? 'An error occurred while processing your payment.') ?></p>
                </div>

                <p class="help-text"><?= $t['help_text'] ?></p>

                <div class="btn-group">
                    <a href="<?= url('distribution/dashboard') ?>" class="btn btn-primary">
                        <i class="fas fa-home"></i>
                        <?= $t['go_dashboard'] ?>
                    </a>
                    <a href="<?= url('distribution/requests') ?>" class="btn btn-secondary">
                        <i class="fas fa-list"></i>
                        <?= $t['view_requests'] ?>
                    </a>
                </div>

                <div class="contact-info">
                    <p><?= $t['need_help'] ?></p>
                    <a href="mailto:support@ocsapp.ca" class="contact-link">
                        <i class="fas fa-envelope" style="margin-right: 4px;"></i>
                        support@ocsapp.ca
                    </a>
                </div>
            </div>
        </div>

        <div class="error-footer">
            OCSAPP Distribution &copy; <?= date('Y') ?>
        </div>
    </div>
<?php require __DIR__ . '/layout-footer.php'; ?>
