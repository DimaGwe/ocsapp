<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = ([
    'en' => [
        'page_title'        => 'New Shipment - OCSAPP Distribution',
        'portal_sub'        => 'Distribution Portal',
        'back_shipments'    => 'Back to Shipments',
        'title'             => 'Create New Shipment',
        'subtitle'          => 'Send packages or products to one or multiple destinations',
        'sec_type'          => 'Shipment Type',
        'type_parcel'       => 'Parcel',
        'type_parcel_desc'  => 'Send packages to one destination',
        'type_multi'        => 'Multi-Drop',
        'type_multi_desc'   => 'Multiple stops in one trip',
        'type_fulfillment'  => 'Product Fulfillment',
        'type_fulfil_desc'  => 'Ship products with inventory',
        'sec_pickup'        => 'Pickup Location',
        'sec_destination'   => 'Delivery Destination',
        'sec_stops'         => 'Delivery Stops',
        'sec_packages'      => 'Package Details',
        'sec_items'         => 'Items to Ship',
        'sec_notes'         => 'Additional Notes',
        'lbl_street'        => 'Street Address',
        'lbl_city'          => 'City',
        'lbl_province'      => 'Province',
        'lbl_postal'        => 'Postal Code',
        'lbl_contact_name'  => 'Contact Name',
        'lbl_contact_phone' => 'Contact Phone',
        'lbl_pickup_date'   => 'Preferred Pickup Date',
        'lbl_time_window'   => 'Pickup Time Window',
        'lbl_to'            => 'to',
        'lbl_pickup_notes'  => 'Pickup Instructions',
        'lbl_recipient'     => 'Recipient Name',
        'lbl_recip_phone'   => 'Recipient Phone',
        'lbl_del_notes'     => 'Delivery Instructions',
        'lbl_stop_name'     => 'Stop Name',
        'lbl_packages'      => 'Packages',
        'lbl_num_packages'  => 'Number of Packages',
        'lbl_weight'        => 'Total Weight (kg)',
        'lbl_pkg_type'      => 'Package Type',
        'lbl_pkg_desc'      => 'Package Description',
        'lbl_item_name'     => 'Item Name',
        'lbl_sku'           => 'SKU',
        'lbl_qty'           => 'Qty',
        'lbl_value'         => 'Value ($)',
        'lbl_notes'         => 'Any special requirements or information for this shipment...',
        'btn_add_stop'      => 'Add Another Stop',
        'btn_add_item'      => 'Add Another Item',
        'btn_remove'        => 'Remove',
        'btn_cancel'        => 'Cancel',
        'btn_draft'         => 'Save as Draft',
        'btn_submit'        => 'Submit for Quote',
        'ph_pickup_notes'   => 'e.g., Ring doorbell, ask for reception, loading dock access...',
        'ph_del_notes'      => 'e.g., Leave at door, call upon arrival...',
        'ph_stop_name'      => 'e.g., Downtown Office',
        'ph_pkg_desc'       => 'Describe the contents of your shipment...',
        'ph_product_name'   => 'Product name',
        'opt_select'        => 'Select...',
        'opt_documents'     => 'Documents',
        'opt_small_box'     => 'Small Box',
        'opt_medium_box'    => 'Medium Box',
        'opt_large_box'     => 'Large Box',
        'opt_pallet'        => 'Pallet',
        'opt_custom'        => 'Custom',
    ],
    'fr' => [
        'page_title'        => 'Nouvel envoi - OCSAPP Distribution',
        'portal_sub'        => 'Portail de Distribution',
        'back_shipments'    => 'Retour aux envois',
        'title'             => 'Créer un nouvel envoi',
        'subtitle'          => 'Envoyez des colis ou des produits vers une ou plusieurs destinations',
        'sec_type'          => "Type d'envoi",
        'type_parcel'       => 'Colis',
        'type_parcel_desc'  => 'Envoyer des colis à une destination',
        'type_multi'        => 'Multi-arrêts',
        'type_multi_desc'   => 'Plusieurs arrêts en un voyage',
        'type_fulfillment'  => 'Expédition de produits',
        'type_fulfil_desc'  => 'Expédier des produits avec inventaire',
        'sec_pickup'        => 'Lieu de ramassage',
        'sec_destination'   => 'Destination de livraison',
        'sec_stops'         => 'Arrêts de livraison',
        'sec_packages'      => 'Détails des colis',
        'sec_items'         => 'Articles à expédier',
        'sec_notes'         => 'Notes supplémentaires',
        'lbl_street'        => 'Adresse',
        'lbl_city'          => 'Ville',
        'lbl_province'      => 'Province',
        'lbl_postal'        => 'Code postal',
        'lbl_contact_name'  => 'Nom du contact',
        'lbl_contact_phone' => 'Téléphone du contact',
        'lbl_pickup_date'   => 'Date de ramassage souhaitée',
        'lbl_time_window'   => 'Plage horaire de ramassage',
        'lbl_to'            => 'à',
        'lbl_pickup_notes'  => 'Instructions de ramassage',
        'lbl_recipient'     => 'Nom du destinataire',
        'lbl_recip_phone'   => 'Téléphone du destinataire',
        'lbl_del_notes'     => 'Instructions de livraison',
        'lbl_stop_name'     => "Nom de l'arrêt",
        'lbl_packages'      => 'Colis',
        'lbl_num_packages'  => 'Nombre de colis',
        'lbl_weight'        => 'Poids total (kg)',
        'lbl_pkg_type'      => 'Type de colis',
        'lbl_pkg_desc'      => 'Description du colis',
        'lbl_item_name'     => "Nom de l'article",
        'lbl_sku'           => 'SKU',
        'lbl_qty'           => 'Qté',
        'lbl_value'         => 'Valeur ($)',
        'lbl_notes'         => "Toute exigence particulière ou information concernant cet envoi...",
        'btn_add_stop'      => 'Ajouter un arrêt',
        'btn_add_item'      => 'Ajouter un article',
        'btn_remove'        => 'Supprimer',
        'btn_cancel'        => 'Annuler',
        'btn_draft'         => 'Enregistrer comme brouillon',
        'btn_submit'        => 'Soumettre pour devis',
        'ph_pickup_notes'   => 'ex. : Sonner à la porte, demander à la réception...',
        'ph_del_notes'      => 'ex. : Laisser à la porte, appeler à l\'arrivée...',
        'ph_stop_name'      => 'ex. : Bureau principal',
        'ph_pkg_desc'       => 'Décrivez le contenu de votre envoi...',
        'ph_product_name'   => 'Nom du produit',
        'opt_select'        => 'Sélectionner...',
        'opt_documents'     => 'Documents',
        'opt_small_box'     => 'Petite boîte',
        'opt_medium_box'    => 'Boîte moyenne',
        'opt_large_box'     => 'Grande boîte',
        'opt_pallet'        => 'Palette',
        'opt_custom'        => 'Personnalisé',
    ],
])[$currentLang] ?? [];

$currentPage = 'shipments';
$pageTitle = $t['page_title'];
$_pageT = $t; // preserve before layout-header.php overwrites $t
require __DIR__ . '/../layout-header.php';
$t = $_pageT; unset($_pageT); // restore page-specific translations
?>
        <a href="<?= url('distribution/shipments') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> <?= $t['back_shipments'] ?>
        </a>

        <div class="page-header">
            <h1 class="page-title"><?= $t['title'] ?></h1>
            <p class="page-subtitle"><?= $t['subtitle'] ?></p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>

        <form action="<?= url('distribution/shipments/store') ?>" method="POST" id="shipmentForm">
            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">

            <!-- Shipment Type -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-box"></i> <?= $t['sec_type'] ?>
                </div>
                <div class="shipment-type-selector">
                    <label class="type-option selected" data-type="parcel">
                        <input type="radio" name="shipment_type" value="parcel" checked>
                        <i class="fas fa-box"></i>
                        <h4><?= $t['type_parcel'] ?></h4>
                        <p><?= $t['type_parcel_desc'] ?></p>
                    </label>
                    <label class="type-option" data-type="multi_drop">
                        <input type="radio" name="shipment_type" value="multi_drop">
                        <i class="fas fa-route"></i>
                        <h4><?= $t['type_multi'] ?></h4>
                        <p><?= $t['type_multi_desc'] ?></p>
                    </label>
                    <label class="type-option" data-type="product_fulfillment">
                        <input type="radio" name="shipment_type" value="product_fulfillment">
                        <i class="fas fa-dolly"></i>
                        <h4><?= $t['type_fulfillment'] ?></h4>
                        <p><?= $t['type_fulfil_desc'] ?></p>
                    </label>
                </div>
            </div>

            <!-- Pickup Information -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-map-marker-alt"></i> <?= $t['sec_pickup'] ?>
                </div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label"><?= $t['lbl_street'] ?> <span class="required">*</span></label>
                        <input type="text" name="pickup_street" class="form-control <?= !empty($errors['pickup_street']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($old['pickup_street'] ?? $business['delivery_street'] ?? '') ?>">
                        <?php if (!empty($errors['pickup_street'])): ?>
                            <div class="error-text"><?= $errors['pickup_street'] ?></div>
                        <?php endif; ?>
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
                                'NL' => 'Newfoundland and Labrador', 'NS' => 'Nova Scotia', 'NT' => 'Northwest Territories',
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
                        <label class="form-label"><?= $t['lbl_contact_name'] ?></label>
                        <input type="text" name="pickup_contact_name" class="form-control"
                               value="<?= htmlspecialchars($old['pickup_contact_name'] ?? ($business['first_name'] . ' ' . $business['last_name']) ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_contact_phone'] ?></label>
                        <input type="tel" name="pickup_contact_phone" class="form-control"
                               value="<?= htmlspecialchars($old['pickup_contact_phone'] ?? $business['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_pickup_date'] ?></label>
                        <input type="date" name="requested_pickup_date" class="form-control" min="<?= date('Y-m-d') ?>"
                               value="<?= htmlspecialchars($old['requested_pickup_date'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_time_window'] ?></label>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <input type="time" name="requested_pickup_time_start" class="form-control"
                                   value="<?= htmlspecialchars($old['requested_pickup_time_start'] ?? '09:00') ?>">
                            <span><?= $t['lbl_to'] ?></span>
                            <input type="time" name="requested_pickup_time_end" class="form-control"
                                   value="<?= htmlspecialchars($old['requested_pickup_time_end'] ?? '17:00') ?>">
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label"><?= $t['lbl_pickup_notes'] ?></label>
                        <textarea name="pickup_instructions" class="form-control" rows="2"
                                  placeholder="<?= $t['ph_pickup_notes'] ?>"><?= htmlspecialchars($old['pickup_instructions'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Single Destination (for parcel/product fulfillment) -->
            <div class="form-card" id="singleDestination">
                <div class="form-section-title">
                    <i class="fas fa-flag-checkered"></i> <?= $t['sec_destination'] ?>
                </div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label"><?= $t['lbl_street'] ?> <span class="required">*</span></label>
                        <input type="text" name="destination_street" class="form-control"
                               value="<?= htmlspecialchars($old['destination_street'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_city'] ?> <span class="required">*</span></label>
                        <input type="text" name="destination_city" class="form-control"
                               value="<?= htmlspecialchars($old['destination_city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_province'] ?> <span class="required">*</span></label>
                        <select name="destination_province" class="form-control">
                            <?php foreach ($provinces as $code => $name): ?>
                                <option value="<?= $code ?>" <?= ($old['destination_province'] ?? '') === $code ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_postal'] ?> <span class="required">*</span></label>
                        <input type="text" name="destination_postal_code" class="form-control" maxlength="7"
                               value="<?= htmlspecialchars($old['destination_postal_code'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_recipient'] ?></label>
                        <input type="text" name="destination_contact_name" class="form-control"
                               value="<?= htmlspecialchars($old['destination_contact_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_recip_phone'] ?></label>
                        <input type="tel" name="destination_contact_phone" class="form-control"
                               value="<?= htmlspecialchars($old['destination_contact_phone'] ?? '') ?>">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label"><?= $t['lbl_del_notes'] ?></label>
                        <textarea name="destination_instructions" class="form-control" rows="2"
                                  placeholder="<?= $t['ph_del_notes'] ?>"><?= htmlspecialchars($old['destination_instructions'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Multi-Drop Destinations -->
            <div class="form-card hidden" id="multiDropDestinations">
                <div class="form-section-title">
                    <i class="fas fa-route"></i> <?= $t['sec_stops'] ?>
                </div>
                <div id="destinationsContainer">
                    <div class="destination-item" data-index="0">
                        <div class="destination-header">
                            <h4>Stop #1</h4>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Stop Name <span class="required">*</span></label>
                                <input type="text" name="destinations[0][name]" class="form-control" placeholder="e.g., Downtown Office">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Packages</label>
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
                                <label class="form-label">Province</label>
                                <select name="destinations[0][province]" class="form-control">
                                    <?php foreach ($provinces as $code => $name): ?>
                                        <option value="<?= $code ?>"><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="destinations[0][postal_code]" class="form-control" maxlength="7">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_contact_name'] ?></label>
                                <input type="text" name="destinations[0][contact_name]" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_contact_phone'] ?></label>
                                <input type="tel" name="destinations[0][contact_phone]" class="form-control">
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label">Delivery Instructions</label>
                                <textarea name="destinations[0][instructions]" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-add-destination" id="addDestination">
                    <i class="fas fa-plus"></i> Add Another Stop
                </button>
            </div>

            <!-- Package Details -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-boxes"></i> <?= $t['sec_packages'] ?>
                </div>
                <div class="form-grid three-cols">
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_num_packages'] ?></label>
                        <input type="number" name="total_packages" class="form-control" value="<?= $old['total_packages'] ?? 1 ?>" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_weight'] ?></label>
                        <input type="number" name="total_weight_kg" class="form-control" step="0.1" value="<?= $old['total_weight_kg'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $t['lbl_pkg_type'] ?></label>
                        <select name="package_type" class="form-control">
                            <option value=""><?= $t['opt_select'] ?></option>
                            <option value="documents"><?= $t['opt_documents'] ?></option>
                            <option value="small_box"><?= $t['opt_small_box'] ?></option>
                            <option value="medium_box"><?= $t['opt_medium_box'] ?></option>
                            <option value="large_box"><?= $t['opt_large_box'] ?></option>
                            <option value="pallet"><?= $t['opt_pallet'] ?></option>
                            <option value="custom"><?= $t['opt_custom'] ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= $t['lbl_pkg_desc'] ?></label>
                    <textarea name="package_description" class="form-control" rows="2"
                              placeholder="<?= $t['ph_pkg_desc'] ?>"><?= htmlspecialchars($old['package_description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Items (for product fulfillment) -->
            <div class="form-card hidden" id="productItems">
                <div class="form-section-title">
                    <i class="fas fa-list-alt"></i> <?= $t['sec_items'] ?>
                </div>
                <div id="itemsContainer">
                    <div class="item-row">
                        <div class="form-group">
                            <label class="form-label"><?= $t['lbl_item_name'] ?></label>
                            <input type="text" name="items[0][name]" class="form-control" placeholder="<?= $t['ph_product_name'] ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?= $t['lbl_sku'] ?></label>
                            <input type="text" name="items[0][sku]" class="form-control" placeholder="SKU">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?= $t['lbl_qty'] ?></label>
                            <input type="number" name="items[0][quantity]" class="form-control" value="1" min="1">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?= $t['lbl_value'] ?></label>
                            <input type="number" name="items[0][value]" class="form-control" step="0.01">
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn-remove remove-item" style="margin-top: 28px; visibility: hidden;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-add-destination" id="addItem">
                    <i class="fas fa-plus"></i> <?= $t['btn_add_item'] ?>
                </button>
            </div>

            <!-- Notes -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-sticky-note"></i> <?= $t['sec_notes'] ?>
                </div>
                <div class="form-group">
                    <textarea name="business_notes" class="form-control" rows="3"
                              placeholder="<?= $t['lbl_notes'] ?>"><?= htmlspecialchars($old['business_notes'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="<?= url('distribution/shipments') ?>" class="btn btn-secondary"><?= $t['btn_cancel'] ?></a>
                <button type="submit" name="action" value="draft" class="btn btn-outline">
                    <i class="fas fa-save"></i> <?= $t['btn_draft'] ?>
                </button>
                <button type="submit" name="action" value="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> <?= $t['btn_submit'] ?>
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
            let destinationIndex = 1;
            let itemIndex = 1;

            // Type selection
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

            // Add destination
            document.getElementById('addDestination').addEventListener('click', function() {
                const template = `
                    <div class="destination-item" data-index="${destinationIndex}">
                        <div class="destination-header">
                            <h4>Stop #${destinationIndex + 1}</h4>
                            <button type="button" class="btn-remove remove-destination">Remove</button>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Stop Name <span class="required">*</span></label>
                                <input type="text" name="destinations[${destinationIndex}][name]" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Packages</label>
                                <input type="number" name="destinations[${destinationIndex}][packages_count]" class="form-control" value="1" min="1">
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label"><?= $t['lbl_street'] ?> <span class="required">*</span></label>
                                <input type="text" name="destinations[${destinationIndex}][street]" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_city'] ?> <span class="required">*</span></label>
                                <input type="text" name="destinations[${destinationIndex}][city]" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Province</label>
                                <select name="destinations[${destinationIndex}][province]" class="form-control">
                                    <?php foreach ($provinces as $code => $name): ?>
                                        <option value="<?= $code ?>"><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="destinations[${destinationIndex}][postal_code]" class="form-control" maxlength="7">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_contact_name'] ?></label>
                                <input type="text" name="destinations[${destinationIndex}][contact_name]" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><?= $t['lbl_contact_phone'] ?></label>
                                <input type="tel" name="destinations[${destinationIndex}][contact_phone]" class="form-control">
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label">Delivery Instructions</label>
                                <textarea name="destinations[${destinationIndex}][instructions]" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                `;
                destinationsContainer.insertAdjacentHTML('beforeend', template);
                destinationIndex++;
            });

            // Remove destination
            destinationsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-destination') || e.target.closest('.remove-destination')) {
                    const item = e.target.closest('.destination-item');
                    if (destinationsContainer.children.length > 1) {
                        item.remove();
                        updateDestinationNumbers();
                    }
                }
            });

            function updateDestinationNumbers() {
                const items = destinationsContainer.querySelectorAll('.destination-item');
                items.forEach((item, index) => {
                    item.querySelector('h4').textContent = `Stop #${index + 1}`;
                });
            }

            // Add item
            document.getElementById('addItem').addEventListener('click', function() {
                const template = `
                    <div class="item-row">
                        <div class="form-group">
                            <input type="text" name="items[${itemIndex}][name]" class="form-control" placeholder="Product name">
                        </div>
                        <div class="form-group">
                            <input type="text" name="items[${itemIndex}][sku]" class="form-control" placeholder="SKU">
                        </div>
                        <div class="form-group">
                            <input type="number" name="items[${itemIndex}][quantity]" class="form-control" value="1" min="1">
                        </div>
                        <div class="form-group">
                            <input type="number" name="items[${itemIndex}][value]" class="form-control" step="0.01">
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn-remove remove-item">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                itemsContainer.insertAdjacentHTML('beforeend', template);
                itemIndex++;
            });

            // Remove item
            itemsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
                    const row = e.target.closest('.item-row');
                    if (itemsContainer.children.length > 1) {
                        row.remove();
                    }
                }
            });
        });
    </script>
<?php require __DIR__ . '/../layout-footer.php'; ?>
