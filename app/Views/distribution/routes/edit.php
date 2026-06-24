<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$currentPage = 'routes';
$pageTitle = $currentLang === 'fr' ? 'Modifier la route' : 'Edit Route';
require __DIR__ . '/../layout-header.php';
?>
        <a href="<?= url('distribution/routes/show?id=' . $route['id']) ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Route
        </a>

        <div class="page-header">
            <h1 class="page-title">Edit Recurring Route</h1>
            <p class="page-subtitle"><?= htmlspecialchars($route['route_name']) ?></p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $err): ?>
                    <div><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('distribution/routes/update') ?>" method="POST">
            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="route_id" value="<?= $route['id'] ?>">

            <!-- Route Details -->
            <div class="form-card">
                <div class="form-section-title"><i class="fas fa-route"></i> Route Details</div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Route Name <span class="required">*</span></label>
                        <input type="text" name="route_name" class="form-control"
                               value="<?= htmlspecialchars($old['route_name'] ?? $route['route_name'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="form-card">
                <div class="form-section-title"><i class="fas fa-calendar-alt"></i> Schedule</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Frequency <span class="required">*</span></label>
                        <select name="frequency" class="form-control" id="frequencySelect">
                            <option value="daily" <?= $freq === 'daily' ? 'selected' : '' ?>>Daily</option>
                            <option value="weekly" <?= $freq === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                            <option value="biweekly" <?= $freq === 'biweekly' ? 'selected' : '' ?>>Bi-Weekly</option>
                            <option value="monthly" <?= $freq === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Start Date <span class="required">*</span></label>
                        <input type="date" name="start_date" class="form-control"
                               value="<?= htmlspecialchars($old['start_date'] ?? $route['start_date'] ?? '') ?>">
                    </div>
                    <div class="form-group full-width <?= ($freq !== 'weekly' && $freq !== 'biweekly') ? 'hidden' : '' ?>" id="daysOfWeek">
                        <label class="form-label">Days of Week</label>
                        <div class="day-selector">
                            <?php
                            $days = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun'];
                            foreach ($days as $val => $label):
                                $isSelected = in_array($val, $selectedDays);
                            ?>
                                <label class="day-option <?= $isSelected ? 'selected' : '' ?>">
                                    <input type="checkbox" name="days_of_week[]" value="<?= $val ?>" <?= $isSelected ? 'checked' : '' ?>>
                                    <?= $label ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group <?= $freq !== 'monthly' ? 'hidden' : '' ?>" id="dayOfMonth">
                        <label class="form-label">Day of Month</label>
                        <select name="day_of_month" class="form-control">
                            <?php for ($i = 1; $i <= 28; $i++): ?>
                                <option value="<?= $i ?>" <?= ($old['day_of_month'] ?? $route['day_of_month'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date (Optional)</label>
                        <input type="date" name="end_date" class="form-control"
                               value="<?= htmlspecialchars($old['end_date'] ?? $route['end_date'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Pickup -->
            <div class="form-card">
                <div class="form-section-title"><i class="fas fa-map-marker-alt"></i> Pickup Location</div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Street Address <span class="required">*</span></label>
                        <input type="text" name="pickup_street" class="form-control"
                               value="<?= htmlspecialchars($old['pickup_street'] ?? $route['pickup_street'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">City <span class="required">*</span></label>
                        <input type="text" name="pickup_city" class="form-control"
                               value="<?= htmlspecialchars($old['pickup_city'] ?? $route['pickup_city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Province <span class="required">*</span></label>
                        <select name="pickup_province" class="form-control">
                            <?php $selProv = $old['pickup_province'] ?? $route['pickup_province'] ?? '';
                            foreach ($provinces as $code => $name): ?>
                                <option value="<?= $code ?>" <?= $selProv === $code ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Postal Code <span class="required">*</span></label>
                        <input type="text" name="pickup_postal_code" class="form-control" maxlength="7"
                               value="<?= htmlspecialchars($old['pickup_postal_code'] ?? $route['pickup_postal_code'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pickup Time Window</label>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <input type="time" name="pickup_time_start" class="form-control"
                                   value="<?= $old['pickup_time_start'] ?? $route['pickup_time_start'] ?? '09:00' ?>">
                            <span>to</span>
                            <input type="time" name="pickup_time_end" class="form-control"
                                   value="<?= $old['pickup_time_end'] ?? $route['pickup_time_end'] ?? '17:00' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Destinations -->
            <div class="form-card">
                <div class="form-section-title"><i class="fas fa-flag-checkered"></i> Destinations</div>
                <div id="destinationsContainer">
                    <?php
                    if (empty($destinations)) $destinations = [['contact_name' => '', 'street' => '', 'city' => '', 'province' => '', 'postal_code' => '', 'delivery_notes' => '']];
                    foreach ($destinations as $di => $d): ?>
                        <div class="destination-item" data-index="<?= $di ?>">
                            <div class="destination-header">
                                <h4>Destination #<?= $di + 1 ?></h4>
                                <?php if ($di > 0): ?>
                                    <button type="button" class="btn-remove remove-destination">Remove</button>
                                <?php endif; ?>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Contact Name</label>
                                    <input type="text" name="destinations[<?= $di ?>][contact_name]" class="form-control"
                                           value="<?= htmlspecialchars($d['contact_name'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" name="destinations[<?= $di ?>][contact_phone]" class="form-control"
                                           value="<?= htmlspecialchars($d['contact_phone'] ?? '') ?>">
                                </div>
                                <div class="form-group full-width">
                                    <label class="form-label">Street Address <span class="required">*</span></label>
                                    <input type="text" name="destinations[<?= $di ?>][street]" class="form-control"
                                           value="<?= htmlspecialchars($d['street'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">City <span class="required">*</span></label>
                                    <input type="text" name="destinations[<?= $di ?>][city]" class="form-control"
                                           value="<?= htmlspecialchars($d['city'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Province</label>
                                    <select name="destinations[<?= $di ?>][province]" class="form-control">
                                        <?php foreach ($provinces as $code => $name): ?>
                                            <option value="<?= $code ?>" <?= ($d['province'] ?? '') === $code ? 'selected' : '' ?>><?= $name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="destinations[<?= $di ?>][postal_code]" class="form-control" maxlength="7"
                                           value="<?= htmlspecialchars($d['postal_code'] ?? '') ?>">
                                </div>
                                <div class="form-group full-width">
                                    <label class="form-label">Delivery Notes</label>
                                    <textarea name="destinations[<?= $di ?>][delivery_notes]" class="form-control" rows="2"><?= htmlspecialchars($d['delivery_notes'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn-add-destination" id="addDestination">
                    <i class="fas fa-plus"></i> Add Another Destination
                </button>
            </div>

            <!-- Settings -->
            <div class="form-card">
                <div class="form-section-title"><i class="fas fa-cog"></i> Settings</div>
                <div class="form-grid">
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" name="auto_submit" id="autoSubmit" value="1"
                                   <?= !empty($old['auto_submit'] ?? $route['auto_submit']) ? 'checked' : '' ?>>
                            <label for="autoSubmit" style="font-size: 14px; color: #374151; cursor: pointer;">
                                Auto-submit generated shipments
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notify Before (days)</label>
                        <select name="notify_before_days" class="form-control">
                            <?php $notifyDays = $old['notify_before_days'] ?? $route['notify_before_days'] ?? 1;
                            foreach ([0 => 'No notification', 1 => '1 day', 2 => '2 days', 3 => '3 days', 5 => '5 days', 7 => '7 days'] as $val => $label): ?>
                                <option value="<?= $val ?>" <?= $notifyDays == $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="<?= url('distribution/routes/show?id=' . $route['id']) ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const freqSelect = document.getElementById('frequencySelect');
            const daysOfWeek = document.getElementById('daysOfWeek');
            const dayOfMonth = document.getElementById('dayOfMonth');
            const destinationsContainer = document.getElementById('destinationsContainer');
            let destIndex = <?= count($destinations) ?>;

            freqSelect.addEventListener('change', function() {
                const freq = this.value;
                daysOfWeek.classList.toggle('hidden', freq !== 'weekly' && freq !== 'biweekly');
                dayOfMonth.classList.toggle('hidden', freq !== 'monthly');
            });

            // Day option toggle
            document.querySelectorAll('.day-option').forEach(opt => {
                opt.addEventListener('click', function(e) {
                    if (e.target.tagName === 'INPUT') return;
                    const cb = this.querySelector('input[type="checkbox"]');
                    cb.checked = !cb.checked;
                    this.classList.toggle('selected', cb.checked);
                });
            });

            // Add destination
            document.getElementById('addDestination').addEventListener('click', function() {
                const provinceOptions = `<?php foreach ($provinces as $code => $name) echo '<option value="' . $code . '">' . $name . '</option>'; ?>`;
                const tpl = `<div class="destination-item" data-index="${destIndex}">
                    <div class="destination-header"><h4>Destination #${destIndex + 1}</h4><button type="button" class="btn-remove remove-destination">Remove</button></div>
                    <div class="form-grid">
                        <div class="form-group"><label class="form-label">Contact Name</label><input type="text" name="destinations[${destIndex}][contact_name]" class="form-control"></div>
                        <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="destinations[${destIndex}][contact_phone]" class="form-control"></div>
                        <div class="form-group full-width"><label class="form-label">Street Address <span class="required">*</span></label><input type="text" name="destinations[${destIndex}][street]" class="form-control"></div>
                        <div class="form-group"><label class="form-label">City <span class="required">*</span></label><input type="text" name="destinations[${destIndex}][city]" class="form-control"></div>
                        <div class="form-group"><label class="form-label">Province</label><select name="destinations[${destIndex}][province]" class="form-control">${provinceOptions}</select></div>
                        <div class="form-group"><label class="form-label">Postal Code</label><input type="text" name="destinations[${destIndex}][postal_code]" class="form-control" maxlength="7"></div>
                        <div class="form-group full-width"><label class="form-label">Delivery Notes</label><textarea name="destinations[${destIndex}][delivery_notes]" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>`;
                destinationsContainer.insertAdjacentHTML('beforeend', tpl);
                destIndex++;
            });

            // Remove destination
            destinationsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-destination') || e.target.closest('.remove-destination')) {
                    const item = e.target.closest('.destination-item');
                    if (destinationsContainer.children.length > 1) {
                        item.remove();
                        destinationsContainer.querySelectorAll('.destination-item').forEach((el, idx) => {
                            el.querySelector('h4').textContent = `Destination #${idx + 1}`;
                        });
                    }
                }
            });
        });
    </script>
<?php require __DIR__ . '/../layout-footer.php'; ?>
