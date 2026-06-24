<?php
/**
 * OCSAPP Driver Central Page
 * Public landing page for delivery driver recruitment
 * Bilingual: EN / FR
 */
$currentLang = $_SESSION['language'] ?? 'fr';

$text = [
    'en' => [
        'page_title'         => 'Driver Central - Deliver with OCSAPP',
        'eyebrow'            => 'Driver Program',
        'hero_headline'      => 'Deliver with <span>OCSAPP</span>',
        'hero_sub'           => 'Earn on your schedule. Choose your hours. Get paid weekly. Join our growing fleet of drivers across Canada.',
        'badge_emission'     => 'Zero-Emission Delivery',
        'badge_zone'         => 'West Island · Grand Montréal',
        'badge_payout'       => 'Weekly Payouts',
        'badge_flex'         => 'Flexible Hours',
        'apply_btn'          => 'Apply to Drive',
        'dashboard_btn'      => 'My Dashboard',
        'login_btn'          => 'Driver Login',
        'stat_hourly'        => 'Average hourly earnings',
        'stat_payouts'       => 'Weekly payouts',
        'stat_types'         => 'Delivery opportunities',
        'stat_schedule'      => 'Flexible scheduling',
        'benefits_title'     => 'Why Drive with OCSAPP?',
        'benefits_sub'       => 'Everything you need to earn on your own terms',
        'b1_title'           => 'Flexible Hours',
        'b1_text'            => 'Work when it fits your life. Set your own schedule and toggle your availability on and off from your driver dashboard at any time.',
        'b2_title'           => 'Competitive Pay',
        'b2_text'            => 'Earn a delivery fee on every completed run plus keep 100% of any tips. Earnings are tracked in real time in your portal.',
        'b3_title'           => 'Zone-Based Routes',
        'b3_text'            => 'Deliveries are matched to your zone so you\'re never driving across the city for a single drop. Short, efficient routes mean more runs per hour.',
        'b4_title'           => 'Variety of Work',
        'b4_text'            => 'Handle B2C customer orders, B2B business distributions, and supplier pickups - multiple income streams all through one app.',
        'b5_title'           => 'Easy-to-Use App',
        'b5_text'            => 'Your driver dashboard works on any mobile browser. Accept jobs, update statuses, and track your earnings - all from your phone.',
        'b6_title'           => 'Admin Support',
        'b6_text'            => 'Our operations team is available to help with any issues. You\'re never on your own - support is just a message away.',
        'types_title'        => 'What You\'ll Deliver',
        'types_sub'          => 'Three distinct delivery types - one driver portal',
        't1_title'           => 'Customer Orders (B2C)',
        't1_text'            => 'Pick up orders from our marketplace shops and deliver them directly to customers at their door. Fast, local, and frequent.',
        't2_title'           => 'Business Distributions (B2B)',
        't2_text'            => 'Deliver bulk orders to our business partners and distribution clients. Larger drops with higher payouts per run.',
        't3_title'           => 'Supplier Pickups',
        't3_text'            => 'Collect inventory from our vetted suppliers and bring it back to the OCSAPP warehouse. Pre-scheduled, predictable runs.',
        'how_title'          => 'How It Works',
        'how_sub'            => 'From application to first delivery - here\'s the process',
        's1_title'           => 'Apply Online',
        's1_text'            => 'Fill out our short application form. Takes less than 5 minutes.',
        's2_title'           => 'Get Approved',
        's2_text'            => 'Our team reviews your application within 1–3 business days.',
        's3_title'           => 'Start Delivering',
        's3_text'            => 'Log in, go online, and accept the deliveries assigned to you.',
        's4_title'           => 'Get Paid',
        's4_text'            => 'Earnings are tracked automatically and paid out every week.',
        'req_title'          => 'Requirements',
        'req_sub'            => 'What you need to get started',
        'r1_title'           => 'Age 18+',
        'r1_text'            => 'Must be at least 18 years old at the time of application.',
        'r2_title'           => 'Valid Driver\'s Licence',
        'r2_text'            => 'Full, valid Canadian licence (or equivalent). Cyclists & e-bike riders welcome too.',
        'r3_title'           => 'Vehicle Insurance',
        'r3_text'            => 'Your vehicle must be insured. We\'ll verify this during onboarding.',
        'r4_title'           => 'Smartphone',
        'r4_text'            => 'Any modern phone with a browser is all you need to access your dashboard.',
        'r5_title'           => 'Reliable Vehicle',
        'r5_text'            => 'Car, van, motorcycle, bicycle, or e-bike - we work with all vehicle types.',
        'r6_title'           => 'Good Standing',
        'r6_text'            => 'Clean record preferred. A background check is part of our approval process.',
        'cta_title'          => 'Ready to Start Earning?',
        'cta_sub'            => 'Join the OCSAPP driver network today. Apply in minutes - start delivering this week.',
        'cta_apply'          => 'Apply Now - It\'s Free',
        'cta_dashboard'      => 'Go to My Dashboard',
        'cta_login'          => 'Already a Driver? Sign In',
    ],
    'fr' => [
        'page_title'         => 'Chauffeur Central - Livrez avec OCSAPP',
        'eyebrow'            => 'Programme Chauffeur',
        'hero_headline'      => 'Livrez avec <span>OCSAPP</span>',
        'hero_sub'           => 'Gagnez selon votre horaire. Choisissez vos heures. Soyez payé chaque semaine. Rejoignez notre flotte de chauffeurs en pleine croissance à travers le Canada.',
        'badge_emission'     => 'Livraison zéro émission',
        'badge_zone'         => 'West Island · Grand Montréal',
        'badge_payout'       => 'Paie hebdomadaire',
        'badge_flex'         => 'Horaire flexible',
        'apply_btn'          => 'Postuler comme chauffeur',
        'dashboard_btn'      => 'Mon tableau de bord',
        'login_btn'          => 'Connexion chauffeur',
        'stat_hourly'        => 'Gains horaires moyens',
        'stat_payouts'       => 'Paiements hebdomadaires',
        'stat_types'         => 'Types de livraisons disponibles',
        'stat_schedule'      => 'Horaire flexible',
        'benefits_title'     => 'Pourquoi livrer avec OCSAPP?',
        'benefits_sub'       => 'Tout ce qu\'il vous faut pour gagner à votre façon',
        'b1_title'           => 'Horaires flexibles',
        'b1_text'            => 'Travaillez quand ça vous convient. Définissez votre propre horaire et activez ou désactivez votre disponibilité depuis votre tableau de bord à tout moment.',
        'b2_title'           => 'Rémunération compétitive',
        'b2_text'            => 'Gagnez des frais de livraison pour chaque course complétée et gardez 100% des pourboires. Vos gains sont suivis en temps réel dans votre portail.',
        'b3_title'           => 'Trajets par zone',
        'b3_text'            => 'Les livraisons sont associées à votre zone, donc vous ne traversez jamais toute la ville pour une seule livraison. Des trajets courts et efficaces permettent plus de courses par heure.',
        'b4_title'           => 'Variété de travail',
        'b4_text'            => 'Gérez les commandes clients B2C, les distributions B2B et les collectes chez les fournisseurs - plusieurs sources de revenus via une seule application.',
        'b5_title'           => 'Application facile à utiliser',
        'b5_text'            => 'Votre tableau de bord chauffeur fonctionne sur n\'importe quel navigateur mobile. Acceptez des missions, mettez à jour les statuts et suivez vos gains - tout depuis votre téléphone.',
        'b6_title'           => 'Support administratif',
        'b6_text'            => 'Notre équipe opérationnelle est disponible pour vous aider. Vous n\'êtes jamais seul - le support est à portée d\'un message.',
        'types_title'        => 'Ce que vous livrerez',
        'types_sub'          => 'Trois types de livraison distincts - un seul portail chauffeur',
        't1_title'           => 'Commandes clients (B2C)',
        't1_text'            => 'Récupérez les commandes dans nos boutiques et livrez-les directement aux clients à leur porte. Rapide, local et fréquent.',
        't2_title'           => 'Distributions aux entreprises (B2B)',
        't2_text'            => 'Livrez des commandes en gros à nos partenaires commerciaux et clients de distribution. Des dépôts plus importants avec des paiements plus élevés par course.',
        't3_title'           => 'Collectes chez les fournisseurs',
        't3_text'            => 'Collectez l\'inventaire chez nos fournisseurs vérifiés et ramenez-le à l\'entrepôt OCSAPP. Des courses planifiées à l\'avance et prévisibles.',
        'how_title'          => 'Comment ça marche',
        'how_sub'            => 'De la candidature à la première livraison - voici le processus',
        's1_title'           => 'Postuler en ligne',
        's1_text'            => 'Remplissez notre courte demande. Prend moins de 5 minutes.',
        's2_title'           => 'Obtenir l\'approbation',
        's2_text'            => 'Notre équipe examine votre candidature en 1 à 3 jours ouvrables.',
        's3_title'           => 'Commencer à livrer',
        's3_text'            => 'Connectez-vous, passez en ligne et acceptez les livraisons qui vous sont assignées.',
        's4_title'           => 'Être payé',
        's4_text'            => 'Les gains sont suivis automatiquement et versés chaque semaine.',
        'req_title'          => 'Exigences',
        'req_sub'            => 'Ce dont vous avez besoin pour commencer',
        'r1_title'           => '18 ans et plus',
        'r1_text'            => 'Vous devez avoir au moins 18 ans au moment de la candidature.',
        'r2_title'           => 'Permis de conduire valide',
        'r2_text'            => 'Permis canadien complet et valide (ou équivalent). Cyclistes et vélos électriques aussi bienvenus.',
        'r3_title'           => 'Assurance véhicule',
        'r3_text'            => 'Votre véhicule doit être assuré. Nous le vérifierons lors de l\'intégration.',
        'r4_title'           => 'Téléphone intelligent',
        'r4_text'            => 'N\'importe quel téléphone moderne avec un navigateur suffit pour accéder à votre tableau de bord.',
        'r5_title'           => 'Véhicule fiable',
        'r5_text'            => 'Voiture, camionnette, moto, vélo ou vélo électrique - nous travaillons avec tous les types de véhicules.',
        'r6_title'           => 'Bonne réputation',
        'r6_text'            => 'Un dossier vierge est préférable. Une vérification des antécédents fait partie de notre processus d\'approbation.',
        'cta_title'          => 'Prêt à commencer à gagner?',
        'cta_sub'            => 'Rejoignez le réseau de chauffeurs OCSAPP aujourd\'hui. Postulez en quelques minutes - commencez à livrer cette semaine.',
        'cta_apply'          => 'Postuler maintenant - C\'est gratuit',
        'cta_dashboard'      => 'Aller à mon tableau de bord',
        'cta_login'          => 'Déjà chauffeur? Se connecter',
    ],
];

// Use $dc (driver central) to avoid conflict with header.php's $t variable
$dc = $text[$currentLang] ?? $text['en'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($dc['page_title']) ?></title>
    <?= csrfMeta() ?>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/pages/driver-central.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/../components/header.php'; ?>
    <?php // Note: header.php resets $t - use $dc for this page's translations ?>

    <!-- Hero -->
    <section class="dc-hero">
        <span class="dc-eyebrow"><i class="fas fa-truck"></i> <?= htmlspecialchars($dc['eyebrow']) ?></span>
        <span class="dc-hero-icon">🚚</span>
        <h1><?= $dc['hero_headline'] ?></h1>
        <p><?= htmlspecialchars($dc['hero_sub']) ?></p>
        <div class="dc-hero-badges">
            <span class="dc-hero-badge"><i class="fas fa-leaf"></i> <?= htmlspecialchars($dc['badge_emission']) ?></span>
            <span class="dc-hero-badge"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($dc['badge_zone']) ?></span>
            <span class="dc-hero-badge"><i class="fas fa-wallet"></i> <?= htmlspecialchars($dc['badge_payout']) ?></span>
            <span class="dc-hero-badge"><i class="fas fa-clock"></i> <?= htmlspecialchars($dc['badge_flex']) ?></span>
        </div>
        <div class="dc-hero-btns">
            <a href="<?= url('delivery/apply') ?>" class="dc-btn-primary">
                <i class="fas fa-rocket"></i> <?= htmlspecialchars($dc['apply_btn']) ?>
            </a>
            <?php if (function_exists('isLoggedIn') && isLoggedIn() && hasRole('delivery')): ?>
                <a href="<?= url('delivery/dashboard') ?>" class="dc-btn-secondary">
                    <i class="fas fa-tachometer-alt"></i> <?= htmlspecialchars($dc['dashboard_btn']) ?>
                </a>
            <?php else: ?>
                <a href="<?= url('delivery/login') ?>" class="dc-btn-secondary">
                    <i class="fas fa-sign-in-alt"></i> <?= htmlspecialchars($dc['login_btn']) ?>
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Stats bar -->
    <div class="dc-stats">
        <div class="dc-stat">
            <div class="dc-stat-value">$18+</div>
            <div class="dc-stat-label"><?= htmlspecialchars($dc['stat_hourly']) ?></div>
        </div>
        <div class="dc-stat">
            <div class="dc-stat-value">7 <?= $currentLang === 'fr' ? 'jours' : 'days' ?></div>
            <div class="dc-stat-label"><?= htmlspecialchars($dc['stat_payouts']) ?></div>
        </div>
        <div class="dc-stat">
            <div class="dc-stat-value">3 types</div>
            <div class="dc-stat-label"><?= htmlspecialchars($dc['stat_types']) ?></div>
        </div>
        <div class="dc-stat">
            <div class="dc-stat-value">24/7</div>
            <div class="dc-stat-label"><?= htmlspecialchars($dc['stat_schedule']) ?></div>
        </div>
    </div>

    <!-- Benefits -->
    <div class="dc-section">
        <h2 class="dc-section-title"><?= htmlspecialchars($dc['benefits_title']) ?></h2>
        <p class="dc-section-sub"><?= htmlspecialchars($dc['benefits_sub']) ?></p>
        <div class="dc-benefits-grid">
            <div class="dc-benefit-card">
                <div class="dc-benefit-icon" style="background:#dcfce7;color:#15803d;">
                    <i class="fas fa-clock"></i>
                </div>
                <h3><?= htmlspecialchars($dc['b1_title']) ?></h3>
                <p><?= htmlspecialchars($dc['b1_text']) ?></p>
            </div>
            <div class="dc-benefit-card">
                <div class="dc-benefit-icon" style="background:#dcfce7;color:#15803d;">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3><?= htmlspecialchars($dc['b2_title']) ?></h3>
                <p><?= htmlspecialchars($dc['b2_text']) ?></p>
            </div>
            <div class="dc-benefit-card">
                <div class="dc-benefit-icon" style="background:#fef3c7;color:#b45309;">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3><?= htmlspecialchars($dc['b3_title']) ?></h3>
                <p><?= htmlspecialchars($dc['b3_text']) ?></p>
            </div>
            <div class="dc-benefit-card">
                <div class="dc-benefit-icon" style="background:#f3e8ff;color:#7c3aed;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3><?= htmlspecialchars($dc['b4_title']) ?></h3>
                <p><?= htmlspecialchars($dc['b4_text']) ?></p>
            </div>
            <div class="dc-benefit-card">
                <div class="dc-benefit-icon" style="background:#ffe4e6;color:#dc2626;">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3><?= htmlspecialchars($dc['b5_title']) ?></h3>
                <p><?= htmlspecialchars($dc['b5_text']) ?></p>
            </div>
            <div class="dc-benefit-card">
                <div class="dc-benefit-icon" style="background:#d1fae5;color:#065f46;">
                    <i class="fas fa-headset"></i>
                </div>
                <h3><?= htmlspecialchars($dc['b6_title']) ?></h3>
                <p><?= htmlspecialchars($dc['b6_text']) ?></p>
            </div>
        </div>
    </div>

    <!-- Delivery types -->
    <div class="dc-alt-bg">
        <div class="dc-section">
            <h2 class="dc-section-title"><?= htmlspecialchars($dc['types_title']) ?></h2>
            <p class="dc-section-sub"><?= htmlspecialchars($dc['types_sub']) ?></p>
            <div class="dc-types">
                <div class="dc-type-card" style="background:linear-gradient(135deg,#166534,#16a34a);">
                    <span class="dc-type-icon">📦</span>
                    <h3><?= htmlspecialchars($dc['t1_title']) ?></h3>
                    <p><?= htmlspecialchars($dc['t1_text']) ?></p>
                </div>
                <div class="dc-type-card" style="background:linear-gradient(135deg,#065f46,#10b981);">
                    <span class="dc-type-icon">🏢</span>
                    <h3><?= htmlspecialchars($dc['t2_title']) ?></h3>
                    <p><?= htmlspecialchars($dc['t2_text']) ?></p>
                </div>
                <div class="dc-type-card" style="background:linear-gradient(135deg,#92400e,#f59e0b);">
                    <span class="dc-type-icon">🏭</span>
                    <h3><?= htmlspecialchars($dc['t3_title']) ?></h3>
                    <p><?= htmlspecialchars($dc['t3_text']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- How it works -->
    <div class="dc-section">
        <h2 class="dc-section-title"><?= htmlspecialchars($dc['how_title']) ?></h2>
        <p class="dc-section-sub"><?= htmlspecialchars($dc['how_sub']) ?></p>
        <div class="dc-steps">
            <div class="dc-step">
                <div class="dc-step-num">1</div>
                <h3><?= htmlspecialchars($dc['s1_title']) ?></h3>
                <p><?= htmlspecialchars($dc['s1_text']) ?></p>
            </div>
            <div class="dc-step">
                <div class="dc-step-num">2</div>
                <h3><?= htmlspecialchars($dc['s2_title']) ?></h3>
                <p><?= htmlspecialchars($dc['s2_text']) ?></p>
            </div>
            <div class="dc-step">
                <div class="dc-step-num">3</div>
                <h3><?= htmlspecialchars($dc['s3_title']) ?></h3>
                <p><?= htmlspecialchars($dc['s3_text']) ?></p>
            </div>
            <div class="dc-step">
                <div class="dc-step-num">4</div>
                <h3><?= htmlspecialchars($dc['s4_title']) ?></h3>
                <p><?= htmlspecialchars($dc['s4_text']) ?></p>
            </div>
        </div>
    </div>

    <!-- Requirements -->
    <div class="dc-alt-bg">
        <div class="dc-section">
            <h2 class="dc-section-title"><?= htmlspecialchars($dc['req_title']) ?></h2>
            <p class="dc-section-sub"><?= htmlspecialchars($dc['req_sub']) ?></p>
            <div class="dc-reqs">
                <div class="dc-req">
                    <div class="dc-req-check"><i class="fas fa-check"></i></div>
                    <div>
                        <h4><?= htmlspecialchars($dc['r1_title']) ?></h4>
                        <p><?= htmlspecialchars($dc['r1_text']) ?></p>
                    </div>
                </div>
                <div class="dc-req">
                    <div class="dc-req-check"><i class="fas fa-check"></i></div>
                    <div>
                        <h4><?= htmlspecialchars($dc['r2_title']) ?></h4>
                        <p><?= htmlspecialchars($dc['r2_text']) ?></p>
                    </div>
                </div>
                <div class="dc-req">
                    <div class="dc-req-check"><i class="fas fa-check"></i></div>
                    <div>
                        <h4><?= htmlspecialchars($dc['r3_title']) ?></h4>
                        <p><?= htmlspecialchars($dc['r3_text']) ?></p>
                    </div>
                </div>
                <div class="dc-req">
                    <div class="dc-req-check"><i class="fas fa-check"></i></div>
                    <div>
                        <h4><?= htmlspecialchars($dc['r4_title']) ?></h4>
                        <p><?= htmlspecialchars($dc['r4_text']) ?></p>
                    </div>
                </div>
                <div class="dc-req">
                    <div class="dc-req-check"><i class="fas fa-check"></i></div>
                    <div>
                        <h4><?= htmlspecialchars($dc['r5_title']) ?></h4>
                        <p><?= htmlspecialchars($dc['r5_text']) ?></p>
                    </div>
                </div>
                <div class="dc-req">
                    <div class="dc-req-check"><i class="fas fa-check"></i></div>
                    <div>
                        <h4><?= htmlspecialchars($dc['r6_title']) ?></h4>
                        <p><?= htmlspecialchars($dc['r6_text']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom CTA -->
    <section class="dc-cta-section">
        <h2><?= htmlspecialchars($dc['cta_title']) ?></h2>
        <p><?= htmlspecialchars($dc['cta_sub']) ?></p>
        <div class="dc-hero-btns">
            <a href="<?= url('delivery/apply') ?>" class="dc-btn-primary">
                <i class="fas fa-rocket"></i> <?= htmlspecialchars($dc['cta_apply']) ?>
            </a>
            <?php if (function_exists('isLoggedIn') && isLoggedIn() && hasRole('delivery')): ?>
                <a href="<?= url('delivery/dashboard') ?>" class="dc-btn-secondary">
                    <i class="fas fa-tachometer-alt"></i> <?= htmlspecialchars($dc['cta_dashboard']) ?>
                </a>
            <?php else: ?>
                <a href="<?= url('delivery/login') ?>" class="dc-btn-secondary">
                    <i class="fas fa-sign-in-alt"></i> <?= htmlspecialchars($dc['cta_login']) ?>
                </a>
            <?php endif; ?>
        </div>
    </section>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
