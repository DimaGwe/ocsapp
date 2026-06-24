<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = ([
    'en' => [
        'portal_sub'        => 'Distribution Portal',
        'back_routes'       => 'Back to Routes',
        'created'           => 'Created',
        'btn_edit'          => 'Edit',
        'btn_pause'         => 'Pause',
        'btn_resume'        => 'Resume',
        'btn_cancel'        => 'Cancel',
        'confirm_cancel'    => 'Are you sure you want to cancel this route?',
        'status_active'     => 'Active',
        'status_paused'     => 'Paused',
        'status_cancelled'  => 'Cancelled',
        'status_draft'      => 'Draft',
        'route_is'          => 'Route is',
        'next_gen_on'       => 'Next shipment will be generated on',
        'paused_desc'       => 'Route is paused. Resume to continue generating shipments.',
        'cancelled_desc'    => 'Route was cancelled and will no longer generate shipments.',
        'sec_details'       => 'Route Details',
        'lbl_name'          => 'Route Name',
        'lbl_frequency'     => 'Frequency',
        'lbl_start'         => 'Start Date',
        'lbl_end'           => 'End Date',
        'no_end_date'       => 'No end date',
        'lbl_days'          => 'Days of Week',
        'lbl_day_month'     => 'Day of Month',
        'not_specified'     => 'Not specified',
        'sec_pickup'        => 'Pickup Location',
        'pickup_address'    => 'Pickup Address',
        'pickup_window'     => 'Pickup Window',
        'sec_dest'          => 'Destinations',
        'stop_prefix'       => 'Stop ',
        'no_destinations'   => 'No destinations configured',
        'sec_settings'      => 'Settings',
        'lbl_auto_submit'   => 'Auto-Submit',
        'enabled'           => 'Enabled',
        'disabled'          => 'Disabled',
        'lbl_notify_before' => 'Notify Before',
        'days'              => 'day(s)',
        'lbl_next_gen'      => 'Next Generation',
        'sec_shipments'     => 'Recent Shipments',
        'no_shipments'      => 'No shipments generated yet',
        'freq_daily'        => 'Daily',
        'freq_weekly'       => 'Weekly',
        'freq_biweekly'     => 'Bi-Weekly',
        'freq_monthly'      => 'Monthly',
    ],
    'fr' => [
        'portal_sub'        => 'Portail de Distribution',
        'back_routes'       => 'Retour aux routes',
        'created'           => 'Créé',
        'btn_edit'          => 'Modifier',
        'btn_pause'         => 'Mettre en pause',
        'btn_resume'        => 'Reprendre',
        'btn_cancel'        => 'Annuler',
        'confirm_cancel'    => 'Êtes-vous sûr de vouloir annuler cette route ?',
        'status_active'     => 'Actif',
        'status_paused'     => 'En pause',
        'status_cancelled'  => 'Annulé',
        'status_draft'      => 'Brouillon',
        'route_is'          => 'La route est',
        'next_gen_on'       => 'Le prochain envoi sera généré le',
        'paused_desc'       => 'La route est en pause. Reprenez pour continuer à générer des envois.',
        'cancelled_desc'    => "La route a été annulée et ne générera plus d'envois.",
        'sec_details'       => 'Détails de la route',
        'lbl_name'          => 'Nom de la route',
        'lbl_frequency'     => 'Fréquence',
        'lbl_start'         => 'Date de début',
        'lbl_end'           => 'Date de fin',
        'no_end_date'       => 'Pas de date de fin',
        'lbl_days'          => 'Jours de la semaine',
        'lbl_day_month'     => 'Jour du mois',
        'not_specified'     => 'Non spécifié',
        'sec_pickup'        => 'Lieu de ramassage',
        'pickup_address'    => 'Adresse de ramassage',
        'pickup_window'     => 'Plage de ramassage',
        'sec_dest'          => 'Destinations',
        'stop_prefix'       => 'Arrêt ',
        'no_destinations'   => 'Aucune destination configurée',
        'sec_settings'      => 'Paramètres',
        'lbl_auto_submit'   => 'Soumission auto',
        'enabled'           => 'Activé',
        'disabled'          => 'Désactivé',
        'lbl_notify_before' => 'Avertir avant',
        'days'              => 'jour(s)',
        'lbl_next_gen'      => 'Prochaine génération',
        'sec_shipments'     => 'Envois récents',
        'no_shipments'      => "Aucun envoi généré pour l'instant",
        'freq_daily'        => 'Quotidien',
        'freq_weekly'       => 'Hebdomadaire',
        'freq_biweekly'     => 'Bihebdomadaire',
        'freq_monthly'      => 'Mensuel',
    ],
])[$currentLang] ?? [];

$statusLabels = [
    'active'    => $t['status_active'],
    'paused'    => $t['status_paused'],
    'cancelled' => $t['status_cancelled'],
    'draft'     => $t['status_draft'],
];
$statusLabel = $statusLabels[$route['status']] ?? ucfirst($route['status']);

$freqLabels = [
    'daily'    => $t['freq_daily'],
    'weekly'   => $t['freq_weekly'],
    'biweekly' => $t['freq_biweekly'],
    'monthly'  => $t['freq_monthly'],
];
$freqLabel = $freqLabels[$route['frequency']] ?? ucfirst($route['frequency']);

$currentPage = 'routes';
$pageTitle = $currentLang === 'fr' ? 'Détails de la route' : 'Route Details';
$_pageT = $t; // preserve before layout-header.php overwrites $t
require __DIR__ . '/../layout-header.php';
$t = $_pageT; unset($_pageT); // restore page-specific translations
?>

        <a href="<?= url('distribution/routes') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> <?= $t['back_routes'] ?>
        </a>

        <div class="page-header">
            <div>
                <h1 class="page-title"><?= htmlspecialchars($route['route_name']) ?></h1>
                <p class="page-subtitle"><?= $t['created'] ?> <?= date('M j, Y', strtotime($route['created_at'])) ?></p>
            </div>
            <div class="header-actions">
                <?php if ($route['status'] === 'active' || $route['status'] === 'paused'): ?>
                    <a href="<?= url('distribution/routes/edit?id=' . $route['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> <?= $t['btn_edit'] ?>
                    </a>
                <?php endif; ?>
                <?php if ($route['status'] === 'active'): ?>
                    <form action="<?= url('distribution/routes/pause') ?>" method="POST" style="display:inline;">
                        <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="route_id" value="<?= $route['id'] ?>">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-pause"></i> <?= $t['btn_pause'] ?></button>
                    </form>
                <?php elseif ($route['status'] === 'paused'): ?>
                    <form action="<?= url('distribution/routes/resume') ?>" method="POST" style="display:inline;">
                        <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="route_id" value="<?= $route['id'] ?>">
                        <button type="submit" class="btn btn-success"><i class="fas fa-play"></i> <?= $t['btn_resume'] ?></button>
                    </form>
                <?php endif; ?>
                <?php if ($route['status'] !== 'cancelled'): ?>
                    <form action="<?= url('distribution/routes/cancel') ?>" method="POST" style="display:inline;"
                          onsubmit="return confirm(<?= htmlspecialchars(json_encode($t['confirm_cancel']), ENT_QUOTES) ?>)">
                        <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="route_id" value="<?= $route['id'] ?>">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> <?= $t['btn_cancel'] ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="status-banner <?= $route['status'] ?>">
            <div class="status-info">
                <h3><?= $t['route_is'] ?> <?= $statusLabel ?></h3>
                <p>
                    <?php if ($route['status'] === 'active' && !empty($route['next_generation_date'])): ?>
                        <?= $t['next_gen_on'] ?> <?= date('M j, Y', strtotime($route['next_generation_date'])) ?>
                    <?php elseif ($route['status'] === 'paused'): ?>
                        <?= $t['paused_desc'] ?>
                    <?php elseif ($route['status'] === 'cancelled'): ?>
                        <?= $t['cancelled_desc'] ?>
                    <?php endif; ?>
                </p>
            </div>
            <span class="badge badge-<?= $route['status'] ?>"><?= $statusLabel ?></span>
        </div>

        <div class="content-grid">
            <!-- Left Column -->
            <div>
                <!-- Route Details -->
                <div class="section-card">
                    <div class="section-title"><i class="fas fa-route"></i> <?= $t['sec_details'] ?></div>
                    <div class="info-grid">
                        <div class="info-group">
                            <div class="info-label"><?= $t['lbl_name'] ?></div>
                            <div class="info-value"><?= htmlspecialchars($route['route_name']) ?></div>
                        </div>
                        <div class="info-group">
                            <div class="info-label"><?= $t['lbl_frequency'] ?></div>
                            <div class="info-value"><?= $freqLabel ?></div>
                        </div>
                        <div class="info-group">
                            <div class="info-label"><?= $t['lbl_start'] ?></div>
                            <div class="info-value"><?= date('M j, Y', strtotime($route['start_date'])) ?></div>
                        </div>
                        <div class="info-group">
                            <div class="info-label"><?= $t['lbl_end'] ?></div>
                            <div class="info-value"><?= !empty($route['end_date']) ? date('M j, Y', strtotime($route['end_date'])) : $t['no_end_date'] ?></div>
                        </div>
                    </div>

                    <?php if ($route['frequency'] === 'weekly' || $route['frequency'] === 'biweekly'): ?>
                        <div class="info-group" style="margin-top: 12px;">
                            <div class="info-label"><?= $t['lbl_days'] ?></div>
                            <div style="margin-top: 8px;">
                                <?php if (!empty($route['days_of_week'])): ?>
                                    <?php foreach ($route['days_of_week'] as $day): ?>
                                        <span class="day-badge"><?= ucfirst($day) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 13px;"><?= $t['not_specified'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif ($route['frequency'] === 'monthly'): ?>
                        <div class="info-group" style="margin-top: 12px;">
                            <div class="info-label"><?= $t['lbl_day_month'] ?></div>
                            <div class="info-value"><?= $route['day_of_month'] ?? $t['not_specified'] ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pickup Location -->
                <div class="section-card">
                    <div class="section-title"><i class="fas fa-map-marker-alt"></i> <?= $t['sec_pickup'] ?></div>
                    <div class="address-block">
                        <h4><?= $t['pickup_address'] ?></h4>
                        <p>
                            <?= htmlspecialchars($route['pickup_street']) ?><br>
                            <?= htmlspecialchars($route['pickup_city']) ?>, <?= htmlspecialchars($route['pickup_province']) ?> <?= htmlspecialchars($route['pickup_postal_code']) ?>
                        </p>
                    </div>
                    <?php if (!empty($route['pickup_time_start']) || !empty($route['pickup_time_end'])): ?>
                        <div class="info-group">
                            <div class="info-label"><?= $t['pickup_window'] ?></div>
                            <div class="info-value">
                                <?= $route['pickup_time_start'] ?? '09:00' ?> - <?= $route['pickup_time_end'] ?? '17:00' ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Destinations -->
                <div class="section-card">
                    <div class="section-title"><i class="fas fa-flag-checkered"></i> <?= $t['sec_dest'] ?></div>
                    <?php if (!empty($route['destinations_template'])): ?>
                        <?php foreach ($route['destinations_template'] as $i => $dest): ?>
                            <div class="destination-item">
                                <div class="destination-number"><?= $i + 1 ?></div>
                                <div class="destination-details">
                                    <div class="destination-name"><?= htmlspecialchars($dest['contact_name'] ?? $t['stop_prefix'] . ($i + 1)) ?></div>
                                    <div class="destination-address">
                                        <?= htmlspecialchars($dest['street'] ?? '') ?>,
                                        <?= htmlspecialchars($dest['city'] ?? '') ?>,
                                        <?= htmlspecialchars($dest['province'] ?? '') ?>
                                        <?= htmlspecialchars($dest['postal_code'] ?? '') ?>
                                    </div>
                                    <?php if (!empty($dest['delivery_notes'])): ?>
                                        <div style="font-size: 12px; color: #999; margin-top: 4px;">
                                            <i class="fas fa-sticky-note"></i> <?= htmlspecialchars($dest['delivery_notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= $t['no_destinations'] ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <!-- Settings -->
                <div class="section-card">
                    <div class="section-title"><i class="fas fa-cog"></i> <?= $t['sec_settings'] ?></div>
                    <div class="setting-row">
                        <span class="setting-label"><?= $t['lbl_auto_submit'] ?></span>
                        <span class="setting-value <?= !empty($route['auto_submit']) ? 'on' : 'off' ?>">
                            <?= !empty($route['auto_submit']) ? $t['enabled'] : $t['disabled'] ?>
                        </span>
                    </div>
                    <div class="setting-row">
                        <span class="setting-label"><?= $t['lbl_notify_before'] ?></span>
                        <span class="setting-value"><?= $route['notify_before_days'] ?? 1 ?> <?= $t['days'] ?></span>
                    </div>
                    <?php if (!empty($route['next_generation_date'])): ?>
                        <div class="setting-row">
                            <span class="setting-label"><?= $t['lbl_next_gen'] ?></span>
                            <span class="setting-value"><?= date('M j, Y', strtotime($route['next_generation_date'])) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Generated Shipments -->
                <div class="section-card">
                    <div class="section-title"><i class="fas fa-truck"></i> <?= $t['sec_shipments'] ?></div>
                    <?php if (!empty($shipments)): ?>
                        <ul class="shipment-list">
                            <?php foreach ($shipments as $s): ?>
                                <li class="shipment-item">
                                    <div>
                                        <a href="<?= url('distribution/shipments/show?id=' . $s['id']) ?>">
                                            <?= htmlspecialchars($s['shipment_number']) ?>
                                        </a>
                                        <div class="shipment-date"><?= date('M j, Y', strtotime($s['created_at'])) ?></div>
                                    </div>
                                    <span class="badge badge-<?= $s['status'] ?>"><?= ucfirst(str_replace('_', ' ', $s['status'])) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <?= $t['no_shipments'] ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php require __DIR__ . '/../layout-footer.php'; ?>
