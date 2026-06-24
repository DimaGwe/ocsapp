<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$currentPage = 'shipments';
$pageTitle = $currentLang === 'fr' ? "Modifier l'envoi" : 'Edit Shipment';
require __DIR__ . '/../layout-header.php';
?>
        <a href="<?= url('distribution/shipments/show?id=' . $shipment['id']) ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Shipment
        </a>

        <div class="page-header">
            <h1 class="page-title">Edit Shipment <?= htmlspecialchars($shipment['shipment_number']) ?></h1>
            <p class="page-subtitle">Update your draft shipment details</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>

        <form action="<?= url('distribution/shipments/update') ?>" method="POST" id="shipmentForm">
            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="shipment_id" value="<?= $shipment['id'] ?>">

            <!-- Shipment Type -->
            <div class="form-card">
                <div class="form-section-title"><i class="fas fa-box"></i> Shipment Type</div>
                <div class="shipment-type-selector">
                    <label class="type-option <?= $currentType === 'parcel' ? 'selected' : '' ?>" data-type="parcel">
                        <input type="radio" name="shipment_type" value="parcel" <?= $currentType === 'parcel' ? 'checked' : '' ?>>
                        <i class="fas fa-box"></i>
                        <h4>Parcel</h4>
                        <p>Send packages to one destination</p>
                    </label>
                    <label class="type-option <?= $currentType === 'multi_drop' ? 'selected' : '' ?>" data-type="multi_drop">
                        <input type="radio" name="shipment_type" value="multi_drop" <?= $currentType === 'multi_drop' ? 'checked' : '' ?>>
                        <i class="fas fa-route"></i>
                        <h4>Multi-Drop</h4>
                        <p>Multiple stops in one trip</p>
                    </label>
                    <label class="type-option <?= $currentType === 'product_fulfillment' ? 'selected' : '' ?>" data-type="product_fulfillment">
                        <input type="radio" name="shipment_type" value="product_fulfillment" <?= $currentType === 'product_fulfillment' ? 'checked' : '' ?>>
                        <i class="fas fa-dolly"></i>
                        <h4>Product Fulfillment</h4>
                        <p>Ship products with inventory</p>
                    </label>
                </div>
            </div>

            <!-- Pickup Information -->
            <div class="form-card">
                <div class="form-section-title"><i class="fas fa-map-marker-alt"></i> Pickup Location</div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Street Address <span class="required">*</span></label>
                        <input type="text" name="pickup_street" class="form-control <?= !empty($errors['pickup_street']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($old['pickup_street'] ?? $shipment['pickup_street'] ?? '') ?>">
                        <?php if (!empty($errors['pickup_street'])): ?><div class="error-text"><?= $errors['pickup_street'] ?></div><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label">City <span class="required">*</span></label>
                        <input type="text" name="pickup_city" class="form-control"
                               value="<?= htmlspecialchars($old['pickup_city'] ?? $shipment['pickup_city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Province <span class="required">*</span></label>
                        <select name="pickup_province" class="form-control">
                            <?php $selProv = $old['pickup_province'] ?? $shipment['pickup_province'] ?? '';
                            foreach ($provinces as $code => $name): ?>
                                <option value="<?= $code ?>" <?= $selProv === $code ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Postal Code <span class="required">*</span></label>
                        <input type="text" name="pickup_postal_code" class="form-control" maxlength="7"
                               value="<?= htmlspecialchars($old['pickup_postal_code'] ?? $shipment['pickup_postal_code'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Name</label>
                        <input type="text" name="pickup_contact_name" class="form-control"
                               value="<?= htmlspecialchars($old['pickup_contact_name'] ?? $shipment['pickup_contact_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Phone</label>
                        <input type="tel" name="pickup_contact_phone" class="form-control"
                               value="<?= htmlspecialchars($old['pickup_contact_phone'] ?? $shipment['pickup_contact_phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Preferred Pickup Date</label>
                        <input type="date" name="requested_pickup_date" class="form-control" min="<?= date('Y-m-d') ?>"
                               value="<?= htmlspecialchars($old['requested_pickup_date'] ?? $shipment['requested_pickup_date'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pickup Time Window</label>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <input type="time" name="requested_pickup_time_start" class="form-control"
                                   value="<?= htmlspecialchars($old['requested_pickup_time_start'] ?? $shipment['requested_pickup_time_start'] ?? '09:00') ?>">
                            <span>to</span>
                            <input type="time" name="requested_pickup_time_end" class="form-control"
                                   value="<?= htmlspecialchars($old['requested_pickup_time_end'] ?? $shipment['requested_pickup_time_end'] ?? '17:00') ?>">
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Pickup Instructions</label>
                        <textarea name="pickup_instructions" class="form-control" rows="2"><?= htmlspecialchars($old['pickup_instructions'] ?? $shipment['pickup_notes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Single Destination -->
            <div class="form-card <?= $currentType === 'multi_drop' ? 'hidden' : '' ?>" id="singleDestination">
                <div class="form-section-title"><i class="fas fa-flag-checkered"></i> Delivery Destination</div>
                <?php $dest0 = $destinations[0] ?? []; ?>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Street Address <span class="required">*</span></label>
                        <input type="text" name="destination_street" class="form-control"
                               value="<?= htmlspecialchars($old['destination_street'] ?? $dest0['street'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">City <span class="required">*</span></label>
                        <input type="text" name="destination_city" class="form-control"
                               value="<?= htmlspecialchars($old['destination_city'] ?? $dest0['city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Province <span class="required">*</span></label>
                        <select name="destination_province" class="form-control">
                            <?php $selDestProv = $old['destination_province'] ?? $dest0['province'] ?? '';
                            foreach ($provinces as $code => $name): ?>
                                <option value="<?= $code ?>" <?= $selDestProv === $code ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Postal Code <span class="required">*</span></label>
                        <input type="text" name="destination_postal_code" class="form-control" maxlength="7"
                               value="<?= htmlspecialchars($old['destination_postal_code'] ?? $dest0['postal_code'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Recipient Name</label>
                        <input type="text" name="destination_contact_name" class="form-control"
                               value="<?= htmlspecialchars($old['destination_contact_name'] ?? $dest0['contact_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Recipient Phone</label>
                        <input type="tel" name="destination_contact_phone" class="form-control"
                               value="<?= htmlspecialchars($old['destination_contact_phone'] ?? $dest0['contact_phone'] ?? '') ?>">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Delivery Instructions</label>
                        <textarea name="destination_instructions" class="form-control" rows="2"><?= htmlspecialchars($old['destination_instructions'] ?? $dest0['delivery_notes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Multi-Drop Destinations -->
            <div class="form-card <?= $currentType !== 'multi_drop' ? 'hidden' : '' ?>" id="multiDropDestinations">
                <div class="form-section-title"><i class="fas fa-route"></i> Delivery Stops</div>
                <div id="destinationsContainer">
                    <?php
                    $editDests = $currentType === 'multi_drop' ? $destinations : [[]];
                    foreach ($editDests as $di => $d): ?>
                        <div class="destination-item" data-index="<?= $di ?>">
                            <div class="destination-header">
                                <h4>Stop #<?= $di + 1 ?></h4>
                                <?php if ($di > 0): ?>
                                    <button type="button" class="btn-remove remove-destination">Remove</button>
                                <?php endif; ?>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Stop Name <span class="required">*</span></label>
                                    <input type="text" name="destinations[<?= $di ?>][name]" class="form-control"
                                           value="<?= htmlspecialchars($d['contact_name'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Packages</label>
                                    <input type="number" name="destinations[<?= $di ?>][packages_count]" class="form-control" value="1" min="1">
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
                                <div class="form-group">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="tel" name="destinations[<?= $di ?>][contact_phone]" class="form-control"
                                           value="<?= htmlspecialchars($d['contact_phone'] ?? '') ?>">
                                </div>
                                <div class="form-group full-width">
                                    <label class="form-label">Delivery Instructions</label>
                                    <textarea name="destinations[<?= $di ?>][instructions]" class="form-control" rows="2"><?= htmlspecialchars($d['delivery_notes'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn-add-destination" id="addDestination">
                    <i class="fas fa-plus"></i> Add Another Stop
                </button>
            </div>

            <!-- Package Details -->
            <div class="form-card">
                <div class="form-section-title"><i class="fas fa-boxes"></i> Package Details</div>
                <div class="form-grid three-cols">
                    <div class="form-group">
                        <label class="form-label">Number of Packages</label>
                        <input type="number" name="total_packages" class="form-control"
                               value="<?= $old['total_packages'] ?? $shipment['total_packages'] ?? 1 ?>" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Weight (kg)</label>
                        <input type="number" name="total_weight_kg" class="form-control" step="0.1"
                               value="<?= $old['total_weight_kg'] ?? $shipment['total_weight_kg'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Package Type</label>
                        <select name="package_type" class="form-control">
                            <?php $pkgType = $old['package_type'] ?? $shipment['package_type'] ?? ''; ?>
                            <option value="">Select...</option>
                            <option value="documents" <?= $pkgType === 'documents' ? 'selected' : '' ?>>Documents</option>
                            <option value="small_box" <?= $pkgType === 'small_box' ? 'selected' : '' ?>>Small Box</option>
                            <option value="medium_box" <?= $pkgType === 'medium_box' ? 'selected' : '' ?>>Medium Box</option>
                            <option value="large_box" <?= $pkgType === 'large_box' ? 'selected' : '' ?>>Large Box</option>
                            <option value="pallet" <?= $pkgType === 'pallet' ? 'selected' : '' ?>>Pallet</option>
                            <option value="custom" <?= $pkgType === 'custom' ? 'selected' : '' ?>>Custom</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Package Description</label>
                    <textarea name="package_description" class="form-control" rows="2"><?= htmlspecialchars($old['package_description'] ?? $shipment['package_description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Items (for product fulfillment) -->
            <div class="form-card <?= $currentType !== 'product_fulfillment' ? 'hidden' : '' ?>" id="productItems">
                <div class="form-section-title"><i class="fas fa-list-alt"></i> Items to Ship</div>
                <div id="itemsContainer">
                    <?php
                    $editItems = !empty($items) ? $items : [['item_name' => '', 'quantity' => 1, 'weight_kg' => '', 'value' => '']];
                    foreach ($editItems as $ii => $item): ?>
                        <div class="item-row">
                            <div class="form-group">
                                <?php if ($ii === 0): ?><label class="form-label">Item Name</label><?php endif; ?>
                                <input type="text" name="items[<?= $ii ?>][name]" class="form-control"
                                       value="<?= htmlspecialchars($item['item_name'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <?php if ($ii === 0): ?><label class="form-label">SKU</label><?php endif; ?>
                                <input type="text" name="items[<?= $ii ?>][sku]" class="form-control"
                                       value="<?= htmlspecialchars($item['sku'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <?php if ($ii === 0): ?><label class="form-label">Qty</label><?php endif; ?>
                                <input type="number" name="items[<?= $ii ?>][quantity]" class="form-control"
                                       value="<?= $item['quantity'] ?? 1 ?>" min="1">
                            </div>
                            <div class="form-group">
                                <?php if ($ii === 0): ?><label class="form-label">Value ($)</label><?php endif; ?>
                                <input type="number" name="items[<?= $ii ?>][value]" class="form-control" step="0.01"
                                       value="<?= $item['value'] ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn-remove remove-item" <?= $ii === 0 ? 'style="visibility:hidden;"' : '' ?>>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn-add-destination" id="addItem">
                    <i class="fas fa-plus"></i> Add Another Item
                </button>
            </div>

            <!-- Notes -->
            <div class="form-card">
                <div class="form-section-title"><i class="fas fa-sticky-note"></i> Additional Notes</div>
                <div class="form-group">
                    <textarea name="business_notes" class="form-control" rows="3"><?= htmlspecialchars($old['business_notes'] ?? $shipment['special_instructions'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="<?= url('distribution/shipments/show?id=' . $shipment['id']) ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="action" value="draft" class="btn btn-outline">
                    <i class="fas fa-save"></i> Save Draft
                </button>
                <button type="submit" name="action" value="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Save & Submit
                </button>
            </div>
        </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeOptions = document.querySelectorAll('.type-option');
            const singleDest = document.getElementById('singleDestination');
            const multiDropDest = document.getElementById('multiDropDestinations');
            const productItems = document.getElementById('productItems');
            const destinationsContainer = document.getElementById('destinationsContainer');
            const itemsContainer = document.getElementById('itemsContainer');
            let destinationIndex = <?= count($editDests) ?>;
            let itemIndex = <?= count($editItems) ?>;

            typeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    typeOptions.forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    this.querySelector('input').checked = true;
                    const type = this.dataset.type;
                    if (type === 'multi_drop') {
                        singleDest.classList.add('hidden');
                        multiDropDest.classList.remove('hidden');
                        productItems.classList.add('hidden');
                    } else if (type === 'product_fulfillment') {
                        singleDest.classList.remove('hidden');
                        multiDropDest.classList.add('hidden');
                        productItems.classList.remove('hidden');
                    } else {
                        singleDest.classList.remove('hidden');
                        multiDropDest.classList.add('hidden');
                        productItems.classList.add('hidden');
                    }
                });
            });

            document.getElementById('addDestination').addEventListener('click', function() {
                const provinceOptions = `<?php foreach ($provinces as $code => $name) echo '<option value="' . $code . '">' . $name . '</option>'; ?>`;
                const tpl = `<div class="destination-item" data-index="${destinationIndex}">
                    <div class="destination-header"><h4>Stop #${destinationIndex + 1}</h4><button type="button" class="btn-remove remove-destination">Remove</button></div>
                    <div class="form-grid">
                        <div class="form-group"><label class="form-label">Stop Name <span class="required">*</span></label><input type="text" name="destinations[${destinationIndex}][name]" class="form-control"></div>
                        <div class="form-group"><label class="form-label">Packages</label><input type="number" name="destinations[${destinationIndex}][packages_count]" class="form-control" value="1" min="1"></div>
                        <div class="form-group full-width"><label class="form-label">Street Address <span class="required">*</span></label><input type="text" name="destinations[${destinationIndex}][street]" class="form-control"></div>
                        <div class="form-group"><label class="form-label">City <span class="required">*</span></label><input type="text" name="destinations[${destinationIndex}][city]" class="form-control"></div>
                        <div class="form-group"><label class="form-label">Province</label><select name="destinations[${destinationIndex}][province]" class="form-control">${provinceOptions}</select></div>
                        <div class="form-group"><label class="form-label">Postal Code</label><input type="text" name="destinations[${destinationIndex}][postal_code]" class="form-control" maxlength="7"></div>
                        <div class="form-group"><label class="form-label">Contact Phone</label><input type="tel" name="destinations[${destinationIndex}][contact_phone]" class="form-control"></div>
                        <div class="form-group full-width"><label class="form-label">Delivery Instructions</label><textarea name="destinations[${destinationIndex}][instructions]" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>`;
                destinationsContainer.insertAdjacentHTML('beforeend', tpl);
                destinationIndex++;
            });

            destinationsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-destination') || e.target.closest('.remove-destination')) {
                    const item = e.target.closest('.destination-item');
                    if (destinationsContainer.children.length > 1) {
                        item.remove();
                        destinationsContainer.querySelectorAll('.destination-item').forEach((el, idx) => {
                            el.querySelector('h4').textContent = `Stop #${idx + 1}`;
                        });
                    }
                }
            });

            document.getElementById('addItem').addEventListener('click', function() {
                const tpl = `<div class="item-row">
                    <div class="form-group"><input type="text" name="items[${itemIndex}][name]" class="form-control" placeholder="Product name"></div>
                    <div class="form-group"><input type="text" name="items[${itemIndex}][sku]" class="form-control" placeholder="SKU"></div>
                    <div class="form-group"><input type="number" name="items[${itemIndex}][quantity]" class="form-control" value="1" min="1"></div>
                    <div class="form-group"><input type="number" name="items[${itemIndex}][value]" class="form-control" step="0.01"></div>
                    <div class="form-group"><button type="button" class="btn-remove remove-item"><i class="fas fa-times"></i></button></div>
                </div>`;
                itemsContainer.insertAdjacentHTML('beforeend', tpl);
                itemIndex++;
            });

            itemsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
                    const row = e.target.closest('.item-row');
                    if (itemsContainer.children.length > 1) row.remove();
                }
            });
        });
    </script>
<?php require __DIR__ . '/../layout-footer.php'; ?>
