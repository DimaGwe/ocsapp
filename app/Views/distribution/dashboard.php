<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'welcome'           => 'Welcome',
        'manage_account'    => 'Manage your procurement orders and account settings from your dashboard.',
        'tier_standard'     => 'Standard Account',
        'tier_approved'     => 'Approved Account',
        'tier_premium'      => 'Premium Account',
        'total_requests'    => 'Total Requests',
        'pending'           => 'Pending',
        'in_progress'       => 'In Progress',
        'completed'         => 'Completed',
        'procurement'       => 'Procurement',
        'new_request'       => 'New Request',
        'view_requests'     => 'View Requests',
        'drafts'            => 'Drafts',
        'invoices'          => 'Invoices',
        'distribution'      => 'Distribution (Send Out)',
        'new_shipment'      => 'New Shipment',
        'my_shipments'      => 'My Shipments',
        'recurring_routes'  => 'Recurring Routes',
        'track_shipment'    => 'Track Shipment',
        'recent_requests'   => 'Recent Requests',
        'view_all'          => 'View All',
        'no_requests'       => 'No requests yet',
        'no_requests_desc'  => 'When you create your first procurement request, it will appear here.',
        'create_request'    => 'Create Request',
        'col_request'       => 'Request',
        'col_status'        => 'Status',
        'col_total'         => 'Total',
        'col_date'          => 'Date',
        'col_actions'       => 'Actions',
        'pay'               => 'Pay',
        'cancel'            => 'Cancel',
        'view'              => 'View',
        'payment_required'  => 'Payment Required',
        'payment_ready'     => 'Request #%s is ready for payment ($%s)',
        'pay_now'           => 'Pay Now',
        'delivery_address'  => 'Delivery Address',
        'street_address'    => 'Street Address',
        'street_hint'       => 'Type to search — selecting a suggestion auto-fills all fields below',
        'city'              => 'City',
        'province'          => 'Province',
        'select_province'   => 'Select Province',
        'postal_code'       => 'Postal Code',
        'country'           => 'Country',
        'save_address'      => 'Save Address',
        'saving'            => 'Saving...',
        'account_info'      => 'Account Information',
        'company_name'      => 'Company Name',
        'contact_person'    => 'Contact Person',
        'email'             => 'Email',
        'phone'             => 'Phone',
        'account_tier'      => 'Account Tier',
        'member_since'      => 'Member Since',
        'cancel_request'    => 'Cancel Request',
        'cancel_confirm'    => 'Are you sure you want to cancel request',
        'reason_optional'   => 'Reason (optional)',
        'reason_placeholder'=> 'Why are you cancelling this request?',
        'keep_request'      => 'Keep Request',
        'invoices_soon'     => 'Coming soon! Invoices feature is under development.',
        'no_results'        => 'No results found — try adding the city name',
        'search_failed'     => 'Search failed — try again',
        'searching'         => 'Searching...',
        'error_occurred'    => 'An error occurred. Please try again.',
        'error_updating'    => 'Error updating address',
        'status_draft'      => 'Draft',
        'status_submitted'  => 'Submitted',
        'status_quoted'     => 'Quoted',
        'status_pending_payment' => 'Pending Payment',
        'status_paid'       => 'Paid',
        'status_in_transit' => 'In Transit',
        'status_delivered'  => 'Delivered',
        'status_completed'  => 'Completed',
        'status_cancelled'  => 'Cancelled',
    ],
    'fr' => [
        'welcome'           => 'Bienvenue',
        'manage_account'    => 'G&#233;rez vos commandes d\'approvisionnement et les param&#232;tres de votre compte depuis votre tableau de bord.',
        'tier_standard'     => 'Compte Standard',
        'tier_approved'     => 'Compte Approuv&#233;',
        'tier_premium'      => 'Compte Premium',
        'total_requests'    => 'Total des demandes',
        'pending'           => 'En attente',
        'in_progress'       => 'En cours',
        'completed'         => 'Compl&#233;t&#233;',
        'procurement'       => 'Approvisionnement',
        'new_request'       => 'Nouvelle demande',
        'view_requests'     => 'Voir les demandes',
        'drafts'            => 'Brouillons',
        'invoices'          => 'Factures',
        'distribution'      => 'Distribution (Exp&#233;dition)',
        'new_shipment'      => 'Nouvel envoi',
        'my_shipments'      => 'Mes envois',
        'recurring_routes'  => 'Routes r&#233;currentes',
        'track_shipment'    => 'Suivre un envoi',
        'recent_requests'   => 'Demandes r&#233;centes',
        'view_all'          => 'Voir tout',
        'no_requests'       => 'Aucune demande pour l\'instant',
        'no_requests_desc'  => 'Lorsque vous cr&#233;ez votre premi&#232;re demande d\'approvisionnement, elle appara&#238;tra ici.',
        'create_request'    => 'Cr&#233;er une demande',
        'col_request'       => 'Demande',
        'col_status'        => 'Statut',
        'col_total'         => 'Total',
        'col_date'          => 'Date',
        'col_actions'       => 'Actions',
        'pay'               => 'Payer',
        'cancel'            => 'Annuler',
        'view'              => 'Voir',
        'payment_required'  => 'Paiement requis',
        'payment_ready'     => 'La demande #%s est pr&#234;te pour le paiement ($%s)',
        'pay_now'           => 'Payer maintenant',
        'delivery_address'  => 'Adresse de livraison',
        'street_address'    => 'Adresse',
        'street_hint'       => 'Tapez pour rechercher - s&#233;lectionner une suggestion remplit automatiquement tous les champs ci-dessous',
        'city'              => 'Ville',
        'province'          => 'Province',
        'select_province'   => 'S&#233;lectionnez une province',
        'postal_code'       => 'Code postal',
        'country'           => 'Pays',
        'save_address'      => 'Enregistrer l\'adresse',
        'saving'            => 'Enregistrement...',
        'account_info'      => 'Informations du compte',
        'company_name'      => 'Nom de l\'entreprise',
        'contact_person'    => 'Personne contact',
        'email'             => 'Courriel',
        'phone'             => 'T&#233;l&#233;phone',
        'account_tier'      => 'Niveau de compte',
        'member_since'      => 'Membre depuis',
        'cancel_request'    => 'Annuler la demande',
        'cancel_confirm'    => 'Voulez-vous vraiment annuler la demande',
        'reason_optional'   => 'Raison (facultatif)',
        'reason_placeholder'=> 'Pourquoi annulez-vous cette demande\u00a0?',
        'keep_request'      => 'Garder la demande',
        'invoices_soon'     => 'Bient&#244;t disponible\u00a0! La fonctionnalit&#233; de facturation est en cours de d&#233;veloppement.',
        'no_results'        => 'Aucun r&#233;sultat &#8212; essayez d\'ajouter le nom de la ville',
        'search_failed'     => 'Recherche &#233;chou&#233;e &#8212; veuillez r&#233;essayer',
        'searching'         => 'Recherche...',
        'error_occurred'    => 'Une erreur s\'est produite. Veuillez r&#233;essayer.',
        'error_updating'    => 'Erreur lors de la mise &#224; jour de l\'adresse',
        'status_draft'      => 'Brouillon',
        'status_submitted'  => 'Soumis',
        'status_quoted'     => 'Cotation re&#231;ue',
        'status_pending_payment' => 'Paiement en attente',
        'status_paid'       => 'Pay&#233;',
        'status_in_transit' => 'En transit',
        'status_delivered'  => 'Livr&#233;',
        'status_completed'  => 'Compl&#233;t&#233;',
        'status_cancelled'  => 'Annul&#233;',
    ],
];
$t = $translations[$currentLang] ?? $translations['en'];

$statusLabels = [
    'draft'           => $t['status_draft'],
    'submitted'       => $t['status_submitted'],
    'quoted'          => $t['status_quoted'],
    'pending_payment' => $t['status_pending_payment'],
    'paid'            => $t['status_paid'],
    'in_transit'      => $t['status_in_transit'],
    'delivered'       => $t['status_delivered'],
    'completed'       => $t['status_completed'],
    'cancelled'       => $t['status_cancelled'],
];

$tierLabels = [
    'standard' => $t['tier_standard'],
    'approved' => $t['tier_approved'],
    'premium'  => $t['tier_premium'],
];
?>
<?php $dt = $t; include __DIR__ . '/layout-header.php'; $t = $dt; ?>

<?php if (($business['status'] ?? '') === 'pending'): ?>
<!-- ═══════════════════════════════════════════════════════ -->
<!--  PENDING ACCOUNT DASHBOARD                             -->
<!-- ═══════════════════════════════════════════════════════ -->
<style>
    /* Pending hero */
    .pending-hero {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 36px 40px;
        color: #14532d;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 28px;
    }
    .pending-hero-icon {
        width: 72px; height: 72px; border-radius: 50%;
        background: #bbf7d0;
        color: #15803d;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; flex-shrink: 0;
    }
    .pending-hero h1 { font-size: 22px; font-weight: 700; margin-bottom: 8px; color: #14532d; }
    .pending-hero p  { font-size: 14px; line-height: 1.6; margin: 0; color: #166534; }
    .pending-status-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: #bbf7d0; color: #15803d; padding: 5px 14px;
        border-radius: 20px; font-size: 12px; font-weight: 600;
        margin-top: 14px;
    }
    .pending-status-pill i { font-size: 10px; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

    /* Two-column layout */
    .pending-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    @media (max-width: 768px) {
        .pending-grid { grid-template-columns: 1fr; }
        .pending-hero { flex-direction: column; text-align: center; padding: 28px 24px; }
    }

    /* Steps card */
    .steps-card {
        background: white; border-radius: 14px;
        padding: 28px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .steps-card h3 {
        font-size: 15px; font-weight: 700; color: #111827;
        margin-bottom: 22px; display: flex; align-items: center; gap: 8px;
    }
    .steps-card h3 i { color: #00b207; }
    .step-item {
        display: flex; gap: 16px; margin-bottom: 20px;
    }
    .step-item:last-child { margin-bottom: 0; }
    .step-number {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; flex-shrink: 0;
    }
    .step-number.done  { background: #d1fae5; color: #059669; }
    .step-number.active{ background: #d1fae5; color: #059669; }
    .step-number.todo  { background: #f3f4f6; color: #9ca3af; }
    .step-body { flex: 1; }
    .step-title { font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 3px; }
    .step-desc  { font-size: 12px; color: #6b7280; line-height: 1.5; }
    .step-connector {
        width: 2px; height: 16px; background: #e5e7eb;
        margin: 4px 0 4px 15px;
    }

    /* Account info card */
    .info-card {
        background: white; border-radius: 14px;
        padding: 28px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .info-card h3 {
        font-size: 15px; font-weight: 700; color: #111827;
        margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
    }
    .info-card h3 i { color: #00b207; }
    .info-row {
        display: flex; justify-content: space-between; align-items: flex-start;
        padding: 12px 0; border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-key   { color: #6b7280; font-size: 13px; }
    .info-val   { font-weight: 600; color: #111827; text-align: right; max-width: 55%; word-break: break-word; }
    .info-val.status-pending {
        display: inline-flex; align-items: center; gap: 5px;
        background: #d1fae5; color: #059669;
        padding: 2px 10px; border-radius: 12px; font-size: 12px;
    }

    /* Documents CTA */
    .docs-cta {
        background: white; border-radius: 14px;
        padding: 28px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        display: flex; align-items: center; gap: 24px;
    }
    .docs-cta-icon {
        width: 64px; height: 64px; border-radius: 14px;
        background: #f0fdf4; color: #00b207;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; flex-shrink: 0;
    }
    .docs-cta-body { flex: 1; }
    .docs-cta-body h3 { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 6px; }
    .docs-cta-body p  { font-size: 13px; color: #6b7280; margin-bottom: 14px; line-height: 1.5; }
    .btn-docs {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 22px; background: #00b207; color: white;
        border-radius: 8px; font-size: 14px; font-weight: 600;
        text-decoration: none; transition: background 0.2s;
    }
    .btn-docs:hover { background: #009906; }
    @media (max-width: 600px) {
        .docs-cta { flex-direction: column; text-align: center; }
    }

    /* Contact strip */
    .contact-strip {
        background: #f8fafc; border: 1px solid #e5e7eb;
        border-radius: 12px; padding: 16px 24px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; flex-wrap: wrap; margin-top: 4px;
        font-size: 13px; color: #374151;
    }
    .contact-strip a { color: #00b207; font-weight: 600; text-decoration: none; }
    .contact-strip a:hover { text-decoration: underline; }
</style>

<?php
$_bizDeadline = $business['verification_deadline'] ?? null;
$_bizDaysLeft = $_bizDeadline ? max(0, (int)ceil((strtotime($_bizDeadline) - time()) / 86400)) : null;
$_bizUrgent   = $_bizDaysLeft !== null && $_bizDaysLeft <= 7;
?>
<!-- Hero -->
<div class="pending-hero">
    <div class="pending-hero-icon"><i class="fas fa-hourglass-half"></i></div>
    <div style="flex:1;">
        <h1><?= $currentLang === 'fr' ? 'Bienvenue, ' : 'Welcome, ' ?><?= htmlspecialchars($business['company_name']) ?>!</h1>
        <p>
            <?= $currentLang === 'fr'
                ? 'Votre compte est en cours de vérification. Téléversez vos documents pour accélérer l\'approbation — vous recevrez un courriel une fois approuvé.'
                : 'Your account is under review. Upload your verification documents to speed up approval — you\'ll receive an email once approved.' ?>
        </p>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:14px;">
            <div class="pending-status-pill">
                <i class="fas fa-circle"></i>
                <?= $currentLang === 'fr' ? 'Vérification en cours' : 'Verification In Progress' ?>
            </div>
            <?php if ($_bizDaysLeft !== null): ?>
            <div style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;background:<?= $_bizUrgent ? '#fef2f2' : '#bbf7d0' ?>;color:<?= $_bizUrgent ? '#dc2626' : '#15803d' ?>;">
                <i class="fas fa-calendar-alt"></i>
                <?= $_bizDaysLeft ?> <?= $currentLang === 'fr' ? 'jours restants' : 'days left' ?>
            </div>
            <?php endif; ?>
            <a href="<?= url('distribution/documents') ?>" style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;background:#065f46;color:white;text-decoration:none;">
                <i class="fas fa-upload"></i>
                <?= $currentLang === 'fr' ? 'Télécharger mes documents' : 'Upload Documents' ?>
            </a>
            <a href="mailto:info@ocsapp.ca" style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(0,0,0,0.06);color:#14532d;text-decoration:none;">
                <i class="fas fa-envelope"></i> info@ocsapp.ca
            </a>
        </div>
    </div>
</div>

<!-- Two-column: Steps + Account Info -->
<div class="pending-grid">

    <!-- What happens next -->
    <div class="steps-card">
        <h3><i class="fas fa-list-check"></i> <?= $currentLang === 'fr' ? 'Ce qui se passe ensuite' : 'What Happens Next' ?></h3>

        <div class="step-item">
            <div>
                <div class="step-number done"><i class="fas fa-check"></i></div>
                <div class="step-connector"></div>
            </div>
            <div class="step-body">
                <div class="step-title"><?= $currentLang === 'fr' ? 'Demande soumise' : 'Application Submitted' ?></div>
                <div class="step-desc"><?= $currentLang === 'fr' ? 'Votre compte a été créé avec succès.' : 'Your account has been created successfully.' ?></div>
            </div>
        </div>

        <div class="step-item">
            <div>
                <div class="step-number active"><i class="fas fa-search"></i></div>
                <div class="step-connector"></div>
            </div>
            <div class="step-body">
                <div class="step-title"><?= $currentLang === 'fr' ? 'Examen en cours' : 'Under Review' ?></div>
                <div class="step-desc"><?= $currentLang === 'fr' ? 'Notre équipe examine votre dossier. Cela prend généralement 1 à 2 jours ouvrables.' : 'Our team is reviewing your file. This typically takes 1–2 business days.' ?></div>
            </div>
        </div>

        <div class="step-item">
            <div>
                <div class="step-number todo">3</div>
                <div class="step-connector"></div>
            </div>
            <div class="step-body">
                <div class="step-title"><?= $currentLang === 'fr' ? 'Approbation du compte' : 'Account Approved' ?></div>
                <div class="step-desc"><?= $currentLang === 'fr' ? 'Vous recevrez un courriel de confirmation avec vos accès complets.' : 'You\'ll receive a confirmation email with full portal access.' ?></div>
            </div>
        </div>

        <div class="step-item">
            <div>
                <div class="step-number todo">4</div>
            </div>
            <div class="step-body">
                <div class="step-title"><?= $currentLang === 'fr' ? 'Accès complet débloqué' : 'Full Access Unlocked' ?></div>
                <div class="step-desc"><?= $currentLang === 'fr' ? 'Créez vos demandes, gérez vos envois et plus encore.' : 'Create requests, manage shipments, and more.' ?></div>
            </div>
        </div>
    </div>

    <!-- Account Info -->
    <div class="info-card">
        <h3><i class="fas fa-building"></i> <?= $currentLang === 'fr' ? 'Informations du compte' : 'Account Information' ?></h3>

        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Entreprise' : 'Company' ?></span>
            <span class="info-val"><?= htmlspecialchars($business['company_name']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Contact' : 'Contact' ?></span>
            <span class="info-val"><?= htmlspecialchars(($business['first_name'] ?? '') . ' ' . ($business['last_name'] ?? '')) ?></span>
        </div>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Courriel' : 'Email' ?></span>
            <span class="info-val"><?= htmlspecialchars($business['email'] ?? '') ?></span>
        </div>
        <?php if (!empty($business['phone'])): ?>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Téléphone' : 'Phone' ?></span>
            <span class="info-val"><?= htmlspecialchars($business['phone']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($business['business_type'])): ?>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Type' : 'Business Type' ?></span>
            <span class="info-val"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $business['business_type']))) ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Membre depuis' : 'Member Since' ?></span>
            <span class="info-val"><?= date('F j, Y', strtotime($business['created_at'])) ?></span>
        </div>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Statut' : 'Status' ?></span>
            <span class="info-val">
                <span class="status-pending"><i class="fas fa-clock"></i> <?= $currentLang === 'fr' ? 'En attente' : 'Pending' ?></span>
            </span>
        </div>
    </div>

</div>


<?php else: ?>
<!-- ═══════════════════════════════════════════════════════ -->
<!--  NORMAL (APPROVED) DASHBOARD                           -->
<!-- ═══════════════════════════════════════════════════════ -->
<style>
    /* Welcome Section */
    .welcome-section {
        background: linear-gradient(135deg, #00b207 0%, #009906 100%);
        border-radius: 16px;
        padding: 32px;
        color: white;
        margin-bottom: 32px;
    }
    .welcome-section h1 { font-size: 24px; font-weight: 600; margin-bottom: 8px; }
    .welcome-section p { opacity: 0.9; font-size: 14px; }
    .account-tier {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.2); padding: 6px 12px;
        border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 16px;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px; margin-bottom: 32px;
    }
    .stat-card {
        background: white; border-radius: 12px; padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 16px; font-size: 20px;
    }
    .stat-icon.green { background: #f0fdf4; color: #00b207; }
    .stat-icon.blue { background: #eff6ff; color: #3b82f6; }
    .stat-icon.orange { background: #fff7ed; color: #f97316; }
    .stat-icon.purple { background: #faf5ff; color: #a855f7; }
    .stat-value { font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 4px; }
    .stat-label { font-size: 13px; color: #666; }

    /* Section Cards */
    .section-card {
        background: white; border-radius: 12px; padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px;
    }
    .section-header {
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
    }
    .section-title {
        font-size: 18px; font-weight: 600; color: #1a1a1a;
        display: flex; align-items: center; gap: 10px;
    }
    .section-title i { color: #00b207; }

    /* Quick Actions */
    .quick-actions {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;
    }
    .action-btn {
        display: flex; flex-direction: column; align-items: center; gap: 12px;
        padding: 24px; background: #f8fafc; border: 2px dashed #e5e7eb;
        border-radius: 12px; text-decoration: none; color: #666; transition: all 0.2s;
    }
    .action-btn:hover { border-color: #00b207; background: #f0fdf4; color: #00b207; }
    .action-btn i { font-size: 28px; }
    .action-btn span { font-size: 14px; font-weight: 500; }
    .action-btn.coming-soon { opacity: 0.5; cursor: not-allowed; }
    .action-btn.coming-soon:hover { border-color: #e5e7eb; background: #f8fafc; color: #666; }

    /* Badges */
    .badge {
        display: inline-block; padding: 4px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 600; text-transform: uppercase;
    }
    .badge-standard { background: #f3f4f6; color: #666; }
    .badge-approved { background: #dbeafe; color: #1d4ed8; }
    .badge-premium { background: #fef3c7; color: #b45309; }
    .badge-draft { background: #f3f4f6; color: #666; }
    .badge-submitted, .badge-pending { background: #dbeafe; color: #1d4ed8; }
    .badge-quoted, .badge-pending_payment { background: #fef3c7; color: #b45309; }
    .badge-paid { background: #d1fae5; color: #059669; }
    .badge-processing, .badge-procurement { background: #e0e7ff; color: #4f46e5; }
    .badge-ready, .badge-in_transit { background: #cffafe; color: #0891b2; }
    .badge-completed, .badge-delivered { background: #d1fae5; color: #059669; }
    .badge-cancelled { background: #fef2f2; color: #991b1b; }
    .badge-expired { background: #f3f4f6; color: #666; }

    /* Action Buttons */
    .btn-pay {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
        background: #00b207; color: white; border: none; border-radius: 6px;
        font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s;
    }
    .btn-pay:hover { background: #009906; }
    .btn-cancel-req {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
        background: #fee2e2; color: #dc2626; border: none; border-radius: 6px;
        font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s;
    }
    .btn-cancel-req:hover { background: #fecaca; }
    .btn-view {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
        background: #f3f4f6; color: #666; border: none; border-radius: 6px;
        font-size: 12px; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.2s;
    }
    .btn-view:hover { background: #e5e7eb; color: #333; }
    .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }

    /* Payment Alert */
    .payment-alert {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0;
        padding: 16px 20px; margin-bottom: 24px;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
    }
    .payment-alert-content { display: flex; align-items: center; gap: 12px; }
    .payment-alert-content i { font-size: 24px; color: #f59e0b; }
    .payment-alert h4 { font-size: 14px; font-weight: 600; color: #92400e; margin: 0 0 4px; }
    .payment-alert p { font-size: 13px; color: #a16207; margin: 0; }

    /* Empty State */
    .empty-state { text-align: center; padding: 48px 24px; color: #666; }
    .empty-state i { font-size: 48px; color: #d1d5db; margin-bottom: 16px; }
    .empty-state h3 { font-size: 16px; color: #1a1a1a; margin-bottom: 8px; }
    .empty-state p { font-size: 14px; }

    /* Cancel Modal */
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content { background: white; border-radius: 12px; padding: 24px; max-width: 400px; width: 90%; }
    .modal-title { font-size: 18px; font-weight: 600; color: #1a1a1a; margin-bottom: 16px; }
    .modal-form textarea {
        width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;
        font-family: inherit; font-size: 14px; resize: vertical; min-height: 80px;
    }
    .modal-form textarea:focus { outline: none; border-color: #00b207; }
    .modal-buttons { display: flex; gap: 12px; margin-top: 16px; }
    .modal-buttons button {
        flex: 1; padding: 10px 16px; border: none; border-radius: 8px;
        font-size: 14px; font-weight: 500; cursor: pointer;
    }
    .modal-btn-cancel { background: #f3f4f6; color: #666; }
    .modal-btn-confirm { background: #dc2626; color: white; }

    /* Address Form */
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 13px; }
    .form-group input, .form-group select {
        width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;
        font-size: 14px; font-family: inherit; transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group select:focus {
        outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
    }
    .form-group small { display: block; margin-top: 4px; font-size: 12px; color: #6b7280; }
    .btn-save-address {
        padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px;
        font-weight: 600; cursor: pointer; background: linear-gradient(135deg, #00b207 0%, #008505 100%);
        color: white; transition: all 0.2s;
    }
    .btn-save-address:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,178,7,0.3); }
    .btn-save-address i { margin-right: 6px; }

    /* Address Autocomplete */
    .address-suggestions {
        display: none; position: absolute; top: 100%; left: 0; right: 0;
        background: #fff; border: 1px solid #d1d5db; border-top: none;
        border-radius: 0 0 6px 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000; max-height: 240px; overflow-y: auto;
    }
    .address-suggestions.active { display: block; }
    .address-suggestion-item {
        padding: 10px 14px; cursor: pointer; font-size: 14px; color: #374151;
        border-bottom: 1px solid #f3f4f6; display: flex; align-items: flex-start;
        gap: 10px; transition: background 0.15s;
    }
    .address-suggestion-item:last-child { border-bottom: none; }
    .address-suggestion-item:hover, .address-suggestion-item.highlighted { background: #f0fdf4; }
    .address-suggestion-item i { color: #00b207; margin-top: 3px; flex-shrink: 0; }
    .address-suggestion-item .suggestion-main { font-weight: 500; }
    .address-suggestion-item .suggestion-detail { font-size: 12px; color: #6b7280; }
    .address-autocomplete-wrapper small i { margin-right: 4px; color: #00b207; }
    .address-loading { padding: 12px 14px; text-align: center; color: #6b7280; font-size: 13px; }

    /* Account Info Grid */
    .account-info { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
    .info-group { margin-bottom: 16px; }
    .info-label { font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .info-value { font-size: 15px; color: #1a1a1a; font-weight: 500; }

    .address-message {
        padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; display: none;
    }
    .address-message.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; display: block; }
    .address-message.alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; display: block; }

    @media (max-width: 768px) {
        .account-info { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
    }
</style>

<!-- Payment Alert for awaiting payment requests -->
<?php
$awaitingPayment = array_filter($recentRequests ?? [], function($req) {
    return in_array($req['status'], ['quoted', 'approved', 'pending_payment']) && !empty($req['payment_link_token']);
});
if (!empty($awaitingPayment)):
    $payReq = reset($awaitingPayment);
?>
    <div class="payment-alert">
        <div class="payment-alert-content">
            <i class="fas fa-credit-card"></i>
            <div>
                <h4><?= $t['payment_required'] ?></h4>
                <p><?= sprintf($t['payment_ready'], htmlspecialchars($payReq['request_number']), number_format($payReq['total_amount'] ?? 0, 2)) ?></p>
            </div>
        </div>
        <a href="<?= url('distribution/pay?token=' . $payReq['payment_link_token']) ?>" class="btn-pay">
            <i class="fas fa-lock"></i> <?= $t['pay_now'] ?>
        </a>
    </div>
<?php endif; ?>

<!-- Welcome Section -->
<div class="welcome-section">
    <h1><?= $t['welcome'] ?>, <?= htmlspecialchars($business['company_name']) ?>!</h1>
    <p><?= $t['manage_account'] ?></p>
    <div class="account-tier">
        <i class="fas fa-star"></i>
        <?= $tierLabels[$business['account_tier']] ?? $t['tier_standard'] ?>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-clipboard-list"></i></div>
        <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
        <div class="stat-label"><?= $t['total_requests'] ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-clock"></i></div>
        <div class="stat-value"><?= $stats['pending'] ?? 0 ?></div>
        <div class="stat-label"><?= $t['pending'] ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-truck"></i></div>
        <div class="stat-value"><?= $stats['processing'] ?? 0 ?></div>
        <div class="stat-label"><?= $t['in_progress'] ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value"><?= $stats['completed'] ?? 0 ?></div>
        <div class="stat-label"><?= $t['completed'] ?></div>
    </div>
</div>

<!-- Quick Actions - Procurement -->
<div class="section-card">
    <div class="section-header">
        <div class="section-title"><i class="fas fa-shopping-cart"></i> <?= $t['procurement'] ?></div>
    </div>
    <div class="quick-actions">
        <a href="<?= url('distribution/requests/create') ?>" class="action-btn">
            <i class="fas fa-plus-circle"></i><span><?= $t['new_request'] ?></span>
        </a>
        <a href="<?= url('distribution/requests') ?>" class="action-btn">
            <i class="fas fa-list"></i><span><?= $t['view_requests'] ?></span>
        </a>
        <a href="<?= url('distribution/requests?status=draft') ?>" class="action-btn">
            <i class="fas fa-edit"></i><span><?= $t['drafts'] ?></span>
        </a>
        <a href="<?= url('distribution/invoices') ?>" class="action-btn">
            <i class="fas fa-file-invoice-dollar"></i><span><?= $t['invoices'] ?></span>
        </a>
    </div>
</div>

<!-- Quick Actions - Distribution (Outbound) -->
<div class="section-card">
    <div class="section-header">
        <div class="section-title"><i class="fas fa-truck"></i> <?= $t['distribution'] ?></div>
    </div>
    <div class="quick-actions">
        <a href="<?= url('distribution/shipments/create') ?>" class="action-btn">
            <i class="fas fa-box"></i><span><?= $t['new_shipment'] ?></span>
        </a>
        <a href="<?= url('distribution/shipments') ?>" class="action-btn">
            <i class="fas fa-truck-loading"></i><span><?= $t['my_shipments'] ?></span>
        </a>
        <a href="<?= url('distribution/routes') ?>" class="action-btn">
            <i class="fas fa-route"></i><span><?= $t['recurring_routes'] ?></span>
        </a>
        <a href="<?= url('distribution/shipments/track') ?>" class="action-btn">
            <i class="fas fa-search-location"></i><span><?= $t['track_shipment'] ?></span>
        </a>
    </div>
</div>

<!-- Recent Requests -->
<div class="section-card">
    <div class="section-header">
        <div class="section-title"><i class="fas fa-history"></i> <?= $t['recent_requests'] ?></div>
        <?php if (!empty($recentRequests)): ?>
            <a href="<?= url('distribution/requests') ?>" style="font-size: 13px; color: #00b207; text-decoration: none;"><?= $t['view_all'] ?></a>
        <?php endif; ?>
    </div>
    <?php if (empty($recentRequests)): ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3><?= $t['no_requests'] ?></h3>
            <p><?= $t['no_requests_desc'] ?></p>
            <a href="<?= url('distribution/requests/create') ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #00b207; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; margin-top: 16px;">
                <i class="fas fa-plus"></i> <?= $t['create_request'] ?>
            </a>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <th style="padding: 12px; text-align: left; font-size: 12px; color: #666; text-transform: uppercase;"><?= $t['col_request'] ?></th>
                    <th style="padding: 12px; text-align: left; font-size: 12px; color: #666; text-transform: uppercase;"><?= $t['col_status'] ?></th>
                    <th style="padding: 12px; text-align: left; font-size: 12px; color: #666; text-transform: uppercase;"><?= $t['col_total'] ?></th>
                    <th style="padding: 12px; text-align: left; font-size: 12px; color: #666; text-transform: uppercase;"><?= $t['col_date'] ?></th>
                    <th style="padding: 12px; text-align: left; font-size: 12px; color: #666; text-transform: uppercase;"><?= $t['col_actions'] ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentRequests as $req): ?>
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 12px;">
                            <a href="<?= url('distribution/requests/show?id=' . $req['id']) ?>" style="color: #00b207; font-weight: 500; text-decoration: none;">
                                <?= htmlspecialchars($req['request_number']) ?>
                            </a>
                            <div style="font-size: 12px; color: #666;"><?= htmlspecialchars($req['request_name']) ?></div>
                        </td>
                        <td style="padding: 12px;">
                            <span class="badge badge-<?= $req['status'] ?>"><?= $statusLabels[$req['status']] ?? ucwords(str_replace('_', ' ', $req['status'])) ?></span>
                        </td>
                        <td style="padding: 12px; font-size: 14px; color: #1a1a1a; font-weight: 500;">
                            <?= ($req['total_amount'] ?? 0) > 0 ? '$' . number_format($req['total_amount'], 2) : '--' ?>
                        </td>
                        <td style="padding: 12px; font-size: 13px; color: #666;"><?= date('M j, Y', strtotime($req['created_at'])) ?></td>
                        <td style="padding: 12px;">
                            <div class="action-buttons">
                                <?php
                                $status = $req['status'];
                                if (in_array($status, ['quoted', 'approved', 'pending_payment']) && !empty($req['payment_link_token'])):
                                ?>
                                    <a href="<?= url('distribution/pay?token=' . $req['payment_link_token']) ?>" class="btn-pay">
                                        <i class="fas fa-credit-card"></i> <?= $t['pay'] ?>
                                    </a>
                                <?php endif; ?>

                                <?php if (in_array($status, ['pending', 'submitted', 'draft'])): ?>
                                    <button type="button" class="btn-cancel-req" onclick="openCancelModal(<?= $req['id'] ?>, '<?= htmlspecialchars($req['request_number']) ?>')">
                                        <i class="fas fa-times"></i> <?= $t['cancel'] ?>
                                    </button>
                                <?php endif; ?>

                                <a href="<?= url('distribution/requests/show?id=' . $req['id']) ?>" class="btn-view">
                                    <i class="fas fa-eye"></i> <?= $t['view'] ?>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<!-- Cancel Request Modal -->
<div class="modal-overlay" id="cancelModal">
    <div class="modal-content">
        <h3 class="modal-title"><?= $t['cancel_request'] ?></h3>
        <p style="font-size: 14px; color: #666; margin-bottom: 16px;">
            <?= $t['cancel_confirm'] ?> <strong id="cancelRequestNumber"></strong>?
        </p>
        <form method="POST" action="<?= url('distribution/requests/cancel') ?>" class="modal-form">
            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="distribution_request_id" id="cancelRequestId">
            <div style="margin-bottom: 12px;">
                <label style="font-size: 13px; font-weight: 500; color: #333; display: block; margin-bottom: 6px;">
                    <?= $t['reason_optional'] ?>
                </label>
                <textarea name="cancel_reason" placeholder="<?= htmlspecialchars(html_entity_decode($t['reason_placeholder'], ENT_QUOTES, 'UTF-8')) ?>"></textarea>
            </div>
            <div class="modal-buttons">
                <button type="button" class="modal-btn-cancel" onclick="closeCancelModal()"><?= $t['keep_request'] ?></button>
                <button type="submit" class="modal-btn-confirm"><?= $t['cancel_request'] ?></button>
            </div>
        </form>
    </div>
</div>

<script>
// Cancel modal
function openCancelModal(requestId, requestNumber) {
    document.getElementById('cancelRequestId').value = requestId;
    document.getElementById('cancelRequestNumber').textContent = '#' + requestNumber;
    document.getElementById('cancelModal').classList.add('active');
}
function closeCancelModal() {
    document.getElementById('cancelModal').classList.remove('active');
}
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});

// Address form + autocomplete JS moved to settings.php
// Placeholder so nothing breaks if addressForm is not on this page
if (document.getElementById('addressForm')) { document.getElementById('addressForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + dashboardLang.saving;

    try {
        const formData = new FormData(this);
        const response = await fetch('<?= url('distribution/update-address') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        const msgDiv = document.getElementById('addressMessage');

        if (data.success) {
            msgDiv.textContent = data.message;
            msgDiv.className = 'address-message alert-success';
            setTimeout(() => { msgDiv.style.display = 'none'; msgDiv.className = 'address-message'; }, 5000);
        } else {
            msgDiv.textContent = data.message || dashboardLang.error_updating;
            msgDiv.className = 'address-message alert-error';
        }
    } catch (error) {
        console.error('Error:', error);
        const msgDiv = document.getElementById('addressMessage');
        msgDiv.textContent = dashboardLang.error_occurred;
        msgDiv.className = 'address-message alert-error';
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> ' + dashboardLang.save_address;
    }
}); }

// ── Address Autocomplete (Nominatim) ──
if (document.getElementById('delivery_street')) (function() {
    const addressInput = document.getElementById('delivery_street');
    const suggestionsBox = document.getElementById('addressSuggestions');
    const cityInput = document.getElementById('delivery_city');
    const provinceSelect = document.getElementById('delivery_province');
    const postalInput = document.getElementById('delivery_postal_code');
    const countryInput = document.getElementById('delivery_country');
    const latInput = document.getElementById('delivery_latitude');
    const lngInput = document.getElementById('delivery_longitude');

    const provinceMap = {
        'alberta': 'AB', 'british columbia': 'BC', 'manitoba': 'MB',
        'new brunswick': 'NB', 'newfoundland and labrador': 'NL',
        'nova scotia': 'NS', 'northwest territories': 'NT',
        'nunavut': 'NU', 'ontario': 'ON', 'prince edward island': 'PE',
        'quebec': 'QC', 'québec': 'QC', 'saskatchewan': 'SK', 'yukon': 'YT'
    };

    let debounceTimer = null;
    let highlightedIndex = -1;
    let currentResults = [];

    addressInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(debounceTimer);
        if (query.length < 3) { hideSuggestions(); return; }
        debounceTimer = setTimeout(() => searchAddress(query), 350);
    });

    addressInput.addEventListener('keydown', function(e) {
        if (!suggestionsBox.classList.contains('active')) return;
        const items = suggestionsBox.querySelectorAll('.address-suggestion-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            highlightedIndex = Math.min(highlightedIndex + 1, items.length - 1);
            updateHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            highlightedIndex = Math.max(highlightedIndex - 1, 0);
            updateHighlight(items);
        } else if (e.key === 'Enter' && highlightedIndex >= 0) {
            e.preventDefault();
            selectSuggestion(currentResults[highlightedIndex]);
        } else if (e.key === 'Escape') {
            hideSuggestions();
        }
    });

    function updateHighlight(items) {
        items.forEach((item, i) => item.classList.toggle('highlighted', i === highlightedIndex));
        if (items[highlightedIndex]) items[highlightedIndex].scrollIntoView({ block: 'nearest' });
    }

    const abbreviations = {
        'hwy': 'Highway', 'blvd': 'Boulevard', 'ave': 'Avenue',
        'st': 'Street', 'dr': 'Drive', 'rd': 'Road', 'crt': 'Court',
        'cres': 'Crescent', 'pl': 'Place', 'ln': 'Lane', 'pkwy': 'Parkway',
        'cir': 'Circle', 'terr': 'Terrace', 'ct': 'Court',
        'aut': 'Autoroute', 'ch': 'Chemin', 'boul': 'Boulevard',
        'rte': 'Route', 'mtee': 'Montée', 'cote': 'Côte'
    };

    const bilingualNames = {
        'trans-canada': 'Transcanadienne',
        'trans canada': 'Transcanadienne'
    };

    function expandAbbreviations(q) {
        return q.replace(/\b(\w+)\b/g, (match) => abbreviations[match.toLowerCase()] || match);
    }

    function translateToFrench(q) {
        let translated = expandAbbreviations(q);
        const lower = translated.toLowerCase();
        for (const [en, fr] of Object.entries(bilingualNames)) {
            if (lower.includes(en)) {
                translated = translated.replace(new RegExp(en, 'gi'), fr);
            }
        }
        translated = translated.replace(/\bHighway\b/gi, 'Autoroute');
        return translated;
    }

    async function nominatimSearch(query, viewbox) {
        const params = new URLSearchParams({
            q: query, format: 'json', addressdetails: 1, limit: 8
        });
        if (viewbox) {
            params.set('viewbox', viewbox);
            params.set('bounded', 0);
        }
        const response = await fetch('https://nominatim.openstreetmap.org/search?' + params, {
            headers: { 'User-Agent': 'OCSAPP-Distribution/1.0' }
        });
        const data = await response.json();
        return data.filter(r => r.address && r.address.country_code === 'ca');
    }

    const CANADA_VIEWBOX = '-80.0,42.0,-57.0,62.0';

    async function searchAddress(query) {
        suggestionsBox.innerHTML = '<div class="address-loading"><i class="fas fa-spinner fa-spin"></i> ' + dashboardLang.searching + '</div>';
        suggestionsBox.classList.add('active');

        try {
            let data = await nominatimSearch(query + ', Quebec', CANADA_VIEWBOX);

            if (data.length === 0) {
                const french = translateToFrench(query);
                if (french !== query) {
                    data = await nominatimSearch(french + ', Quebec', CANADA_VIEWBOX);
                }
            }

            if (data.length === 0) {
                const expanded = expandAbbreviations(query);
                if (expanded !== query) {
                    data = await nominatimSearch(expanded + ', Quebec', CANADA_VIEWBOX);
                }
            }

            if (data.length === 0) {
                data = await nominatimSearch(query, CANADA_VIEWBOX);
            }

            if (data.length === 0) {
                const expanded = expandAbbreviations(query);
                if (expanded !== query) {
                    data = await nominatimSearch(expanded, CANADA_VIEWBOX);
                }
            }

            currentResults = data;
            highlightedIndex = -1;

            if (data.length === 0) {
                suggestionsBox.innerHTML = '<div class="address-loading">' + dashboardLang.no_results + '</div>';
                return;
            }

            suggestionsBox.innerHTML = data.map((item, i) => {
                const addr = item.address || {};
                const street = [addr.house_number, addr.road].filter(Boolean).join(' ');
                const city = addr.city || addr.town || addr.village || addr.municipality || '';
                const province = addr.state || '';
                return `
                    <div class="address-suggestion-item" data-index="${i}">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <div class="suggestion-main">${street || item.display_name.split(',')[0]}</div>
                            <div class="suggestion-detail">${[city, province, addr.postcode].filter(Boolean).join(', ')}</div>
                        </div>
                    </div>
                `;
            }).join('');

            suggestionsBox.querySelectorAll('.address-suggestion-item').forEach(item => {
                item.addEventListener('click', function() {
                    selectSuggestion(currentResults[parseInt(this.dataset.index)]);
                });
            });

        } catch (error) {
            console.error('Address search error:', error);
            suggestionsBox.innerHTML = '<div class="address-loading">' + dashboardLang.search_failed + '</div>';
        }
    }

    function selectSuggestion(result) {
        const addr = result.address || {};
        const street = [addr.house_number, addr.road].filter(Boolean).join(' ');
        addressInput.value = street || result.display_name.split(',')[0];

        const city = addr.city || addr.town || addr.village || addr.municipality || '';
        cityInput.value = city;

        const stateLower = (addr.state || '').toLowerCase();
        const provinceCode = provinceMap[stateLower] || '';
        if (provinceCode) provinceSelect.value = provinceCode;

        postalInput.value = addr.postcode || '';
        countryInput.value = addr.country || 'Canada';
        latInput.value = result.lat || '';
        lngInput.value = result.lon || '';

        hideSuggestions();

        [cityInput, provinceSelect, postalInput, countryInput].forEach(el => {
            el.style.transition = 'background 0.3s';
            el.style.background = '#d1fae5';
            setTimeout(() => { el.style.background = ''; }, 1200);
        });
    }

    function hideSuggestions() {
        suggestionsBox.classList.remove('active');
        suggestionsBox.innerHTML = '';
        currentResults = [];
        highlightedIndex = -1;
    }

    document.addEventListener('click', function(e) {
        if (!addressInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            hideSuggestions();
        }
    });
})();
</script>

<?php endif; // end pending vs approved dashboard ?>

<?php include __DIR__ . '/layout-footer.php'; ?>
