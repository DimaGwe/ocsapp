<?php
/**
 * Admin Driver Details View
 * File: app/Views/admin/delivery/driver-details.php
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$pageTitle   = $currentLang === 'fr' ? 'Détails du livreur' : 'Driver Details';
$currentPage = 'delivery';

$t = [
    'en' => [
        'back_link'         => 'Back to Delivery Staff',
        'edit_driver'       => 'Edit Driver',
        'stat_total'        => 'Total Deliveries',
        'stat_completed'    => 'Completed',
        'stat_earnings'     => 'Total Earnings',
        'stat_rating'       => 'Average Rating',
        'account_info'      => 'Account Information',
        'delivery_settings' => 'Delivery Settings',
        'activity'          => 'Activity',
        'driver_id'         => 'Driver ID',
        'email'             => 'Email',
        'phone'             => 'Phone',
        'account_status'    => 'Account Status',
        'availability'      => 'Availability',
        'max_deliveries'    => 'Max Deliveries',
        'at_once'           => 'at once',
        'active_deliveries' => 'Active Deliveries',
        'assigned_zone'     => 'Assigned Zone',
        'not_assigned'      => 'Not assigned',
        'joined_date'       => 'Joined Date',
        'last_location'     => 'Last Location Update',
        'total_distance'    => 'Total Distance',
        'never'             => 'Never',
        'compliance'        => 'Compliance Documents',
        'submitted'         => 'Submitted:',
        'doc_date_lbl'      => 'Doc date:',
        'notes_lbl'         => 'Notes:',
        'awaiting_sub'      => 'Awaiting driver submission',
        'btn_verify'        => 'Verify',
        'btn_flag'          => 'Flag',
        'btn_waive'         => 'Waive',
        'view_doc'          => 'View Document',
        'notes_field'       => 'Notes',
        'notes_ph'          => 'Optional admin notes...',
        'btn_cancel'        => 'Cancel',
        'btn_confirm'       => 'Confirm',
        'perf_score'        => 'Performance Score',
        'perf_rolling'      => '(30-day rolling)',
        'based_on'          => 'Based on',
        'scored_del'        => 'scored deliveries',
        'resp_speed'        => 'Response Speed',
        'timeliness'        => 'Timeliness',
        'completion'        => 'Completion',
        'resp_desc'         => '0-20 pts - How fast driver accepts assignment',
        'time_desc'         => '0-60 pts - Delivered before order deadline',
        'comp_desc'         => '0-20 pts - Pickup speed relative to submission',
        'col_request'       => 'Request',
        'col_score'         => 'Score',
        'col_response'      => 'Response',
        'col_timeliness'    => 'Timeliness',
        'col_completion'    => 'Completion',
        'col_date'          => 'Date',
        'no_scores'         => 'No performance scores yet. Scores are calculated automatically when a distribution delivery is completed.',
        'recent_del'        => 'Recent Deliveries',
        'col_order'         => 'Order #',
        'col_customer'      => 'Customer',
        'col_shop'          => 'Shop',
        'col_fee'           => 'Fee',
        'col_status'        => 'Status',
        'col_rating'        => 'Rating',
        'no_deliveries'     => 'No deliveries yet',
        'js_verify'         => 'Verify Document',
        'js_flag'           => 'Flag Document',
        'js_waive'          => 'Waive Requirement',
        'js_flag_reason'    => 'Please enter a reason for flagging.',
        'js_saving'         => 'Saving...',
        'js_failed'         => 'Failed to update.',
        'js_network'        => 'Network error.',
        'bgcheck'           => 'Background Check',
        'bgcheck_not_sub'   => 'Not submitted yet',
        'bgcheck_view'      => 'View Document',
        'bgcheck_verify'    => 'Verify',
        'bgcheck_flag'      => 'Flag',
        'bgcheck_waive'     => 'Waive',
        'bgcheck_verified'  => 'Verified on',
        'bgcheck_uploaded'  => 'Uploaded',
        'bgcheck_notes_lbl' => 'Admin notes:',
        'bgcheck_js_verify'   => 'Verify Background Check',
        'bgcheck_js_flag'     => 'Flag Background Check',
        'bgcheck_js_waive'    => 'Waive Background Check',
        'bgcheck_request'     => 'Request Background Check',
        'bgcheck_resend'      => 'Resend Request',
        'compliance_request'  => 'Request Documents',
        'compliance_req_sent' => 'Reminder sent.',
        'payment_info'      => 'Payment Information',
        'pay_method'        => 'Method',
        'pay_bank'          => 'Bank',
        'pay_holder'        => 'Account Holder',
        'pay_transit'       => 'Transit',
        'pay_institution'   => 'Institution',
        'pay_account'       => 'Account',
        'pay_type'          => 'Account Type',
        'pay_interac'       => 'Interac Email',
        'pay_updated'       => 'Last updated',
        'pay_none'          => 'No payment information on file.',
        'pay_eft'           => 'EFT / Direct Deposit',
        'pay_interac_lbl'   => 'Interac e-Transfer',
        'pay_cheque'        => 'Cheque',
    ],
    'fr' => [
        'back_link'         => 'Retour au personnel de livraison',
        'edit_driver'       => 'Modifier le livreur',
        'stat_total'        => 'Livraisons totales',
        'stat_completed'    => 'Complétées',
        'stat_earnings'     => 'Gains totaux',
        'stat_rating'       => 'Note moyenne',
        'account_info'      => 'Informations du compte',
        'delivery_settings' => 'Paramètres de livraison',
        'activity'          => 'Activité',
        'driver_id'         => 'No de livreur',
        'email'             => 'Courriel',
        'phone'             => 'Téléphone',
        'account_status'    => 'Statut du compte',
        'availability'      => 'Disponibilité',
        'max_deliveries'    => 'Livraisons max',
        'at_once'           => 'à la fois',
        'active_deliveries' => 'Livraisons actives',
        'assigned_zone'     => 'Zone assignée',
        'not_assigned'      => 'Non assigné',
        'joined_date'       => "Date d'inscription",
        'last_location'     => 'Dernière mise à jour GPS',
        'total_distance'    => 'Distance totale',
        'never'             => 'Jamais',
        'compliance'        => 'Documents de conformité',
        'submitted'         => 'Soumis :',
        'doc_date_lbl'      => 'Date du document :',
        'notes_lbl'         => 'Notes :',
        'awaiting_sub'      => 'En attente de soumission du livreur',
        'btn_verify'        => 'Vérifier',
        'btn_flag'          => 'Signaler',
        'btn_waive'         => 'Dispenser',
        'view_doc'          => 'Voir le document',
        'notes_field'       => 'Notes',
        'notes_ph'          => 'Notes administratives (optionnel)...',
        'btn_cancel'        => 'Annuler',
        'btn_confirm'       => 'Confirmer',
        'perf_score'        => 'Score de performance',
        'perf_rolling'      => '(30 derniers jours)',
        'based_on'          => 'Basé sur',
        'scored_del'        => 'livraisons évaluées',
        'resp_speed'        => 'Vitesse de réponse',
        'timeliness'        => 'Ponctualité',
        'completion'        => 'Exécution',
        'resp_desc'         => "0-20 pts - Rapidité d'acceptation de l'assignation",
        'time_desc'         => '0-60 pts - Livré avant la date limite de la commande',
        'comp_desc'         => '0-20 pts - Vitesse de ramassage par rapport à la soumission',
        'col_request'       => 'Demande',
        'col_score'         => 'Score',
        'col_response'      => 'Réponse',
        'col_timeliness'    => 'Ponctualité',
        'col_completion'    => 'Exécution',
        'col_date'          => 'Date',
        'no_scores'         => "Aucun score de performance pour l'instant. Les scores sont calculés automatiquement lorsqu'une livraison de distribution est complétée.",
        'recent_del'        => 'Livraisons récentes',
        'col_order'         => 'Commande #',
        'col_customer'      => 'Client',
        'col_shop'          => 'Boutique',
        'col_fee'           => 'Frais',
        'col_status'        => 'Statut',
        'col_rating'        => 'Note',
        'no_deliveries'     => "Aucune livraison pour l'instant",
        'js_verify'         => 'Vérifier le document',
        'js_flag'           => 'Signaler le document',
        'js_waive'          => "Dispenser l'exigence",
        'js_flag_reason'    => 'Veuillez entrer une raison pour signaler ce document.',
        'js_saving'         => 'Sauvegarde...',
        'js_failed'         => 'Échec de la mise à jour.',
        'js_network'        => 'Erreur réseau.',
        'bgcheck'           => 'Vérification des antécédents',
        'bgcheck_not_sub'   => 'Pas encore soumis',
        'bgcheck_view'      => 'Voir le document',
        'bgcheck_verify'    => 'Vérifier',
        'bgcheck_flag'      => 'Signaler',
        'bgcheck_waive'     => 'Dispenser',
        'bgcheck_verified'  => 'Vérifié le',
        'bgcheck_uploaded'  => 'Soumis le',
        'bgcheck_notes_lbl' => 'Notes administratives :',
        'bgcheck_js_verify'   => 'Vérifier les antécédents',
        'bgcheck_js_flag'     => 'Signaler les antécédents',
        'bgcheck_js_waive'    => "Dispenser l'exigence",
        'bgcheck_request'     => 'Demander les antécédents',
        'bgcheck_resend'      => 'Renvoyer la demande',
        'compliance_request'  => 'Demander les documents',
        'compliance_req_sent' => 'Rappel envoyé.',
        'payment_info'      => 'Informations de paiement',
        'pay_method'        => 'Méthode',
        'pay_bank'          => 'Banque',
        'pay_holder'        => 'Titulaire du compte',
        'pay_transit'       => 'Transit',
        'pay_institution'   => 'Institution',
        'pay_account'       => 'Compte',
        'pay_type'          => 'Type de compte',
        'pay_interac'       => 'Courriel Interac',
        'pay_updated'       => 'Dernière mise à jour',
        'pay_none'          => 'Aucune information de paiement enregistrée.',
        'pay_eft'           => 'Virement bancaire (VD)',
        'pay_interac_lbl'   => 'Virement Interac',
        'pay_cheque'        => 'Chèque',
    ],
];
$t = $t[$currentLang] ?? $t['en'];

$statusLabels = [
    'active'    => $currentLang === 'fr' ? 'Actif'      : 'Active',
    'inactive'  => $currentLang === 'fr' ? 'Inactif'    : 'Inactive',
    'suspended' => $currentLang === 'fr' ? 'Suspendu'   : 'Suspended',
    'available' => $currentLang === 'fr' ? 'Disponible' : 'Available',
    'busy'      => $currentLang === 'fr' ? 'Occupé'     : 'Busy',
    'offline'   => $currentLang === 'fr' ? 'Hors ligne' : 'Offline',
];

$complianceDocDefs = $currentLang === 'fr' ? [
    'class5_license'       => ['label' => 'Permis de conduire classe 5',           'icon' => 'id-card'],
    'saaq_record'          => ['label' => 'Dossier de conduite SAAQ',              'icon' => 'car-side'],
    'commercial_insurance' => ['label' => "Preuve d'assurance commerciale (COI)",  'icon' => 'file-shield'],
    'vehicle_registration' => ['label' => 'Immatriculation du véhicule',           'icon' => 'file-contract'],
    'work_authorization'   => ['label' => "Preuve d'autorisation de travail",      'icon' => 'passport'],
] : [
    'class5_license'       => ['label' => "Class 5 Driver's License",              'icon' => 'id-card'],
    'saaq_record'          => ['label' => 'SAAQ Driving Record',                   'icon' => 'car-side'],
    'commercial_insurance' => ['label' => 'Proof of Commercial Insurance (COI)',   'icon' => 'file-shield'],
    'vehicle_registration' => ['label' => 'Vehicle Registration',                  'icon' => 'file-contract'],
    'work_authorization'   => ['label' => 'Proof of Work Authorization',           'icon' => 'passport'],
];

$cdStatusColors = [
    'not_uploaded' => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => $currentLang === 'fr' ? 'Non soumis'             : 'Not Submitted'],
    'not_required' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'label' => $currentLang === 'fr' ? 'Non requis'             : 'Not Required'],
    'uploaded'     => ['bg' => '#fef3c7', 'color' => '#b45309', 'label' => $currentLang === 'fr' ? 'En attente de révision' : 'Awaiting Review'],
    'verified'     => ['bg' => '#dcfce7', 'color' => '#16a34a', 'label' => $currentLang === 'fr' ? 'Vérifié'                : 'Verified'],
    'flagged'      => ['bg' => '#fee2e2', 'color' => '#dc2626', 'label' => $currentLang === 'fr' ? 'Signalé'                : 'Flagged'],
];

ob_start();
?>

<style>
  /* Page Layout */
  .driver-details-page {
    max-width: 1400px;
    margin: 0 auto;
  }

  /* Back Button */
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 24px;
    transition: color var(--transition-base);
  }

  .back-link:hover {
    color: var(--primary-600);
  }

  /* Driver Header Card */
  .driver-header {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    margin-bottom: 24px;
  }

  .driver-header-content {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 24px;
  }

  .driver-profile {
    display: flex;
    align-items: center;
    gap: 24px;
  }

  .driver-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    flex-shrink: 0;
    overflow: hidden;
  }

  .driver-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
  }

  .driver-info h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .driver-contact {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-top: 8px;
  }

  .driver-contact-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--gray-600);
    font-size: 14px;
  }

  .driver-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  /* Status Badge */
  .status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: var(--radius-full);
    font-size: 13px;
    font-weight: 600;
  }

  .status-badge.active { background: #dcfce7; color: #166534; }
  .status-badge.inactive { background: var(--gray-200); color: var(--gray-700); }
  .status-badge.suspended { background: #fee2e2; color: #991b1b; }
  .status-badge.available { background: #dcfce7; color: #166534; }
  .status-badge.busy { background: #fef3c7; color: #92400e; }
  .status-badge.offline { background: var(--gray-200); color: var(--gray-700); }

  /* Action Buttons */
  .btn-action {
    padding: 10px 20px;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn-edit {
    background: var(--primary);
    color: white;
  }

  .btn-edit:hover {
    background: var(--primary-600);
  }

  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
  }

  .stat-card-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .stat-icon.blue { background: #dbeafe; color: #3b82f6; }
  .stat-icon.green { background: #dcfce7; color: #22c55e; }
  .stat-icon.orange { background: #ffedd5; color: #f97316; }
  .stat-icon.purple { background: #f3e8ff; color: #a855f7; }

  .stat-label {
    font-size: 13px;
    color: var(--gray-600);
    margin-bottom: 8px;
  }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
  }

  /* Info Grid */
  .info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 24px;
  }

  .info-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
  }

  .info-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 16px;
  }

  .info-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .info-item dt {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-500);
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .info-item dd {
    font-size: 14px;
    color: var(--dark);
  }

  /* Table Card */
  .table-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 24px;
  }

  .table-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
  }

  .table-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--dark);
  }

  .table-wrapper {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: var(--gray-50);
  }

  th {
    padding: 12px 24px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  td {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    font-size: 14px;
  }

  tbody tr {
    transition: background var(--transition-base);
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .badge.delivered { background: #dcfce7; color: #166534; }
  .badge.assigned { background: #dbeafe; color: #1e40af; }
  .badge.accepted { background: #fef3c7; color: #92400e; }
  .badge.picked_up { background: #e0e7ff; color: #3730a3; }
  .badge.on_the_way { background: #ddd6fe; color: #5b21b6; }
  .badge.failed { background: #fee2e2; color: #991b1b; }

  /* Empty State */
  .empty-state {
    padding: 64px 24px;
    text-align: center;
  }

  .empty-icon {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 16px;
  }

  .empty-text {
    font-size: 15px;
    color: var(--gray-500);
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .info-grid {
      grid-template-columns: 1fr;
    }

    .driver-header-content {
      flex-direction: column;
    }

    .driver-actions {
      width: 100%;
      justify-content: flex-start;
    }
  }

  @media (max-width: 768px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }

    .driver-profile {
      flex-direction: column;
      text-align: center;
    }

    .driver-contact {
      align-items: center;
    }
  }
</style>

<div class="driver-details-page">
  <!-- Back Button -->
  <a href="<?= url('admin/delivery/staff') ?>" class="back-link">
    <i class="fas fa-arrow-left"></i> <?= $t['back_link'] ?>
  </a>

  <!-- Driver Header -->
  <div class="driver-header">
    <div class="driver-header-content">
      <div class="driver-profile">
        <div class="driver-avatar-large">
          <?php if (!empty($driver['avatar'])): ?>
            <img src="<?= htmlspecialchars('https://ocsapp.ca/' . ltrim($driver['avatar'], '/')) ?>"
                 alt="<?= htmlspecialchars($driver['first_name'] ?? '') ?>">
          <?php else: ?>
            <?= strtoupper(substr($driver['first_name'] ?? 'D', 0, 1)) ?>
          <?php endif; ?>
        </div>
        <div class="driver-info">
          <h1><?= htmlspecialchars(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? '')) ?></h1>
          <div class="driver-contact">
            <span class="driver-contact-item">
              <i class="fas fa-envelope"></i>
              <?= htmlspecialchars($driver['email'] ?? '') ?>
            </span>
            <?php if (!empty($driver['phone'])): ?>
              <span class="driver-contact-item">
                <i class="fas fa-phone"></i>
                <?= htmlspecialchars($driver['phone']) ?>
              </span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="driver-actions">
        <?php
        $driverStatus       = $driver['status'] ?? 'inactive';
        $driverAvailability = $driver['availability_status'] ?? 'offline';
        ?>
        <span class="status-badge <?= $driverStatus ?>">
          <?= $statusLabels[$driverStatus] ?? ucfirst($driverStatus) ?>
        </span>
        <span class="status-badge <?= $driverAvailability ?>">
          <?= $statusLabels[$driverAvailability] ?? ucfirst($driverAvailability) ?>
        </span>

        <a href="<?= url('admin/delivery/edit-driver?id=' . ($driver['id'] ?? 0)) ?>" class="btn-action btn-edit">
          <i class="fas fa-edit"></i> <?= $t['edit_driver'] ?>
        </a>
      </div>
    </div>
  </div>

  <!-- Statistics -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-card-inner">
        <div>
          <div class="stat-label"><?= $t['stat_total'] ?></div>
          <div class="stat-value"><?= number_format($stats['total_deliveries'] ?? 0) ?></div>
        </div>
        <div class="stat-icon blue">
          <i class="fas fa-truck"></i>
        </div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-card-inner">
        <div>
          <div class="stat-label"><?= $t['stat_completed'] ?></div>
          <div class="stat-value"><?= number_format($stats['completed_deliveries'] ?? 0) ?></div>
        </div>
        <div class="stat-icon green">
          <i class="fas fa-check-circle"></i>
        </div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-card-inner">
        <div>
          <div class="stat-label"><?= $t['stat_earnings'] ?></div>
          <div class="stat-value" style="font-size: 20px;"><?= currency($stats['total_earnings'] ?? 0) ?></div>
        </div>
        <div class="stat-icon orange">
          <i class="fas fa-dollar-sign"></i>
        </div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-card-inner">
        <div>
          <div class="stat-label"><?= $t['stat_rating'] ?></div>
          <div class="stat-value"><?= number_format($stats['avg_rating'] ?? 0, 1) ?> ⭐</div>
        </div>
        <div class="stat-icon purple">
          <i class="fas fa-star"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Information Grid -->
  <div class="info-grid">
    <!-- Account Information -->
    <div class="info-card">
      <h3><?= $t['account_info'] ?></h3>
      <dl class="info-list">
        <div class="info-item">
          <dt><?= $t['driver_id'] ?></dt>
          <dd>#<?= $driver['id'] ?? 'N/A' ?></dd>
        </div>
        <div class="info-item">
          <dt><?= $t['email'] ?></dt>
          <dd><?= htmlspecialchars($driver['email'] ?? 'N/A') ?></dd>
        </div>
        <div class="info-item">
          <dt><?= $t['phone'] ?></dt>
          <dd><?= htmlspecialchars($driver['phone'] ?? 'N/A') ?></dd>
        </div>
        <div class="info-item">
          <dt><?= $t['account_status'] ?></dt>
          <dd>
            <span class="badge <?= $driverStatus ?>">
              <?= $statusLabels[$driverStatus] ?? ucfirst($driverStatus) ?>
            </span>
          </dd>
        </div>
      </dl>
    </div>

    <!-- Delivery Settings -->
    <div class="info-card">
      <h3><?= $t['delivery_settings'] ?></h3>
      <dl class="info-list">
        <div class="info-item">
          <dt><?= $t['availability'] ?></dt>
          <dd>
            <span class="badge <?= $driverAvailability ?>">
              <?= $statusLabels[$driverAvailability] ?? ucfirst($driverAvailability) ?>
            </span>
          </dd>
        </div>
        <div class="info-item">
          <dt><?= $t['max_deliveries'] ?></dt>
          <dd><?= $driver['max_deliveries'] ?? 3 ?> <?= $t['at_once'] ?></dd>
        </div>
        <div class="info-item">
          <dt><?= $t['active_deliveries'] ?></dt>
          <dd><?= $driver['active_deliveries'] ?? 0 ?></dd>
        </div>
        <div class="info-item">
          <dt><?= $t['assigned_zone'] ?></dt>
          <dd><?= htmlspecialchars($driver['zone_name'] ?? $t['not_assigned']) ?></dd>
        </div>
      </dl>
    </div>

    <!-- Activity -->
    <div class="info-card">
      <h3><?= $t['activity'] ?></h3>
      <dl class="info-list">
        <div class="info-item">
          <dt><?= $t['joined_date'] ?></dt>
          <dd><?= formatDate($driver['created_at'] ?? '', 'M d, Y') ?></dd>
        </div>
        <div class="info-item">
          <dt><?= $t['last_location'] ?></dt>
          <dd>
            <?php if (!empty($driver['last_location_update'])): ?>
              <?= formatDate($driver['last_location_update'], 'M d, Y h:i A') ?>
            <?php else: ?>
              <?= $t['never'] ?>
            <?php endif; ?>
          </dd>
        </div>
        <div class="info-item">
          <dt><?= $t['total_distance'] ?></dt>
          <dd><?= number_format($stats['total_distance'] ?? 0, 1) ?> km</dd>
        </div>
      </dl>
    </div>
  </div>

  <!-- Background Check -->
  <?php
  $bgStatus     = $bgcheck['bgcheck_status'] ?? 'not_requested';
  $bgFile       = $bgcheck['bgcheck_file_path'] ?? null;
  $bgAppId      = $bgcheck['id'] ?? null;
  $bgStatusMap  = [
      'not_requested' => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => $currentLang === 'fr' ? 'Non demandé'          : 'Not Requested'],
      'requested'     => ['bg' => '#eff6ff', 'color' => '#2563eb', 'label' => $currentLang === 'fr' ? 'Lien envoyé'          : 'Link Sent'],
      'uploaded'      => ['bg' => '#fef3c7', 'color' => '#b45309', 'label' => $currentLang === 'fr' ? 'En attente de révision' : 'Awaiting Review'],
      'verified'      => ['bg' => '#dcfce7', 'color' => '#16a34a', 'label' => $currentLang === 'fr' ? 'Vérifié'              : 'Verified'],
      'flagged'       => ['bg' => '#fee2e2', 'color' => '#dc2626', 'label' => $currentLang === 'fr' ? 'Signalé'              : 'Flagged'],
      'waived'        => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'label' => $currentLang === 'fr' ? 'Dispensé'             : 'Waived'],
  ];
  $bgSc = $bgStatusMap[$bgStatus] ?? $bgStatusMap['not_requested'];
  ?>
  <div class="table-card" style="margin-bottom: 24px;">
    <div class="table-header" style="display:flex; align-items:center; justify-content:space-between;">
      <h3><i class="fas fa-user-shield" style="color:#2563eb; margin-right:6px;"></i> <?= $t['bgcheck'] ?></h3>
      <span style="font-size:12px; font-weight:600; padding:4px 12px; border-radius:12px; background:<?= $bgSc['bg'] ?>; color:<?= $bgSc['color'] ?>;"><?= $bgSc['label'] ?></span>
    </div>
    <div style="padding: 20px;">
      <?php if (!$bgAppId || in_array($bgStatus, ['not_requested', 'requested'])): ?>
        <p style="font-size:13px; color:#9ca3af; font-style:italic; margin-bottom:12px;"><?= $t['bgcheck_not_sub'] ?></p>
        <?php if ($bgAppId): ?>
        <button id="reqBgcheckBtn" onclick="requestBgcheck()"
                style="padding:7px 16px; background:#f5f3ff; color:#6d28d9; border:1px solid #ddd8fe; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit; display:inline-flex; align-items:center; gap:6px;">
          <i class="fas fa-paper-plane"></i>
          <?= $bgStatus === 'requested' ? $t['bgcheck_resend'] : $t['bgcheck_request'] ?>
        </button>
        <?php endif; ?>

      <?php else: ?>
        <?php if ($bgcheck['bgcheck_uploaded_at']): ?>
        <div style="font-size:12px; color:#6b7280; margin-bottom:10px;">
          <?= $t['bgcheck_uploaded'] ?> <?= date('M j, Y', strtotime($bgcheck['bgcheck_uploaded_at'])) ?>
          <?php if ($bgStatus === 'verified' && $bgcheck['bgcheck_verified_at']): ?>
            &nbsp;·&nbsp; <?= $t['bgcheck_verified'] ?> <?= date('M j, Y', strtotime($bgcheck['bgcheck_verified_at'])) ?>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($bgcheck['bgcheck_notes'])): ?>
        <div style="font-size:12px; background:#fffbeb; border:1px solid #fde68a; border-radius:6px; padding:6px 10px; color:#92400e; margin-bottom:10px;">
          <strong><?= $t['bgcheck_notes_lbl'] ?></strong> <?= htmlspecialchars($bgcheck['bgcheck_notes']) ?>
        </div>
        <?php endif; ?>

        <?php if ($bgFile && in_array($bgStatus, ['uploaded', 'verified', 'flagged'])): ?>
        <a href="<?= url('admin/delivery/bgcheck/download?app_id=' . $bgAppId) ?>" target="_blank"
           style="display:inline-flex; align-items:center; gap:5px; font-size:12px; color:#2563eb; text-decoration:none; font-weight:600; margin-bottom:12px;">
          <i class="fas fa-eye"></i> <?= $t['bgcheck_view'] ?>
        </a>
        <?php endif; ?>

        <?php if ($bgAppId && in_array($bgStatus, ['uploaded', 'flagged'])): ?>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
          <button onclick="reviewBgcheck('verify')"
                  style="padding:6px 14px; background:#16a34a; color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit;">
            <i class="fas fa-check"></i> <?= $t['bgcheck_verify'] ?>
          </button>
          <?php if ($bgStatus !== 'flagged'): ?>
          <button onclick="reviewBgcheck('flag')"
                  style="padding:6px 14px; background:#dc2626; color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit;">
            <i class="fas fa-flag"></i> <?= $t['bgcheck_flag'] ?>
          </button>
          <?php endif; ?>
          <button onclick="reviewBgcheck('waive')"
                  style="padding:6px 14px; background:#6b7280; color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit;">
            <i class="fas fa-ban"></i> <?= $t['bgcheck_waive'] ?>
          </button>
        </div>
        <?php elseif ($bgStatus === 'verified'): ?>
        <div style="font-size:12px; color:#16a34a;"><i class="fas fa-shield-check"></i> <?= $bgSc['label'] ?></div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Bgcheck review modal -->
  <div id="bgcheckModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:28px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,.2);">
      <h3 style="font-size:16px; font-weight:700; color:#111827; margin-bottom:6px;" id="bcmTitle"></h3>
      <div style="margin-bottom:14px;">
        <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;"><?= $t['notes_field'] ?> <span id="bcmNotesRequired" style="color:#dc2626; display:none;">*</span></label>
        <textarea id="bcmNotes" rows="3" placeholder="<?= htmlspecialchars($t['notes_ph']) ?>"
                  style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; font-family:inherit; resize:vertical;"></textarea>
      </div>
      <div style="display:flex; gap:10px; justify-content:flex-end;">
        <button type="button" onclick="document.getElementById('bgcheckModal').style.display='none'"
                style="padding:9px 18px; background:#f3f4f6; border:1px solid #d1d5db; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; font-family:inherit;"><?= $t['btn_cancel'] ?></button>
        <button type="button" id="bcmConfirmBtn" onclick="submitBgcheckReview()"
                style="padding:9px 18px; background:#1e40af; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit;"><?= $t['btn_confirm'] ?></button>
      </div>
    </div>
  </div>
  <script>
  let _bcmAction = null;
  const _bgAppId = <?= json_encode($bgAppId) ?>;
  const _bcmLabels = {
      verify: <?= json_encode($t['bgcheck_js_verify']) ?>,
      flag:   <?= json_encode($t['bgcheck_js_flag']) ?>,
      waive:  <?= json_encode($t['bgcheck_js_waive']) ?>,
  };
  const _bcmColors = { verify: '#16a34a', flag: '#dc2626', waive: '#6b7280' };
  function reviewBgcheck(action) {
      _bcmAction = action;
      document.getElementById('bcmTitle').textContent = _bcmLabels[action];
      document.getElementById('bcmConfirmBtn').style.background = _bcmColors[action];
      document.getElementById('bcmNotesRequired').style.display = action === 'flag' ? 'inline' : 'none';
      document.getElementById('bcmNotes').value = '';
      document.getElementById('bgcheckModal').style.display = 'flex';
  }
  async function requestBgcheck() {
    const btn = document.getElementById('reqBgcheckBtn');
    btn.disabled = true;
    const fd = new FormData();
    fd.append('application_id', '<?= (int)($bgAppId ?? 0) ?>');
    fd.append('_csrf_token', document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value || '');
    try {
      const res  = await fetch('<?= url('admin/delivery/bgcheck/request') ?>', { method:'POST', body:fd });
      const data = await res.json();
      if (data.success) { alert('✅ ' + data.message); location.reload(); }
      else { alert(data.error || 'Failed to send.'); btn.disabled = false; }
    } catch(e) { alert('Network error.'); btn.disabled = false; }
  }
  async function requestComplianceDocs() {
    const btn = document.getElementById('reqComplianceBtn');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    const fd = new FormData();
    fd.append('driver_id', '<?= (int)($driver['id'] ?? 0) ?>');
    fd.append('_csrf_token', document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value || '');
    try {
      const res  = await fetch('<?= url('admin/delivery/compliance/request') ?>', { method:'POST', body:fd });
      const data = await res.json();
      if (data.success) { btn.innerHTML = '<i class="fas fa-check"></i> <?= $t['compliance_req_sent'] ?>'; btn.style.background = '#f0fdf4'; btn.style.color = '#16a34a'; btn.style.borderColor = '#bbf7d0'; }
      else { alert(data.error || 'Failed.'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane"></i> <?= $t['compliance_request'] ?>'; }
    } catch(e) { alert('Network error.'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane"></i> <?= $t['compliance_request'] ?>'; }
  }
  function submitBgcheckReview() {
      const notes = document.getElementById('bcmNotes').value.trim();
      if (_bcmAction === 'flag' && !notes) { alert(<?= json_encode($t['js_flag_reason']) ?>); return; }
      const btn = document.getElementById('bcmConfirmBtn');
      btn.disabled = true; btn.textContent = <?= json_encode($t['js_saving']) ?>;
      const statusMap = { verify: 'verified', flag: 'flagged', waive: 'waived' };
      fetch('<?= url('admin/delivery/bgcheck/verify') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '' },
          body: new URLSearchParams({ application_id: _bgAppId, status: statusMap[_bcmAction], notes: notes, _token: document.querySelector('input[name="_token"]')?.value || '' })
      })
      .then(r => r.json())
      .then(data => {
          if (data.success) { location.reload(); }
          else { alert(data.error || <?= json_encode($t['js_failed']) ?>); btn.disabled = false; btn.textContent = <?= json_encode($t['btn_confirm']) ?>; }
      })
      .catch(() => { alert(<?= json_encode($t['js_network']) ?>); btn.disabled = false; btn.textContent = <?= json_encode($t['btn_confirm']) ?>; });
  }
  </script>

  <!-- Compliance Documents -->
  <div class="table-card" style="margin-bottom: 24px;">
    <div class="table-header" style="display:flex; align-items:center; justify-content:space-between;">
      <h3><i class="fas fa-file-shield" style="color:#2563eb; margin-right:6px;"></i> <?= $t['compliance'] ?></h3>
      <button id="reqComplianceBtn" onclick="requestComplianceDocs()"
              style="padding:6px 14px; background:#f5f3ff; color:#6d28d9; border:1px solid #ddd8fe; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit; display:inline-flex; align-items:center; gap:6px;">
        <i class="fas fa-paper-plane"></i> <?= $t['compliance_request'] ?>
      </button>
    </div>
    <div style="padding: 20px; display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px;">
      <?php foreach ($complianceDocDefs as $type => $def):
        $doc = ($complianceDocs ?? [])[$type] ?? null;
        $status = $doc['status'] ?? 'not_uploaded';
        $sc = $cdStatusColors[$status] ?? $cdStatusColors['not_uploaded'];
      ?>
      <div style="border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
        <div style="background: #f9fafb; padding: 12px 16px; border-bottom: 1px solid #e5e7eb; display:flex; align-items:center; gap:10px;">
          <i class="fas fa-<?= $def['icon'] ?>" style="color:#2563eb; width:16px; text-align:center;"></i>
          <span style="font-size:13px; font-weight:700; color:#111827; flex:1;"><?= htmlspecialchars($def['label']) ?></span>
          <span style="font-size:11px; font-weight:600; padding:3px 10px; border-radius:12px; background:<?= $sc['bg'] ?>; color:<?= $sc['color'] ?>;"><?= $sc['label'] ?></span>
        </div>
        <div style="padding: 12px 16px;">
          <?php if ($doc && $doc['uploaded_at']): ?>
            <div style="font-size:11px; color:#6b7280; margin-bottom:8px;">
              <?= $t['submitted'] ?> <?= date('M j, Y', strtotime($doc['uploaded_at'])) ?>
              <?php if ($doc['doc_date']): ?> &nbsp;·&nbsp; <?= $t['doc_date_lbl'] ?> <?= date('M j, Y', strtotime($doc['doc_date'])) ?><?php endif; ?>
              <?php if ($doc['doc_subtype']): ?> &nbsp;·&nbsp; <?= htmlspecialchars($doc['doc_subtype']) ?><?php endif; ?>
            </div>
          <?php endif; ?>
          <?php if (!empty($doc['admin_notes'])): ?>
            <div style="font-size:11px; background:#fffbeb; border:1px solid #fde68a; border-radius:6px; padding:6px 10px; color:#92400e; margin-bottom:8px;">
              <strong><?= $t['notes_lbl'] ?></strong> <?= htmlspecialchars($doc['admin_notes']) ?>
            </div>
          <?php endif; ?>

          <?php if ($doc && $doc['file_path'] && in_array($status, ['uploaded', 'verified', 'flagged'])): ?>
            <a href="<?= url('admin/delivery/compliance/download?doc_id=' . $doc['id']) ?>" target="_blank"
               style="display:inline-flex; align-items:center; gap:5px; font-size:12px; color:#2563eb; text-decoration:none; font-weight:600; margin-bottom:8px;">
              <i class="fas fa-eye"></i> <?= $t['view_doc'] ?>
            </a>
          <?php endif; ?>

          <?php if ($doc && in_array($status, ['uploaded', 'flagged', 'not_required']) && !empty($driverAppId)): ?>
            <!-- Review actions -->
            <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:6px;">
              <?php if ($status !== 'verified'): ?>
              <button onclick="reviewDoc(<?= $doc['id'] ?>, 'verify', '<?= addslashes($def['label']) ?>')"
                      style="padding:5px 12px; background:#16a34a; color:#fff; border:none; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; font-family:inherit;">
                <i class="fas fa-check"></i> <?= $t['btn_verify'] ?>
              </button>
              <?php endif; ?>
              <?php if ($status !== 'flagged'): ?>
              <button onclick="reviewDoc(<?= $doc['id'] ?>, 'flag', '<?= addslashes($def['label']) ?>')"
                      style="padding:5px 12px; background:#dc2626; color:#fff; border:none; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; font-family:inherit;">
                <i class="fas fa-flag"></i> <?= $t['btn_flag'] ?>
              </button>
              <?php endif; ?>
              <?php if ($status !== 'not_required'): ?>
              <button onclick="reviewDoc(<?= $doc['id'] ?>, 'waive', '<?= addslashes($def['label']) ?>')"
                      style="padding:5px 12px; background:#6b7280; color:#fff; border:none; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; font-family:inherit;">
                <i class="fas fa-ban"></i> <?= $t['btn_waive'] ?>
              </button>
              <?php endif; ?>
            </div>
          <?php elseif (!$doc || $status === 'not_uploaded'): ?>
            <div style="font-size:12px; color:#9ca3af; font-style:italic;"><?= $t['awaiting_sub'] ?></div>
          <?php elseif ($status === 'verified'): ?>
            <div style="font-size:12px; color:#16a34a;">
              <i class="fas fa-shield-check"></i> <?= $cdStatusColors['verified']['label'] ?> <?= $doc['verified_at'] ? date('M j, Y', strtotime($doc['verified_at'])) : '' ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Review modal -->
  <div id="reviewDocModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:28px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,.2);">
      <h3 style="font-size:16px; font-weight:700; color:#111827; margin-bottom:6px;" id="rdmTitle"></h3>
      <p style="font-size:13px; color:#6b7280; margin-bottom:16px;" id="rdmDesc"></p>
      <div style="margin-bottom:14px;">
        <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;"><?= $t['notes_field'] ?> <span id="rdmNotesRequired" style="color:#dc2626; display:none;">*</span></label>
        <textarea id="rdmNotes" rows="3" placeholder="<?= htmlspecialchars($t['notes_ph']) ?>"
                  style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; font-family:inherit; resize:vertical;"></textarea>
      </div>
      <div style="display:flex; gap:10px; justify-content:flex-end;">
        <button type="button" onclick="closeReviewModal()" style="padding:9px 18px; background:#f3f4f6; border:1px solid #d1d5db; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; font-family:inherit;"><?= $t['btn_cancel'] ?></button>
        <button type="button" id="rdmConfirmBtn" onclick="submitReview()" style="padding:9px 18px; background:#1e40af; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit;"><?= $t['btn_confirm'] ?></button>
      </div>
    </div>
  </div>

  <script>
  let _rdmDocId = null, _rdmAction = null;
  const _rdmT = {
      verify: <?= json_encode($t['js_verify']) ?>,
      flag:   <?= json_encode($t['js_flag']) ?>,
      waive:  <?= json_encode($t['js_waive']) ?>,
  };
  function reviewDoc(docId, action, label) {
      _rdmDocId  = docId;
      _rdmAction = action;
      const titles = { verify: _rdmT.verify, flag: _rdmT.flag, waive: _rdmT.waive };
      const descs  = {
          verify: label + (<?= $currentLang === 'fr' ? 'true' : 'false' ?> ? ' — marquer comme vérifié.' : ' — mark as verified.'),
          flag:   label + (<?= $currentLang === 'fr' ? 'true' : 'false' ?> ? ' — le livreur devra télécharger à nouveau.' : ' — the driver will be asked to re-upload.'),
          waive:  label + (<?= $currentLang === 'fr' ? 'true' : 'false' ?> ? ' — dispenser cette exigence.' : ' — waive this requirement.'),
      };
      const colors = { verify: '#16a34a', flag: '#dc2626', waive: '#6b7280' };
      document.getElementById('rdmTitle').textContent = titles[action];
      document.getElementById('rdmDesc').textContent  = descs[action];
      document.getElementById('rdmConfirmBtn').style.background = colors[action];
      document.getElementById('rdmNotesRequired').style.display = action === 'flag' ? 'inline' : 'none';
      document.getElementById('rdmNotes').value = '';
      document.getElementById('reviewDocModal').style.display = 'flex';
  }
  function closeReviewModal() { document.getElementById('reviewDocModal').style.display = 'none'; }
  function submitReview() {
      const notes = document.getElementById('rdmNotes').value.trim();
      if (_rdmAction === 'flag' && !notes) { alert(<?= json_encode($t['js_flag_reason']) ?>); return; }
      const btn = document.getElementById('rdmConfirmBtn');
      btn.disabled = true; btn.textContent = <?= json_encode($t['js_saving']) ?>;
      fetch('<?= url('admin/delivery/compliance/review') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '' },
          body: new URLSearchParams({ doc_id: _rdmDocId, action: _rdmAction, notes: notes, _token: document.querySelector('input[name="_token"]')?.value || '' })
      })
      .then(r => r.json())
      .then(data => {
          if (data.success) { location.reload(); }
          else { alert(data.error || <?= json_encode($t['js_failed']) ?>); btn.disabled = false; btn.textContent = <?= json_encode($t['btn_confirm']) ?>; }
      })
      .catch(() => { alert(<?= json_encode($t['js_network']) ?>); btn.disabled = false; btn.textContent = <?= json_encode($t['btn_confirm']) ?>; });
  }
  </script>

  <!-- Payment Information -->
  <?php
  $payPref   = $driverPayment['payment_preference'] ?? null;
  $payLabels = ['eft' => $t['pay_eft'], 'interac' => $t['pay_interac_lbl'], 'cheque' => $t['pay_cheque']];
  $payColors = ['eft' => ['bg' => '#eff6ff', 'color' => '#2563eb'], 'interac' => ['bg' => '#f0fdf4', 'color' => '#16a34a'], 'cheque' => ['bg' => '#fef3c7', 'color' => '#b45309']];
  $paySc     = $payPref ? ($payColors[$payPref] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280']) : null;
  ?>
  <div class="table-card" style="margin-bottom: 24px;">
    <div class="table-header" style="display:flex; align-items:center; justify-content:space-between;">
      <h3><i class="fas fa-university" style="color:#2563eb; margin-right:6px;"></i> <?= $t['payment_info'] ?></h3>
      <?php if ($payPref && $paySc): ?>
        <span style="font-size:12px; font-weight:600; padding:4px 12px; border-radius:12px; background:<?= $paySc['bg'] ?>; color:<?= $paySc['color'] ?>;">
          <?= htmlspecialchars($payLabels[$payPref] ?? ucfirst($payPref)) ?>
        </span>
      <?php endif; ?>
    </div>
    <div style="padding: 20px;">
      <?php if (empty($driverPayment) || !$payPref): ?>
        <p style="font-size:13px; color:#9ca3af; font-style:italic;"><?= $t['pay_none'] ?></p>
      <?php else: ?>
        <dl style="display:grid; grid-template-columns: 160px 1fr; gap:8px 16px; font-size:13px; margin:0;">
          <dt style="color:#6b7280; font-weight:600;"><?= $t['pay_method'] ?></dt>
          <dd style="margin:0; color:#111827; font-weight:600;"><?= htmlspecialchars($payLabels[$payPref] ?? ucfirst($payPref)) ?></dd>

          <?php if ($payPref === 'eft'): ?>
            <?php if (!empty($driverPayment['bank_name'])): ?>
            <dt style="color:#6b7280;"><?= $t['pay_bank'] ?></dt>
            <dd style="margin:0; color:#111827;"><?= htmlspecialchars($driverPayment['bank_name']) ?></dd>
            <?php endif; ?>
            <?php if (!empty($driverPayment['bank_account_holder'])): ?>
            <dt style="color:#6b7280;"><?= $t['pay_holder'] ?></dt>
            <dd style="margin:0; color:#111827;"><?= htmlspecialchars($driverPayment['bank_account_holder']) ?></dd>
            <?php endif; ?>
            <?php if (!empty($driverPayment['bank_transit'])): ?>
            <dt style="color:#6b7280;"><?= $t['pay_transit'] ?></dt>
            <dd style="margin:0; color:#111827; font-family:monospace;"><?= htmlspecialchars($driverPayment['bank_transit']) ?></dd>
            <?php endif; ?>
            <?php if (!empty($driverPayment['bank_institution'])): ?>
            <dt style="color:#6b7280;"><?= $t['pay_institution'] ?></dt>
            <dd style="margin:0; color:#111827; font-family:monospace;"><?= htmlspecialchars($driverPayment['bank_institution']) ?></dd>
            <?php endif; ?>
            <?php if (!empty($driverPayment['bank_account'])): ?>
            <dt style="color:#6b7280;"><?= $t['pay_account'] ?></dt>
            <dd style="margin:0; color:#111827; font-family:monospace;">****<?= htmlspecialchars(substr($driverPayment['bank_account'], -4)) ?></dd>
            <?php endif; ?>
            <?php if (!empty($driverPayment['bank_account_type'])): ?>
            <dt style="color:#6b7280;"><?= $t['pay_type'] ?></dt>
            <dd style="margin:0; color:#111827;"><?= htmlspecialchars(ucfirst($driverPayment['bank_account_type'])) ?></dd>
            <?php endif; ?>

          <?php elseif ($payPref === 'interac'): ?>
            <?php if (!empty($driverPayment['interac_email'])): ?>
            <dt style="color:#6b7280;"><?= $t['pay_interac'] ?></dt>
            <dd style="margin:0; color:#111827;"><?= htmlspecialchars($driverPayment['interac_email']) ?></dd>
            <?php endif; ?>
          <?php endif; ?>

          <?php if (!empty($driverPayment['updated_at'])): ?>
          <dt style="color:#6b7280;"><?= $t['pay_updated'] ?></dt>
          <dd style="margin:0; color:#6b7280; font-size:12px;"><?= date('M j, Y', strtotime($driverPayment['updated_at'])) ?></dd>
          <?php endif; ?>
        </dl>
      <?php endif; ?>
    </div>
  </div>

  <!-- Performance Score Card -->
  <?php
  $avgScore    = isset($driverScore['avg_score']) && $driverScore['avg_score'] !== null ? (float)$driverScore['avg_score'] : null;
  $totalScored = (int)($driverScore['total_scored'] ?? 0);
  $scoreLbl    = $avgScore !== null ? \App\Services\ScoringService::scoreLabel((int)round($avgScore)) : null;
  ?>
  <div class="table-card" style="margin-bottom:24px;">
    <div class="table-header" style="border-bottom:1px solid #f0f0f0;padding-bottom:14px;margin-bottom:16px;">
      <h3 style="display:flex;align-items:center;gap:8px;">
        <i class="fas fa-chart-bar" style="color:#6366f1;"></i> <?= $t['perf_score'] ?>
        <span style="font-size:13px;font-weight:400;color:#888;"><?= $t['perf_rolling'] ?></span>
      </h3>
    </div>

    <?php if ($avgScore !== null): ?>
    <!-- Summary row -->
    <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px;flex-wrap:wrap;">
      <div style="text-align:center;min-width:90px;">
        <div style="font-size:48px;font-weight:800;color:<?= $scoreLbl['color'] ?>;line-height:1;"><?= number_format($avgScore, 1) ?></div>
        <div style="font-size:12px;color:#888;margin-top:4px;">/ 100</div>
      </div>
      <div style="flex:1;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
          <span style="padding:4px 14px;border-radius:20px;background:<?= $scoreLbl['bg'] ?>;color:<?= $scoreLbl['color'] ?>;font-weight:700;font-size:14px;"><?= $scoreLbl['label'] ?></span>
          <span style="font-size:13px;color:#888;"><?= $t['based_on'] ?> <?= $totalScored ?> <?= $t['scored_del'] ?></span>
        </div>
        <div style="height:10px;background:#f0f0f0;border-radius:5px;overflow:hidden;">
          <div style="height:100%;width:<?= min(100, round($avgScore)) ?>%;background:<?= $scoreLbl['color'] ?>;border-radius:5px;transition:width .5s;"></div>
        </div>
      </div>
    </div>

    <!-- Score breakdown legend -->
    <div style="display:flex;gap:16px;flex-wrap:wrap;padding:12px 0;border-top:1px solid #f3f4f6;margin-bottom:16px;font-size:12px;color:#555;">
      <span>⚡ <strong><?= $t['resp_speed'] ?></strong> — <?= $t['resp_desc'] ?></span>
      <span>⏱ <strong><?= $t['timeliness'] ?></strong> — <?= $t['time_desc'] ?></span>
      <span>✅ <strong><?= $t['completion'] ?></strong> — <?= $t['comp_desc'] ?></span>
    </div>

    <!-- Recent individual scores -->
    <?php if (!empty($recentScores)): ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
      <thead>
        <tr style="background:#f9fafb;">
          <th style="padding:8px 12px;text-align:left;font-size:11px;color:#888;text-transform:uppercase;font-weight:600;"><?= $t['col_request'] ?></th>
          <th style="padding:8px 12px;text-align:center;font-size:11px;color:#888;text-transform:uppercase;font-weight:600;"><?= $t['col_score'] ?></th>
          <th style="padding:8px 12px;text-align:center;font-size:11px;color:#888;text-transform:uppercase;font-weight:600;"><?= $t['col_response'] ?></th>
          <th style="padding:8px 12px;text-align:center;font-size:11px;color:#888;text-transform:uppercase;font-weight:600;"><?= $t['col_timeliness'] ?></th>
          <th style="padding:8px 12px;text-align:center;font-size:11px;color:#888;text-transform:uppercase;font-weight:600;"><?= $t['col_completion'] ?></th>
          <th style="padding:8px 12px;text-align:left;font-size:11px;color:#888;text-transform:uppercase;font-weight:600;"><?= $t['col_date'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentScores as $sc):
          $sl = \App\Services\ScoringService::scoreLabel($sc['total_score']);
        ?>
        <tr style="border-bottom:1px solid #f3f4f6;">
          <td style="padding:9px 12px;">
            <?php if (!empty($sc['request_number'])): ?>
              <a href="<?= url('admin/distribution/view?id=' . $sc['distribution_request_id']) ?>" style="color:#6366f1;font-weight:600;text-decoration:none;">
                #<?= htmlspecialchars($sc['request_number']) ?>
              </a>
            <?php else: ?>
              <span style="color:#aaa;">—</span>
            <?php endif; ?>
          </td>
          <td style="padding:9px 12px;text-align:center;">
            <span style="padding:2px 10px;border-radius:20px;background:<?= $sl['bg'] ?>;color:<?= $sl['color'] ?>;font-weight:700;"><?= $sc['total_score'] ?></span>
          </td>
          <td style="padding:9px 12px;text-align:center;color:#444;"><?= $sc['response_score'] ?></td>
          <td style="padding:9px 12px;text-align:center;color:#444;"><?= $sc['timeliness_score'] ?></td>
          <td style="padding:9px 12px;text-align:center;color:#444;"><?= $sc['completion_score'] ?></td>
          <td style="padding:9px 12px;color:#888;"><?= !empty($sc['calculated_at']) ? date('M j, Y', strtotime($sc['calculated_at'])) : '—' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

    <?php else: ?>
    <div style="text-align:center;padding:28px;color:#aaa;">
      <i class="fas fa-chart-bar" style="font-size:32px;margin-bottom:10px;display:block;"></i>
      <?= $t['no_scores'] ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Recent Deliveries Table -->
  <div class="table-card">
    <div class="table-header">
      <h3><?= $t['recent_del'] ?> (<?= count($recent_deliveries ?? []) ?>)</h3>
    </div>
    <?php if (!empty($recent_deliveries)): ?>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th><?= $t['col_order'] ?></th>
              <th><?= $t['col_customer'] ?></th>
              <th><?= $t['col_shop'] ?></th>
              <th><?= $t['col_fee'] ?></th>
              <th><?= $t['col_status'] ?></th>
              <th><?= $t['col_date'] ?></th>
              <th><?= $t['col_rating'] ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_deliveries as $delivery): ?>
              <tr>
                <td><strong>#<?= htmlspecialchars($delivery['order_number'] ?? 'N/A') ?></strong></td>
                <td><?= htmlspecialchars($delivery['customer_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($delivery['shop_name'] ?? 'N/A') ?></td>
                <td><?= currency($delivery['delivery_fee'] ?? 0) ?></td>
                <td>
                  <span class="badge <?= $delivery['status'] ?? 'assigned' ?>">
                    <?= ucfirst(str_replace('_', ' ', $delivery['status'] ?? 'Assigned')) ?>
                  </span>
                </td>
                <td><?= formatDate($delivery['created_at'] ?? '', 'M d, Y') ?></td>
                <td>
                  <?php if (!empty($delivery['rating'])): ?>
                    <?= number_format($delivery['rating'], 1) ?> ⭐
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <div class="empty-icon">
          <i class="fas fa-truck"></i>
        </div>
        <p class="empty-text"><?= $t['no_deliveries'] ?></p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
