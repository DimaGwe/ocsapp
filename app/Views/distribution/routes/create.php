<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = ([
    'en' => [
        'page_title'    => 'Create Recurring Route - OCSAPP Distribution',
        'portal_sub'    => 'Distribution Portal',
        'back_routes'   => 'Back to Routes',
        'title'         => 'Create Recurring Route',
        'subtitle'      => 'Set up a scheduled route for regular shipments',
        'sec_details'   => 'Route Details',
        'sec_schedule'  => 'Schedule',
        'sec_pickup'    => 'Pickup Location',
        'sec_dest'      => 'Delivery Destinations',
        'sec_settings'  => 'Settings',
        'lbl_name'      => 'Route Name',
        'ph_name'       => 'e.g., Weekly Downtown Delivery',
        'lbl_frequency' => 'Frequency',
        'lbl_start'     => 'Start Date',
        'lbl_end'       => 'End Date (Optional)',
        'lbl_days'      => 'Days of Week',
        'lbl_day_month' => 'Day of Month',
        'lbl_street'    => 'Street Address',
        'lbl_city'      => 'City',
        'lbl_province'  => 'Province',
        'lbl_postal'    => 'Postal Code',
        'lbl_time'      => 'Pickup Time Window',
        'lbl_to'        => 'to',
        'lbl_stop_name' => 'Stop Name',
        'ph_stop'       => 'e.g., Main Office',
        'lbl_packages'  => 'Packages',
        'lbl_contact'   => 'Contact Name',
        'lbl_notify'    => 'Notify Before (Days)',
        'notify_1'      => '1 day before',
        'notify_2'      => '2 days before',
        'notify_3'      => '3 days before',
        'notify_5'      => '5 days before',
        'notify_7'      => '1 week before',
        'lbl_auto_quote'=> 'Auto-submit for quote',
        'btn_add_stop'  => 'Add Another Stop',
        'btn_cancel'    => 'Cancel',
        'btn_create'    => 'Create Route',
        'btn_remove'    => 'Remove',
        'stop_prefix'   => 'Stop #',
        'freq_daily'    => 'Daily',
        'freq_weekly'   => 'Weekly',
        'freq_biweekly' => 'Bi-Weekly',
        'freq_monthly'  => 'Monthly',
    ],
    'fr' => [
        'page_title'    => 'Créer une route récurrente - OCSAPP Distribution',
        'portal_sub'    => 'Portail de Distribution',
        'back_routes'   => 'Retour aux routes',
        'title'         => 'Créer une route récurrente',
        'subtitle'      => 'Configurez une route planifiée pour des envois réguliers',
        'sec_details'   => 'Détails de la route',
        'sec_schedule'  => 'Calendrier',
        'sec_pickup'    => 'Lieu de ramassage',
        'sec_dest'      => 'Destinations de livraison',
        'sec_settings'  => 'Paramètres',
        'lbl_name'      => 'Nom de la route',
        'ph_name'       => 'ex. : Livraison hebdomadaire au centre-ville',
        'lbl_frequency' => 'Fréquence',
        'lbl_start'     => 'Date de début',
        'lbl_end'       => 'Date de fin (optionnel)',
        'lbl_days'      => 'Jours de la semaine',
        'lbl_day_month' => 'Jour du mois',
        'lbl_street'    => 'Adresse',
        'lbl_city'      => 'Ville',
        'lbl_province'  => 'Province',
        'lbl_postal'    => 'Code postal',
        'lbl_time'      => 'Plage horaire de ramassage',
        'lbl_to'        => 'à',
        'lbl_stop_name' => "Nom de l'arrêt",
        'ph_stop'       => 'ex. : Bureau principal',
        'lbl_packages'  => 'Colis',
        'lbl_contact'   => 'Nom du contact',
        'lbl_notify'    => 'Avertir avant (jours)',
        'notify_1'      => '1 jour avant',
        'notify_2'      => '2 jours avant',
        'notify_3'      => '3 jours avant',
        'notify_5'      => '5 jours avant',
        'notify_7'      => '1 semaine avant',
        'lbl_auto_quote'=> 'Soumettre automatiquement pour devis',
        'btn_add_stop'  => 'Ajouter un arrêt',
        'btn_cancel'    => 'Annuler',
        'btn_create'    => 'Créer la route',
        'btn_remove'    => 'Supprimer',
        'stop_prefix'   => 'Arrêt #',
        'freq_daily'    => 'Quotidien',
        'freq_weekly'   => 'Hebdomadaire',
        'freq_biweekly' => 'Bihebdomadaire',
        'freq_monthly'  => 'Mensuel',
    ],
])[$currentLang] ?? [];

$currentPage = 'routes';
$pageTitle = $t['page_title'];
$_pageT = $t; // preserve before layout-header.php overwrites $t
require __DIR__ . '/../layout-header.php';
$t = $_pageT; unset($_pageT); // restore page-specific translations
?>
        <a href="<?= url('distribution/routes') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> <?= $t['back_routes'] ?>
        </a>

        <div class="page-header">
            <h1 class="page-title"><?= $t['title'] ?></h1>
            <p class="page-subtitle"><?= $t['subtitle'] ?></p>
        </div>

        <form action="<?= url('distribution/routes/store') ?>" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">

            <!-- Route Details -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-route"></i> <?= $t['sec_details'] ?>
                </div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label"><?= $t['lbl_name'] ?> <span class="required">*</span></label>
                        <input type="text" name="route_name" class="form-control"
                               placeholder="<?= htmlspecialchars($t['ph_name']) ?>"
                               value="<?= htmlspecialchars($old['route_name'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-calendar-alt"></i> <?= $t['sec_schedule'] ?>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_frequency'] ?> <span class="required">*</span></label>
                        <select name="frequency" class="form-control" id="frequencySelect">
                            <option value="daily"><?= $t['freq_daily'] ?></option>
                            <option value="weekly" selected><?= $t['freq_weekly'] ?></option>
                            <option value="biweekly"><?= $t['freq_biweekly'] ?></option>
                            <option value="monthly"><?= $t['freq_monthly'] ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_start'] ?> <span class="required">*</span></label>
                        <input type="date" name="start_date" class="form-control" min="<?= date('Y-m-d') ?>"
                               value="<?= htmlspecialchars($old['start_date'] ?? date('Y-m-d')) ?>">
                    </div>
                    <div class="form-group full-width" id="daysOfWeek">
                        <label class="form-label"><?= $t['lbl_days'] ?></label>
                        <div class="day-selector">
                            <?php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            foreach ($days as $day):
                            ?>
                                <label class="day-option">
                                    <input type="checkbox" name="days_of_week[]" value="<?= strtolower($day) ?>">
                                    <?= substr($day, 0, 3) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group hidden" id="dayOfMonth">
                        <label class="form-label"><?= $t['lbl_day_month'] ?></label>
                        <select name="day_of_month" class="form-control">
                            <?php for ($i = 1; $i <= 28; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_end'] ?></label>
                        <input type="date" name="end_date" class="form-control"
                               value="<?= htmlspecialchars($old['end_date'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Pickup -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-map-marker-alt"></i> <?= $t['sec_pickup'] ?>
                </div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label"><?= $t['lbl_street'] ?> <span class="required">*</span></label>
                        <input type="text" name="pickup_street" class="form-control"
                               value="<?= htmlspecialchars($old['pickup_street'] ?? $business['delivery_street'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_city'] ?> <span class="required">*</span></label>
                        <input type="text" name="pickup_city" class="form-control"
                               value="<?= htmlspecialchars($old['pickup_city'] ?? $business['delivery_city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_province'] ?> <span class="required">*</span></label>
                        <select name="pickup_province" class="form-control">
                            <?php
                            $provinces = ['AB' => 'Alberta', 'BC' => 'British Columbia', 'MB' => 'Manitoba', 'NB' => 'New Brunswick',
                                'NL' => 'Newfoundland', 'NS' => 'Nova Scotia', 'NT' => 'Northwest Territories',
                                'NU' => 'Nunavut', 'ON' => 'Ontario', 'PE' => 'Prince Edward Island', 'QC' => 'Quebec',
                                'SK' => 'Saskatchewan', 'YT' => 'Yukon'];
                            $selectedProvince = $old['pickup_province'] ?? $business['delivery_province'] ?? '';
                            foreach ($provinces as $code => $name): ?>
                                <option value="<?= $code ?>" <?= $selectedProvince === $code ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_postal'] ?> <span class="required">*</span></label>
                        <input type="text" name="pickup_postal_code" class="form-control" maxlength="7"
                               value="<?= htmlspecialchars($old['pickup_postal_code'] ?? $business['delivery_postal_code'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_time'] ?></label>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <input type="time" name="pickup_time_start" class="form-control" value="09:00">
                            <span><?= $t['lbl_to'] ?></span>
                            <input type="time" name="pickup_time_end" class="form-control" value="17:00">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Destinations -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-flag-checkered"></i> <?= $t['sec_dest'] ?>
                </div>
                <div id="destinationsContainer">
                    <div class="destination-item" data-index="0">
                        <div class="destination-header">
                            <h4><?= $t['stop_prefix'] ?>1</h4>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_stop_name'] ?> <span class="required">*</span></label>
                                <input type="text" name="destinations[0][name]" class="form-control" placeholder="<?= htmlspecialchars($t['ph_stop']) ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_packages'] ?></label>
                                <input type="number" name="destinations[0][packages_count]" class="form-control" value="1" min="1">
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label"><?= $t['lbl_street'] ?> <span class="required">*</span></label>
                                <input type="text" name="destinations[0][street]" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_city'] ?> <span class="required">*</span></label>
                                <input type="text" name="destinations[0][city]" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_province'] ?></label>
                                <select name="destinations[0][province]" class="form-control">
                                    <?php foreach ($provinces as $code => $name): ?>
                                        <option value="<?= $code ?>"><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_postal'] ?></label>
                                <input type="text" name="destinations[0][postal_code]" class="form-control" maxlength="7">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_contact'] ?></label>
                                <input type="text" name="destinations[0][contact_name]" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-add-destination" id="addDestination">
                    <i class="fas fa-plus"></i> <?= $t['btn_add_stop'] ?>
                </button>
            </div>

            <!-- Settings -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-cog"></i> <?= $t['sec_settings'] ?>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_notify'] ?></label>
                        <select name="notify_days_before" class="form-control">
                            <option value="1"><?= $t['notify_1'] ?></option>
                            <option value="2" selected><?= $t['notify_2'] ?></option>
                            <option value="3"><?= $t['notify_3'] ?></option>
                            <option value="5"><?= $t['notify_5'] ?></option>
                            <option value="7"><?= $t['notify_7'] ?></option>
                        </select>
                    </div>
                    <div class="form-group" style="display: flex; align-items: end;">
                        <div class="checkbox-group">
                            <input type="checkbox" name="auto_submit" id="autoSubmit">
                            <label for="autoSubmit" style="font-size: 14px; cursor: pointer;"><?= $t['lbl_auto_quote'] ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?= url('distribution/routes') ?>" class="btn btn-secondary"><?= $t['btn_cancel'] ?></a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> <?= $t['btn_create'] ?>
                </button>
            </div>
        </form>

    <script>
        // Translations for dynamic JS content
        const jsT = {
            stopPrefix:   <?= json_encode($t['stop_prefix']) ?>,
            btnRemove:    <?= json_encode($t['btn_remove']) ?>,
            lblStopName:  <?= json_encode($t['lbl_stop_name']) ?>,
            lblPackages:  <?= json_encode($t['lbl_packages']) ?>,
            lblStreet:    <?= json_encode($t['lbl_street']) ?>,
            lblCity:      <?= json_encode($t['lbl_city']) ?>,
            lblProvince:  <?= json_encode($t['lbl_province']) ?>,
            lblPostal:    <?= json_encode($t['lbl_postal']) ?>,
            lblContact:   <?= json_encode($t['lbl_contact']) ?>,
            phStop:       <?= json_encode($t['ph_stop']) ?>,
        };

        document.addEventListener('DOMContentLoaded', function() {
            const frequencySelect = document.getElementById('frequencySelect');
            const daysOfWeek = document.getElementById('daysOfWeek');
            const dayOfMonth = document.getElementById('dayOfMonth');
            const destinationsContainer = document.getElementById('destinationsContainer');
            let destinationIndex = 1;

            // Day options selection
            document.querySelectorAll('.day-option').forEach(option => {
                option.addEventListener('click', function() {
                    this.classList.toggle('selected');
                });
            });

            // Frequency change
            frequencySelect.addEventListener('change', function() {
                if (this.value === 'weekly' || this.value === 'biweekly') {
                    daysOfWeek.classList.remove('hidden');
                    dayOfMonth.classList.add('hidden');
                } else if (this.value === 'monthly') {
                    daysOfWeek.classList.add('hidden');
                    dayOfMonth.classList.remove('hidden');
                } else {
                    daysOfWeek.classList.add('hidden');
                    dayOfMonth.classList.add('hidden');
                }
            });

            // Add destination
            document.getElementById('addDestination').addEventListener('click', function() {
                const provinces = <?= json_encode($provinces) ?>;
                let provinceOptions = '';
                for (const [code, name] of Object.entries(provinces)) {
                    provinceOptions += `<option value="${code}">${name}</option>`;
                }

                const template = `
                    <div class="destination-item" data-index="${destinationIndex}">
                        <div class="destination-header">
                            <h4>${jsT.stopPrefix}${destinationIndex + 1}</h4>
                            <button type="button" class="btn-remove">${jsT.btnRemove}</button>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">${jsT.lblStopName} <span class="required">*</span></label>
                                <input type="text" name="destinations[${destinationIndex}][name]" class="form-control" placeholder="${jsT.phStop}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">${jsT.lblPackages}</label>
                                <input type="number" name="destinations[${destinationIndex}][packages_count]" class="form-control" value="1" min="1">
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label">${jsT.lblStreet} <span class="required">*</span></label>
                                <input type="text" name="destinations[${destinationIndex}][street]" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">${jsT.lblCity} <span class="required">*</span></label>
                                <input type="text" name="destinations[${destinationIndex}][city]" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">${jsT.lblProvince}</label>
                                <select name="destinations[${destinationIndex}][province]" class="form-control">${provinceOptions}</select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">${jsT.lblPostal}</label>
                                <input type="text" name="destinations[${destinationIndex}][postal_code]" class="form-control" maxlength="7">
                            </div>
                            <div class="form-group">
                                <label class="form-label">${jsT.lblContact}</label>
                                <input type="text" name="destinations[${destinationIndex}][contact_name]" class="form-control">
                            </div>
                        </div>
                    </div>
                `;
                destinationsContainer.insertAdjacentHTML('beforeend', template);
                destinationIndex++;
            });

            // Remove destination
            destinationsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove')) {
                    if (destinationsContainer.children.length > 1) {
                        e.target.closest('.destination-item').remove();
                        updateStopNumbers();
                    }
                }
            });

            function updateStopNumbers() {
                const items = destinationsContainer.querySelectorAll('.destination-item');
                items.forEach((item, index) => {
                    item.querySelector('h4').textContent = `${jsT.stopPrefix}${index + 1}`;
                });
            }
        });
    </script>
<?php require __DIR__ . '/../layout-footer.php'; ?>
