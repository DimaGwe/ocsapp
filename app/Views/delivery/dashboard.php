<?php $currentPage = 'dashboard'; include __DIR__ . '/layout-header.php'; ?>

<style>
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 8px 0;
    }

    .page-header p {
        color: #6b7280;
        margin: 0;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.blue { background: linear-gradient(135deg, #00b207 0%, #008505 100%); }
    .stat-icon.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-icon.green { background: linear-gradient(135deg, #00b207 0%, #00b207 100%); }
    .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 4px;
        font-weight: 500;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
    }

    /* Section Styles */
    .section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 24px;
        margin-bottom: 30px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f3f4f6;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    /* Delivery Cards */
    .deliveries-grid {
        display: grid;
        gap: 20px;
    }

    .delivery-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        background: #fafafa;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .delivery-card:hover {
        border-color: #00b207;
        box-shadow: 0 4px 12px rgba(0, 178, 7, 0.1);
    }

    .delivery-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .delivery-order-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .order-number {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge.b2c { background: #d1fae5; color: #065f46; }
    .badge.b2b { background: #dbeafe; color: #1e40af; }
    .badge.assigned { background: #fef3c7; color: #92400e; }
    .badge.accepted { background: #dbeafe; color: #1e40af; }
    .badge.picked_up { background: #fed7aa; color: #9a3412; }
    .badge.on_the_way { background: #e0e7ff; color: #3730a3; }

    .delivery-details {
        display: grid;
        gap: 12px;
        margin-bottom: 16px;
    }

    .detail-row {
        display: flex;
        align-items: start;
        gap: 10px;
        font-size: 14px;
        color: #4b5563;
    }

    .detail-row i {
        color: #9ca3af;
        margin-top: 2px;
        width: 16px;
        flex-shrink: 0;
    }

    .detail-label {
        font-weight: 600;
        color: #1f2937;
        min-width: 80px;
    }

    .delivery-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    /* Buttons */
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-success {
        background: #00b207;
        color: white;
    }

    .btn-success:hover:not(:disabled) {
        background: #009206;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 178, 7, 0.3);
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover:not(:disabled) {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }

    .btn-primary {
        background: #00b207;
        color: white;
    }

    .btn-primary:hover:not(:disabled) {
        background: #009206;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 178, 7, 0.3);
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover:not(:disabled) {
        background: #d97706;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
    }

    /* Recent Deliveries List */
    .recent-list {
        display: grid;
        gap: 12px;
    }

    .recent-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: #f9fafb;
        border-radius: 8px;
        border-left: 4px solid #00b207;
    }

    .recent-item-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .recent-item-icon {
        width: 40px;
        height: 40px;
        background: #d1fae5;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #00b207;
        font-size: 18px;
    }

    .recent-item-details h4 {
        font-size: 15px;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 4px 0;
    }

    .recent-item-details p {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }

    .recent-item-amount {
        font-size: 16px;
        font-weight: 700;
        color: #00b207;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: #f3f4f6;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: #9ca3af;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 20px;
        font-weight: 600;
        color: #4b5563;
        margin: 0 0 8px 0;
    }

    .empty-state p {
        font-size: 14px;
        color: #9ca3af;
        margin: 0;
    }

    /* Loading State */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .stat-card { padding: 16px; }
        .stat-icon { width: 48px; height: 48px; font-size: 20px; }
        .stat-value { font-size: 22px; }
        .section { padding: 16px; }
        .delivery-card { padding: 16px; }
        .delivery-header { flex-direction: column; }
        .delivery-actions { flex-direction: column; }
        .btn { width: 100%; justify-content: center; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .stat-card { padding: 12px; gap: 10px; }
        .stat-icon { width: 40px; height: 40px; font-size: 18px; flex-shrink: 0; }
        .stat-value { font-size: 20px; }
        .stat-label { font-size: 12px; }
        .section { padding: 12px; }
        .section-title { font-size: 16px; }
        .page-header h1 { font-size: 22px; }
        .recent-item { flex-direction: column; align-items: flex-start; gap: 6px; }
        .recent-item-amount { font-size: 15px; }
    }
</style>

<?php
$_driverIsActive = ($_SESSION['user']['status'] ?? '') === 'active';
$_appStatusLabels = [
    'pending'              => ['en' => 'Application Received',  'fr' => 'Candidature reçue'],
    'under_review'         => ['en' => 'Under Review',          'fr' => 'En cours d\'examen'],
    'interview_requested'  => ['en' => 'Interview Requested',   'fr' => 'Entretien demandé'],
    'interview_scheduled'  => ['en' => 'Interview Scheduled',   'fr' => 'Entretien planifié'],
];
$_statusLabel = $_appStatusLabels[$applicationStatus ?? 'pending'] ?? $_appStatusLabels['pending'];
?>

<?php if (!$_driverIsActive): ?>
<style>
    .pending-hero {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 36px 40px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 28px;
    }
    .pending-hero-icon {
        width: 72px; height: 72px; border-radius: 50%;
        background: #bbf7d0; color: #15803d;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; flex-shrink: 0;
    }
    .pending-hero h1 { font-size: 22px; font-weight: 700; margin-bottom: 8px; color: #14532d; }
    .pending-hero p  { font-size: 14px; line-height: 1.6; margin: 0; color: #166534; }
    .pending-status-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: #bbf7d0; color: #15803d; padding: 5px 14px;
        border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 14px;
    }
    .pending-status-pill i { font-size: 10px; animation: drv-pulse 1.5s infinite; }
    @keyframes drv-pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    .pending-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;
    }
    @media (max-width: 768px) {
        .pending-grid { grid-template-columns: 1fr; }
        .pending-hero { flex-direction: column; text-align: center; padding: 28px 24px; }
    }
    .steps-card, .info-card {
        background: white; border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07); padding: 24px;
    }
    .steps-card h3, .info-card h3 {
        font-size: 15px; font-weight: 700; color: #111827;
        margin: 0 0 20px; display: flex; align-items: center; gap: 8px;
    }
    .steps-card h3 i, .info-card h3 i { color: #00b207; }
    .step-item { display: flex; gap: 14px; margin-bottom: 16px; }
    .step-item:last-child { margin-bottom: 0; }
    .step-number {
        width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700;
    }
    .step-number.done  { background: #d1fae5; color: #059669; }
    .step-number.todo  { background: #f3f4f6; color: #9ca3af; }
    .step-body .step-title { font-size: 14px; font-weight: 600; color: #111827; }
    .step-body .step-desc  { font-size: 13px; color: #6b7280; margin-top: 2px; line-height: 1.4; }
    .info-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-key { color: #6b7280; }
    .info-val { font-weight: 600; color: #111827; }
    .status-pill-sm {
        display: inline-flex; align-items: center; gap: 5px;
        background: #d1fae5; color: #059669;
        padding: 2px 10px; border-radius: 12px; font-size: 12px;
    }
    .contact-strip {
        background: white; border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        padding: 20px 24px;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    }
    .contact-strip p { margin: 0; font-size: 14px; color: #6b7280; }
    .contact-strip a { font-weight: 600; color: #00b207; text-decoration: none; }
    .contact-strip a:hover { text-decoration: underline; }
</style>

<!-- Pending Hero -->
<div class="pending-hero">
    <div class="pending-hero-icon"><i class="fas fa-hourglass-half"></i></div>
    <div style="flex:1;">
        <h1><?= $fr ? 'Bienvenue, ' : 'Welcome, ' ?><?= htmlspecialchars(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?>!</h1>
        <p>
            <?= $fr
                ? 'Votre candidature est en cours d\'examen par notre équipe. Complétez votre formation et téléversez vos documents pour accélérer l\'approbation - vous recevrez un courriel une fois approuvé.'
                : 'Your application is being reviewed by our team. Complete your training and upload your documents to speed up approval - you\'ll receive an email once approved.' ?>
        </p>
        <div class="pending-status-pill">
            <i class="fas fa-circle"></i>
            <?= $fr ? $_statusLabel['fr'] : $_statusLabel['en'] ?>
        </div>
    </div>
</div>

<!-- Two-column: Steps + Info -->
<div class="pending-grid">
    <div class="steps-card">
        <h3><i class="fas fa-list-check"></i> <?= $fr ? 'Ce qui se passe ensuite' : 'What Happens Next' ?></h3>
        <div class="step-item">
            <div class="step-number done"><i class="fas fa-check"></i></div>
            <div class="step-body">
                <div class="step-title"><?= $fr ? 'Candidature soumise' : 'Application Submitted' ?></div>
                <div class="step-desc"><?= $fr ? 'Votre candidature a été reçue avec succès.' : 'Your application has been successfully received.' ?></div>
            </div>
        </div>
        <div class="step-item">
            <div class="step-number <?= in_array($applicationStatus ?? '', ['under_review','interview_requested','interview_scheduled']) ? 'done' : 'todo' ?>">
                <?= in_array($applicationStatus ?? '', ['under_review','interview_requested','interview_scheduled']) ? '<i class="fas fa-check"></i>' : '2' ?>
            </div>
            <div class="step-body">
                <div class="step-title"><?= $fr ? 'Examen de la candidature' : 'Application Review' ?></div>
                <div class="step-desc"><?= $fr ? 'Notre équipe examine votre dossier.' : 'Our team reviews your application.' ?></div>
            </div>
        </div>
        <div class="step-item">
            <div class="step-number <?= in_array($applicationStatus ?? '', ['interview_requested','interview_scheduled']) ? 'done' : 'todo' ?>">
                <?= in_array($applicationStatus ?? '', ['interview_requested','interview_scheduled']) ? '<i class="fas fa-check"></i>' : '3' ?>
            </div>
            <div class="step-body">
                <div class="step-title"><?= $fr ? 'Entretien' : 'Interview' ?></div>
                <div class="step-desc"><?= $fr ? 'Un entretien rapide pour vous connaître.' : 'A quick interview to get to know you.' ?></div>
            </div>
        </div>
        <div class="step-item">
            <div class="step-number todo">4</div>
            <div class="step-body">
                <div class="step-title"><?= $fr ? 'Accès complet débloqué' : 'Full Access Unlocked' ?></div>
                <div class="step-desc"><?= $fr ? 'Acceptez des livraisons et commencez à gagner.' : 'Accept deliveries and start earning.' ?></div>
            </div>
        </div>
    </div>

    <div class="info-card">
        <h3><i class="fas fa-id-card"></i> <?= $fr ? 'Votre candidature' : 'Your Application' ?></h3>
        <div class="info-row">
            <span class="info-key"><?= $fr ? 'Nom' : 'Name' ?></span>
            <span class="info-val"><?= htmlspecialchars(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?></span>
        </div>
        <div class="info-row">
            <span class="info-key"><?= $fr ? 'Courriel' : 'Email' ?></span>
            <span class="info-val"><?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?></span>
        </div>
        <?php if (!empty($applicationSubmittedAt)): ?>
        <div class="info-row">
            <span class="info-key"><?= $fr ? 'Soumise le' : 'Submitted' ?></span>
            <span class="info-val"><?= date($fr ? 'j M Y' : 'M j, Y', strtotime($applicationSubmittedAt)) ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-key"><?= $fr ? 'Statut' : 'Status' ?></span>
            <span class="info-val">
                <span class="status-pill-sm"><i class="fas fa-clock"></i> <?= $fr ? $_statusLabel['fr'] : $_statusLabel['en'] ?></span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-key"><?= $fr ? 'Prochaine étape' : 'Next Step' ?></span>
            <span class="info-val" style="text-align:right;max-width:55%;">
                <?php
                if ($fr) {
                    echo match($applicationStatus ?? 'pending') {
                        'pending'             => 'Compléter votre formation',
                        'under_review'        => 'Attendre la confirmation d\'entretien',
                        'interview_requested' => 'Choisir un créneau d\'entretien',
                        'interview_scheduled' => 'Préparer votre entretien',
                        default               => 'Attendre la décision de l\'équipe',
                    };
                } else {
                    echo match($applicationStatus ?? 'pending') {
                        'pending'             => 'Complete your training',
                        'under_review'        => 'Await interview confirmation',
                        'interview_requested' => 'Select an interview slot',
                        'interview_scheduled' => 'Prepare for your interview',
                        default               => 'Await team decision',
                    };
                }
                ?>
            </span>
        </div>
    </div>
</div>

<!-- Contact Strip -->
<div class="contact-strip" style="margin-bottom: 28px;">
    <p><?= $fr ? 'Des questions sur votre candidature ?' : 'Questions about your application?' ?></p>
    <div style="display:flex;gap:16px;flex-wrap:wrap;">
        <a href="<?= url('delivery/messages') ?>"><i class="fas fa-comments" style="margin-right:5px;"></i>Messages</a>
        <a href="mailto:info@ocsapp.ca"><i class="fas fa-envelope" style="margin-right:5px;"></i>info@ocsapp.ca</a>
    </div>
</div>

<?php else: // approved driver — show full dashboard ?>

<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1><?= $fr ? 'Tableau de bord' : 'Delivery Dashboard' ?></h1>
        <p><?= $fr ? 'Gérez vos livraisons et suivez vos revenus' : 'Manage your deliveries and track your earnings' ?></p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label"><?= $fr ? 'Total livraisons' : 'Total Deliveries' ?></div>
                <div class="stat-value"><?= number_format($stats['total_deliveries']) ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-truck"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label"><?= $fr ? 'En transit' : 'In Transit' ?></div>
                <div class="stat-value"><?= number_format($stats['in_transit']) ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label"><?= $fr ? 'Livrées' : 'Delivered' ?></div>
                <div class="stat-value"><?= number_format($stats['delivered']) ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label"><?= $fr ? 'Revenus totaux' : 'Total Earnings' ?></div>
                <div class="stat-value">$<?= number_format($stats['total_earnings'], 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Vehicle Info (if assigned) -->
    <?php if (!empty($assignedVehicle)): ?>
    <div style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1px solid #86efac; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
        <div style="font-size: 36px;">
            <?php
            $vIcons = ['bicycle' => '🚲', 'e-bike' => '⚡', 'scooter' => '🛵', 'motorcycle' => '🏍️', 'car' => '🚗', 'van' => '🚐'];
            echo $vIcons[$assignedVehicle['vehicle_type'] ?? 'car'] ?? '🚗';
            ?>
        </div>
        <div style="flex:1;">
            <div style="font-weight: 700; color: #166534; font-size: 16px;">
                <?= htmlspecialchars(trim(($assignedVehicle['make'] ?? '') . ' ' . ($assignedVehicle['model'] ?? '') . ' ' . ($assignedVehicle['year'] ?? ''))) ?: ucfirst($assignedVehicle['vehicle_type'] ?? 'Vehicle') ?>
            </div>
            <?php if (!empty($assignedVehicle['plate_number'])): ?>
                <div style="color: #00b207; font-size: 14px; margin-top: 2px;"><?= $fr ? 'Plaque:' : 'Plate:' ?> <?= htmlspecialchars($assignedVehicle['plate_number']) ?></div>
            <?php endif; ?>
        </div>
        <?php if (!empty($assignedVehicle['insurance_expiry'])): ?>
            <?php $expiry = strtotime($assignedVehicle['insurance_expiry']); $daysLeft = (int)(($expiry - time()) / 86400); ?>
            <div style="font-size: 13px; padding: 6px 14px; border-radius: 8px; <?= $daysLeft < 30 ? 'background:#fef2f2;color:#991b1b;' : 'background:#f0fdf4;color:#166534;' ?>">
                <?= $fr ? 'Assurance:' : 'Insurance:' ?> <?= date('M j, Y', $expiry) ?>
                <?= $daysLeft < 30 ? ' ⚠️' : '' ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Active Deliveries Section -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title"><?= $fr ? 'Livraisons actives' : 'Active Deliveries' ?></h2>
            <?php if (!empty($activeDeliveries)): ?>
                <span class="badge b2c"><?= count($activeDeliveries) ?> <?= $fr ? 'Actives' : 'Active' ?></span>
            <?php endif; ?>
        </div>

        <?php if (empty($activeDeliveries)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3><?= $fr ? 'Aucune livraison active' : 'No Active Deliveries' ?></h3>
                <p><?= $fr ? 'Vous êtes à jour! Les nouvelles livraisons apparaîtront ici lorsqu\'elles vous seront assignées.' : "You're all caught up! New deliveries will appear here when assigned." ?></p>
            </div>
        <?php else: ?>
            <div class="deliveries-grid">
                <?php foreach ($activeDeliveries as $delivery): ?>
                    <div class="delivery-card" data-delivery-id="<?= $delivery['id'] ?>">
                        <div class="delivery-header">
                            <div class="delivery-order-info">
                                <span class="order-number">#<?= htmlspecialchars($delivery['order_number']) ?></span>
                                <span class="badge <?= strtolower($delivery['delivery_type']) ?>">
                                    <?= htmlspecialchars($delivery['delivery_type']) ?>
                                </span>
                                <span class="badge <?= strtolower($delivery['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $delivery['status'])) ?>
                                </span>
                            </div>
                        </div>

                        <div class="delivery-details">
                            <div class="detail-row">
                                <i class="fas fa-user"></i>
                                <div>
                                    <span class="detail-label"><?= $fr ? 'Client:' : 'Customer:' ?></span>
                                    <?= htmlspecialchars($delivery['customer_first_name'] . ' ' . $delivery['customer_last_name']) ?>
                                    <?php if (!empty($delivery['customer_phone'])): ?>
                                        <br>
                                        <small style="color: #6b7280;">
                                            <i class="fas fa-phone"></i> <?= htmlspecialchars($delivery['customer_phone']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="detail-row">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <span class="detail-label"><?= $fr ? 'Livraison à:' : 'Delivery To:' ?></span>
                                    <?= htmlspecialchars($delivery['delivery_address']) ?>
                                </div>
                            </div>

                            <div class="detail-row">
                                <i class="fas fa-store"></i>
                                <div>
                                    <span class="detail-label"><?= $fr ? 'Depuis:' : 'From Shop:' ?></span>
                                    <?= htmlspecialchars($delivery['shop_name']) ?>
                                </div>
                            </div>

                            <div class="detail-row">
                                <i class="fas fa-money-bill-wave"></i>
                                <div>
                                    <span class="detail-label"><?= $fr ? 'Total commande:' : 'Order Total:' ?></span>
                                    $<?= number_format($delivery['total'], 2) ?> CAD
                                </div>
                            </div>

                            <div class="detail-row">
                                <i class="fas fa-hand-holding-usd"></i>
                                <div>
                                    <span class="detail-label"><?= $fr ? 'Frais livraison:' : 'Delivery Fee:' ?></span>
                                    <strong style="color: #00b207;">$<?= number_format($delivery['delivery_fee'], 2) ?> CAD</strong>
                                </div>
                            </div>

                            <?php if (!empty($delivery['assigned_at'])): ?>
                                <div class="detail-row">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <span class="detail-label"><?= $fr ? 'Assignée:' : 'Assigned:' ?></span>
                                        <?= date('M j, Y g:i A', strtotime($delivery['assigned_at'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="delivery-actions">
                            <?php if ($delivery['status'] === 'assigned'): ?>
                                <button class="btn btn-success" onclick="acceptDelivery(<?= $delivery['id'] ?>)">
                                    <i class="fas fa-check"></i> <?= $fr ? 'Accepter' : 'Accept Delivery' ?>
                                </button>
                                <button class="btn btn-danger" onclick="rejectDelivery(<?= $delivery['id'] ?>)">
                                    <i class="fas fa-times"></i> <?= $fr ? 'Refuser' : 'Reject' ?>
                                </button>
                            <?php elseif ($delivery['status'] === 'accepted'): ?>
                                <button class="btn btn-primary" onclick="updateStatus(<?= $delivery['id'] ?>, 'picked_up')">
                                    <i class="fas fa-box-open"></i> <?= $fr ? 'Ramassé' : 'Mark Picked Up' ?>
                                </button>
                            <?php elseif ($delivery['status'] === 'picked_up'): ?>
                                <button class="btn btn-warning" onclick="updateStatus(<?= $delivery['id'] ?>, 'on_the_way')">
                                    <i class="fas fa-shipping-fast"></i> <?= $fr ? 'En route' : 'On the Way' ?>
                                </button>
                            <?php elseif ($delivery['status'] === 'on_the_way'): ?>
                                <button class="btn btn-success" onclick="updateStatus(<?= $delivery['id'] ?>, 'delivered')">
                                    <i class="fas fa-check-circle"></i> <?= $fr ? 'Livré' : 'Mark Delivered' ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Deliveries Section -->
    <?php if (!empty($recentDeliveries)): ?>
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><?= $fr ? 'Livraisons récentes' : 'Recent Deliveries' ?></h2>
            </div>

            <div class="recent-list">
                <?php foreach ($recentDeliveries as $recent): ?>
                    <div class="recent-item">
                        <div class="recent-item-info">
                            <div class="recent-item-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="recent-item-details">
                                <h4><?= $fr ? 'Commande n°' : 'Order #' ?><?= htmlspecialchars($recent['order_number']) ?></h4>
                                <p><?php
                                    $_ts = strtotime($recent['delivered_at']);
                                    $_frMo = ['','jan.','fév.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.'];
                                    echo ($fr ? 'Livrée le ' . date('j', $_ts) . ' ' . $_frMo[(int)date('n', $_ts)] . ' ' . date('Y', $_ts) . ' à ' . date('G', $_ts) . 'h' . date('i', $_ts)
                                             : 'Delivered on ' . date('M j, Y g:i A', $_ts));
                                ?></p>
                            </div>
                        </div>
                        <div class="recent-item-amount">
                            +$<?= number_format($recent['delivery_fee'], 2) ?> CAD
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Messages Section -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-comments" style="color:#00b207; margin-right:8px;"></i>
                Messages
                <?php if (!empty($unreadCount)): ?>
                    <span style="background:#ef4444; color:#fff; font-size:12px; font-weight:700; padding:2px 8px; border-radius:20px; margin-left:8px;"><?= $unreadCount ?> <?= $fr ? 'nouveau(x)' : 'new' ?></span>
                <?php endif; ?>
            </h2>
            <?php if (!empty($messages)): ?>
            <a href="<?= url('delivery/application-status') ?>" style="font-size:13px; color:#00b207; text-decoration:none;">
                <?= $fr ? 'Voir tout' : 'View all' ?> <i class="fas fa-arrow-right"></i>
            </a>
            <?php endif; ?>
        </div>

        <!-- Message thread (newest first, show last 5) -->
        <?php if (empty($messages)): ?>
            <div style="text-align:center; padding:24px 20px; color:#9ca3af;">
                <i class="fas fa-comments" style="font-size:2rem; display:block; margin-bottom:10px;"></i>
                <?= $fr ? 'Aucun message pour l\'instant. Une question? Envoyez-nous un message ci-dessous!' : 'No messages yet. Have a question? Send us a message below!' ?>
            </div>
        <?php else: ?>
            <div id="dashMsgThread" style="display:flex; flex-direction:column; gap:8px; margin-bottom:16px; max-height:320px; overflow-y:auto; padding-right:4px;">
                <?php foreach (array_reverse(array_slice($messages, 0, 6)) as $msg): ?>
                    <?php $isAdmin = $msg['sender_type'] === 'admin'; ?>
                    <div style="display:flex; gap:10px; align-items:flex-start;">
                        <div style="width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:13px;
                            background:<?= $isAdmin ? '#dbeafe' : '#d1fae5' ?>; color:<?= $isAdmin ? '#1d4ed8' : '#065f46' ?>;">
                            <i class="fas <?= $isAdmin ? 'fa-headset' : 'fa-user' ?>"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; gap:8px; align-items:baseline; margin-bottom:3px;">
                                <span style="font-weight:700; font-size:13px; color:<?= $isAdmin ? '#1d4ed8' : '#065f46' ?>;">
                                    <?= $isAdmin ? ($fr ? 'Équipe OCSAPP' : 'OCSAPP Team') : ($fr ? 'Vous' : 'You') ?>
                                </span>
                                <span style="font-size:11px; color:#9ca3af;"><?php
                                    $_ts = strtotime($msg['created_at']);
                                    $_frMo = ['','jan.','fév.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.'];
                                    echo $fr ? date('j', $_ts) . ' ' . $_frMo[(int)date('n', $_ts)] . ', ' . date('G', $_ts) . 'h' . date('i', $_ts)
                                             : date('M j, g:i A', $_ts);
                                ?></span>
                            </div>
                            <div style="font-size:13px; color:#374151; line-height:1.55; background:<?= $isAdmin ? '#eff6ff' : '#f0fdf4' ?>; border-radius:0 10px 10px 10px; padding:10px 14px;">
                                <?= nl2br(htmlspecialchars($fr && !empty($msg['message_fr']) ? $msg['message_fr'] : $msg['message'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($messages) > 6): ?>
                <div style="text-align:center; margin-bottom:12px;">
                    <a href="<?= url('delivery/application-status') ?>" style="font-size:12px; color:#6b7280; text-decoration:none;">
                        <i class="fas fa-clock"></i> <?= count($messages) - 6 ?> <?= $fr ? 'anciens messages -' : 'older messages -' ?> <u><?= $fr ? 'voir le fil' : 'view full thread' ?></u>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Send message form -->
        <?php if ($applicationId): ?>
        <form id="dashMsgForm" onsubmit="sendDashboardMessage(event)" style="display:flex; gap:8px; align-items:flex-end; margin-top:4px;">
            <input type="hidden" name="application_id" value="<?= $applicationId ?>">
            <textarea id="dashMsgInput" name="message" placeholder="<?= $fr ? 'Posez une question ou envoyez un message à l\'assistance OCSAPP…' : 'Ask a question or send a message to OCSAPP Support…' ?>"
                style="flex:1; resize:none; border:1px solid #d1d5db; border-radius:10px; padding:10px 14px; font-size:13px; font-family:inherit; outline:none; min-height:44px; max-height:120px; overflow-y:auto; transition:border-color .2s;"
                rows="1"
                onfocus="this.style.borderColor='#00b207'"
                onblur="this.style.borderColor='#d1d5db'"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendDashboardMessage(event);}"></textarea>
            <button type="submit" id="dashMsgBtn" style="background:#00b207; color:#fff; border:none; border-radius:10px; padding:10px 18px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px; height:44px; white-space:nowrap; flex-shrink:0; transition:background .2s;"
                onmouseover="this.style.background='#009206'" onmouseout="this.style.background='#00b207'">
                <i class="fas fa-paper-plane"></i> <?= $fr ? 'Envoyer' : 'Send' ?>
            </button>
        </form>
        <p style="font-size:11px; color:#9ca3af; margin:6px 0 0;"><?= $fr ? 'Entrée pour envoyer · Maj+Entrée pour nouvelle ligne' : 'Press Enter to send · Shift+Enter for new line' ?></p>
        <?php else: ?>
        <p style="font-size:13px; color:#9ca3af; text-align:center; margin-top:8px;">
            <i class="fas fa-info-circle"></i> <?= $fr ? 'Soumettez une demande de livreur pour activer la messagerie.' : 'Submit a driver application to enable messaging.' ?>
        </p>
        <?php endif; ?>
    </div>
</div>

<script>
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const i18n = {
        confirmAccept:   '<?= $fr ? 'Accepter cette livraison?' : 'Accept this delivery?' ?>',
        acceptedOk:      '<?= $fr ? 'Livraison acceptée!' : 'Delivery accepted successfully!' ?>',
        acceptFail:      '<?= $fr ? 'Échec de l\'acceptation' : 'Failed to accept delivery' ?>',
        rejectPrompt:    '<?= $fr ? 'Raison du refus:' : 'Please provide a reason for rejecting this delivery:' ?>',
        rejectRequired:  '<?= $fr ? 'La raison du refus est requise' : 'Rejection reason is required' ?>',
        rejectedOk:      '<?= $fr ? 'Livraison refusée' : 'Delivery rejected' ?>',
        rejectFail:      '<?= $fr ? 'Échec du refus' : 'Failed to reject delivery' ?>',
        confirmPickup:   '<?= $fr ? 'Marquer comme ramassée?' : 'Mark this delivery as picked up?' ?>',
        confirmOnWay:    '<?= $fr ? 'Marquer comme en route?' : 'Mark this delivery as on the way?' ?>',
        confirmDelivered:'<?= $fr ? 'Marquer comme livrée?' : 'Mark this delivery as delivered?' ?>',
        confirmStatus:   '<?= $fr ? 'Mettre à jour le statut?' : 'Update delivery status?' ?>',
        statusOk:        '<?= $fr ? 'Statut mis à jour!' : 'Status updated successfully!' ?>',
        statusFail:      '<?= $fr ? 'Échec de la mise à jour' : 'Failed to update status' ?>',
        accepting:       '<?= $fr ? 'En cours…' : 'Accepting...' ?>',
        rejecting:       '<?= $fr ? 'Refus en cours…' : 'Rejecting...' ?>',
        updating:        '<?= $fr ? 'Mise à jour…' : 'Updating...' ?>',
        error:           '<?= $fr ? 'Une erreur s\'est produite. Veuillez réessayer.' : 'An error occurred. Please try again.' ?>',
        sendBtn:         '<?= $fr ? '<i class="fas fa-paper-plane"></i> Envoyer' : '<i class="fas fa-paper-plane"></i> Send' ?>',
        acceptBtn:       '<?= $fr ? '<i class="fas fa-check"></i> Accepter' : '<i class="fas fa-check"></i> Accept Delivery' ?>',
        rejectBtn:       '<?= $fr ? '<i class="fas fa-times"></i> Refuser' : '<i class="fas fa-times"></i> Reject' ?>',
        msgYou:          '<?= $fr ? 'Vous' : 'You' ?>',
    };

    async function acceptDelivery(deliveryId) {
        const button = event.target.closest('button');
        if (!button) return;

        if (!confirm(i18n.confirmAccept)) return;

        button.disabled = true;
        button.innerHTML = '<span class="spinner"></span> ' + i18n.accepting;

        try {
            const response = await fetch('/delivery/accept', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({ delivery_id: deliveryId })
            });

            const data = await response.json();

            if (data.success) {
                alert(i18n.acceptedOk);
                window.location.reload();
            } else {
                alert(data.message || i18n.acceptFail);
                button.disabled = false;
                button.innerHTML = i18n.acceptBtn;
            }
        } catch (error) {
            console.error('Error accepting delivery:', error);
            alert(i18n.error);
            button.disabled = false;
            button.innerHTML = i18n.acceptBtn;
        }
    }

    async function rejectDelivery(deliveryId) {
        const reason = prompt(i18n.rejectPrompt);

        if (!reason || reason.trim() === '') {
            alert(i18n.rejectRequired);
            return;
        }

        const button = event.target.closest('button');
        if (!button) return;

        button.disabled = true;
        button.innerHTML = '<span class="spinner"></span> ' + i18n.rejecting;

        try {
            const response = await fetch('/delivery/reject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({
                    delivery_id: deliveryId,
                    reason: reason.trim()
                })
            });

            const data = await response.json();

            if (data.success) {
                alert(i18n.rejectedOk);
                window.location.reload();
            } else {
                alert(data.message || i18n.rejectFail);
                button.disabled = false;
                button.innerHTML = i18n.rejectBtn;
            }
        } catch (error) {
            console.error('Error rejecting delivery:', error);
            alert(i18n.error);
            button.disabled = false;
            button.innerHTML = i18n.rejectBtn;
        }
    }

    async function updateStatus(deliveryId, newStatus) {
        const button = event.target.closest('button');
        if (!button) return;

        const statusMessages = {
            'picked_up': i18n.confirmPickup,
            'on_the_way': i18n.confirmOnWay,
            'delivered': i18n.confirmDelivered
        };

        if (!confirm(statusMessages[newStatus] || i18n.confirmStatus)) return;

        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner"></span> ' + i18n.updating;

        try {
            const response = await fetch('/delivery/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({
                    delivery_id: deliveryId,
                    status: newStatus
                })
            });

            const data = await response.json();

            if (data.success) {
                alert(i18n.statusOk);
                window.location.reload();
            } else {
                alert(data.message || i18n.statusFail);
                button.disabled = false;
                button.innerHTML = originalHTML;
            }
        } catch (error) {
            console.error('Error updating status:', error);
            alert(i18n.error);
            button.disabled = false;
            button.innerHTML = originalHTML;
        }
    }

    async function sendDashboardMessage(e) {
        e.preventDefault();
        const form = document.getElementById('dashMsgForm');
        const input = document.getElementById('dashMsgInput');
        const btn = document.getElementById('dashMsgBtn');
        const msg = input.value.trim();
        if (!msg) return;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span>';

        const formData = new FormData(form);
        formData.set('message', msg);
        formData.set(document.querySelector('meta[name="csrf-param"]')?.content || '_csrf_token', csrfToken);

        try {
            const res = await fetch('<?= url('delivery/send-application-message') ?>', {
                method: 'POST',
                headers: { 'X-CSRF-Token': csrfToken },
                body: formData
            });
            const data = await res.json();
            if (data.success || res.ok) {
                // Append message bubble instantly
                const thread = document.getElementById('dashMsgThread');
                if (thread) {
                    const now = new Date();
                    const timeStr = now.toLocaleString('<?= $fr ? 'fr-CA' : 'en-CA' ?>', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
                    const bubble = document.createElement('div');
                    bubble.style.cssText = 'display:flex;gap:10px;align-items:flex-start;';
                    bubble.innerHTML = `
                        <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;background:#d1fae5;color:#065f46;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;gap:8px;align-items:baseline;margin-bottom:3px;">
                                <span style="font-weight:700;font-size:13px;color:#065f46;">${i18n.msgYou}</span>
                                <span style="font-size:11px;color:#9ca3af;">${timeStr}</span>
                            </div>
                            <div style="font-size:13px;color:#374151;line-height:1.55;background:#f0fdf4;border-radius:0 10px 10px 10px;padding:10px 14px;">${msg.replace(/\n/g,'<br>')}</div>
                        </div>`;
                    thread.appendChild(bubble);
                    thread.scrollTop = thread.scrollHeight;
                } else {
                    // No thread shown yet (empty state) — reload to show it
                    window.location.reload();
                }
                input.value = '';
                input.style.height = 'auto';
            } else {
                alert(data.error || 'Failed to send message.');
            }
        } catch (err) {
            alert('An error occurred. Please try again.');
        }

        btn.disabled = false;
        btn.innerHTML = i18n.sendBtn;
    }

    // Auto-refresh every 30 seconds to check for new deliveries
    let autoRefreshInterval;

    function startAutoRefresh() {
        autoRefreshInterval = setInterval(() => {
            // Only refresh if user is not interacting with the page
            if (document.hidden === false) {
                window.location.reload();
            }
        }, 30000); // 30 seconds
    }

    // Start auto-refresh on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startAutoRefresh);
    } else {
        startAutoRefresh();
    }

    // Clear interval when page is hidden to save resources
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(autoRefreshInterval);
        } else {
            startAutoRefresh();
        }
    });
</script>

<?php endif; // end pending vs approved dashboard ?>

<?php include __DIR__ . '/layout-footer.php'; ?>
