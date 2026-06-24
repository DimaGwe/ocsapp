<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = ([
    'en' => [
        'page_title'        => 'New Request - OCSAPP Distribution',
        'portal_sub'        => 'Distribution Portal',
        'nav_dashboard'     => 'Dashboard',
        'nav_requests'      => 'Requests',
        'nav_logout'        => 'Logout',
        'new_request'       => 'New Request',
        'title'             => 'Create New Procurement Request',
        'subtitle'          => "Select items from our catalog or add custom shopping list items.",
        'sec_details'       => 'Request Details',
        'lbl_name'          => 'Request Name',
        'ph_name'           => 'e.g., Weekly Office Supplies',
        'lbl_notes'         => 'Notes (optional)',
        'ph_notes'          => 'Any special instructions or notes...',
        'sec_delivery'      => 'Delivery Address',
        'lbl_street'        => 'Street Address',
        'lbl_city'          => 'City',
        'lbl_province'      => 'Province',
        'select_province'   => 'Select Province',
        'lbl_postal'        => 'Postal Code',
        'lbl_pref_date'     => 'Preferred Delivery Date',
        'sec_items'         => 'Items',
        'tab_catalog'       => 'Catalog',
        'tab_shopping'      => 'Shopping List',
        'select_supplier'   => 'Select a supplier to browse their products.',
        'no_suppliers'      => 'No suppliers available.',
        'products_suffix'   => 'products',
        'btn_back'          => 'Back',
        'ph_search'         => 'Search products...',
        'sku'               => 'SKU',
        'shopping_desc'     => "Add custom items that aren't in our catalog. We'll source them for you!",
        'excel_title'       => 'Bulk Upload from Excel',
        'excel_desc'        => 'Upload an Excel file to quickly populate your shopping list',
        'btn_dl_template'   => 'Download Template',
        'btn_upload_excel'  => 'Upload Excel',
        'excel_supports'    => 'Supports: .xlsx, .xls, .csv files',
        'or_manually'       => 'OR ADD ITEMS MANUALLY',
        'btn_add_item'      => 'Add Item',
        'sec_summary'       => 'Order Summary',
        'no_items'          => 'No items added yet',
        'lbl_route'         => 'Delivery Route',
        'lbl_manual'        => 'Manual:',
        'items_total'       => 'Items Total',
        'service_fee'       => 'Service Fee',
        'handling'          => 'Handling',
        'delivery'          => 'Delivery',
        'subtotal'          => 'Subtotal',
        'gst'               => 'GST (5%)',
        'qst'               => 'QST (9.975%)',
        'tip_optional'      => 'Add a Tip (Optional)',
        'tip_desc'          => 'Calculated on pre-tax amount per Canadian regulations. We never take a cut of your tip.',
        'tip_skip'          => 'Skip',
        'custom'            => 'Custom',
        'est_total'         => 'Estimated Total',
        'summary_note'      => 'Shopping list items will be quoted after review. Final total may vary.',
        'btn_save_draft'    => 'Save as Draft',
        'btn_cancel'            => 'Cancel',
        'lbl_delivery_type'     => 'Delivery Type',
        'dt_scheduled_title'    => 'Scheduled',
        'dt_scheduled_desc'     => 'Next day up to 1 week &mdash; choose date &amp; window',
        'dt_same_day_title'     => 'Same Day',
        'dt_same_day_desc'      => 'Delivered today during business hours &mdash; pick your window',
        'dt_express_title'      => 'Express ASAP',
        'dt_express_desc'       => 'Delivered within 2 hours of submission',
        'lbl_delivery_date'     => 'Delivery Date',
        'lbl_window_from'       => 'Window From',
        'lbl_window_to'         => 'Window To',
        'same_day_notice_title' => 'Same-Day Delivery',
        'same_day_notice_sub'   => 'Delivered today during business hours. Select your preferred delivery window below.',
        'lbl_earliest_time'     => 'Earliest Delivery Time',
        'lbl_latest_time'       => 'Latest Delivery Time',
        'express_notice'        => '<strong>&#9889; Express ASAP:</strong> Your order will be delivered within <strong>2 hours of submission</strong>. No delivery window needed &mdash; our system dispatches immediately upon submission.',
    ],
    'fr' => [
        'page_title'        => 'Nouvelle demande - OCSAPP Distribution',
        'portal_sub'        => 'Portail de Distribution',
        'nav_dashboard'     => 'Tableau de bord',
        'nav_requests'      => 'Demandes',
        'nav_logout'        => 'Déconnexion',
        'new_request'       => 'Nouvelle demande',
        'title'             => "Créer une nouvelle demande d'approvisionnement",
        'subtitle'          => "Sélectionnez des articles du catalogue ou ajoutez des articles personnalisés.",
        'sec_details'       => 'Détails de la demande',
        'lbl_name'          => 'Nom de la demande',
        'ph_name'           => 'ex. : Fournitures de bureau hebdomadaires',
        'lbl_notes'         => 'Notes (optionnel)',
        'ph_notes'          => 'Instructions spéciales ou notes...',
        'sec_delivery'      => 'Adresse de livraison',
        'lbl_street'        => 'Adresse',
        'lbl_city'          => 'Ville',
        'lbl_province'      => 'Province',
        'select_province'   => 'Sélectionner une province',
        'lbl_postal'        => 'Code postal',
        'lbl_pref_date'     => 'Date de livraison préférée',
        'sec_items'         => 'Articles',
        'tab_catalog'       => 'Catalogue',
        'tab_shopping'      => "Liste d'achats",
        'select_supplier'   => 'Sélectionnez un fournisseur pour parcourir ses produits.',
        'no_suppliers'      => 'Aucun fournisseur disponible.',
        'products_suffix'   => 'produits',
        'btn_back'          => 'Retour',
        'ph_search'         => 'Rechercher des produits...',
        'sku'               => 'UGS',
        'shopping_desc'     => "Ajoutez des articles personnalisés absents de notre catalogue. Nous les trouverons pour vous !",
        'excel_title'       => 'Importation en masse depuis Excel',
        'excel_desc'        => "Téléchargez un fichier Excel pour remplir rapidement votre liste d'achats",
        'btn_dl_template'   => 'Télécharger le modèle',
        'btn_upload_excel'  => 'Télécharger Excel',
        'excel_supports'    => 'Formats acceptés : .xlsx, .xls, .csv',
        'or_manually'       => 'OU AJOUTER DES ARTICLES MANUELLEMENT',
        'btn_add_item'      => 'Ajouter un article',
        'sec_summary'       => 'Résumé de la commande',
        'no_items'          => 'Aucun article ajouté',
        'lbl_route'         => 'Itinéraire de livraison',
        'lbl_manual'        => 'Manuel :',
        'items_total'       => 'Total des articles',
        'service_fee'       => 'Frais de service',
        'handling'          => 'Manutention',
        'delivery'          => 'Livraison',
        'subtotal'          => 'Sous-total',
        'gst'               => 'TPS (5%)',
        'qst'               => 'TVQ (9,975%)',
        'tip_optional'      => 'Ajouter un pourboire (optionnel)',
        'tip_desc'          => 'Calculé sur le montant avant taxes selon les règlements canadiens. Nous ne prenons jamais de commission sur votre pourboire.',
        'tip_skip'          => 'Ignorer',
        'custom'            => 'Personnalisé',
        'est_total'         => 'Total estimé',
        'summary_note'      => 'Les articles de la liste seront cotés après révision. Le total final peut varier.',
        'btn_save_draft'    => 'Enregistrer comme brouillon',
        'btn_cancel'            => 'Annuler',
        'lbl_delivery_type'     => 'Type de livraison',
        'dt_scheduled_title'    => 'Planifi&eacute;',
        'dt_scheduled_desc'     => 'Le lendemain jusqu\'&agrave; 1 semaine &mdash; choisissez la date et la plage horaire',
        'dt_same_day_title'     => 'Aujourd\'hui',
        'dt_same_day_desc'      => 'Livr&eacute; aujourd\'hui pendant les heures d\'affaires &mdash; choisissez votre plage',
        'dt_express_title'      => 'Express - Le plus vite possible',
        'dt_express_desc'       => 'Livr&eacute; dans les 2 heures suivant la soumission',
        'lbl_delivery_date'     => 'Date de livraison',
        'lbl_window_from'       => 'D&eacute;but de plage',
        'lbl_window_to'         => 'Fin de plage',
        'same_day_notice_title' => 'Livraison le jour m&ecirc;me',
        'same_day_notice_sub'   => 'Livr&eacute; aujourd\'hui pendant les heures d\'affaires. S&eacute;lectionnez votre plage horaire ci-dessous.',
        'lbl_earliest_time'     => 'Heure de livraison au plus t&ocirc;t',
        'lbl_latest_time'       => 'Heure de livraison au plus tard',
        'express_notice'        => '<strong>&#9889; Express - Le plus vite possible :</strong> votre commande sera livr&eacute;e dans les <strong>2 heures suivant la soumission</strong>. Aucune plage horaire requise &mdash; notre syst&egrave;me exp&eacute;die imm&eacute;diatement.',
    ],
])[$currentLang] ?? [];

$currentPage = 'request-create';
$pageTitle = $t['page_title'];
$_createT = $t; // preserve before layout-header.php overwrites $t
require __DIR__ . '/../layout-header.php';
$t = $_createT; // restore page-specific translations
unset($_createT);
?>
        <div class="breadcrumb">
            <a href="<?= url('distribution/requests') ?>"><?= $t['nav_requests'] ?></a>
            <span> / <?= $t['new_request'] ?></span>
        </div>

        <div class="page-header">
            <h1 class="page-title"><?= $t['title'] ?></h1>
            <p class="page-subtitle"><?= $t['subtitle'] ?></p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors['items'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errors['items']) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('distribution/requests/store') ?>" id="requestForm">
            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">

            <div class="form-grid page-layout">
                <div class="main-column">
                    <!-- Request Details -->
                    <div class="form-card">
                        <h3 class="form-section-title"><i class="fas fa-info-circle"></i> <?= $t['sec_details'] ?></h3>
                        <div class="form-group">
                            <label class="form-label" for="request_name"><?= $t['lbl_name'] ?> <span class="required" aria-hidden="true">*</span></label>
                            <input id="request_name" type="text" name="request_name" class="form-input <?= !empty($errors['request_name']) ? 'error' : '' ?>"
                                   value="<?= htmlspecialchars($old['request_name'] ?? '') ?>"
                                   placeholder="<?= htmlspecialchars($t['ph_name']) ?>"
                                   aria-required="true">
                            <?php if (!empty($errors['request_name'])): ?>
                                <div class="form-error"><?= htmlspecialchars($errors['request_name']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="notes"><?= $t['lbl_notes'] ?></label>
                            <textarea id="notes" name="notes" class="form-input" rows="3"
                                      placeholder="<?= htmlspecialchars($t['ph_notes']) ?>"><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="form-card">
                        <h3 class="form-section-title"><i class="fas fa-map-marker-alt"></i> <?= $t['sec_delivery'] ?></h3>
                        <div class="form-group">
                            <label class="form-label" for="delivery_street"><?= $t['lbl_street'] ?> <span class="required" aria-hidden="true">*</span></label>
                            <input id="delivery_street" type="text" name="delivery_street" class="form-input <?= !empty($errors['delivery_street']) ? 'error' : '' ?>"
                                   value="<?= htmlspecialchars($old['delivery_street'] ?? $businessAddress['delivery_street'] ?? '') ?>"
                                   aria-required="true">
                            <?php if (!empty($errors['delivery_street'])): ?>
                                <div class="form-error"><?= htmlspecialchars($errors['delivery_street']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="delivery_city"><?= $t['lbl_city'] ?> <span class="required" aria-hidden="true">*</span></label>
                                <input id="delivery_city" type="text" name="delivery_city" class="form-input <?= !empty($errors['delivery_city']) ? 'error' : '' ?>"
                                       value="<?= htmlspecialchars($old['delivery_city'] ?? $businessAddress['delivery_city'] ?? '') ?>"
                                       aria-required="true">
                                <?php if (!empty($errors['delivery_city'])): ?>
                                    <div class="form-error"><?= htmlspecialchars($errors['delivery_city']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="delivery_province"><?= $t['lbl_province'] ?> <span class="required" aria-hidden="true">*</span></label>
                                <select id="delivery_province" name="delivery_province" class="form-input <?= !empty($errors['delivery_province']) ? 'error' : '' ?>" aria-required="true">
                                    <option value=""><?= $t['select_province'] ?></option>
                                    <?php
                                    $provinces = ['AB' => 'Alberta', 'BC' => 'British Columbia', 'MB' => 'Manitoba', 'NB' => 'New Brunswick',
                                                  'NL' => 'Newfoundland and Labrador', 'NS' => 'Nova Scotia', 'NT' => 'Northwest Territories',
                                                  'NU' => 'Nunavut', 'ON' => 'Ontario', 'PE' => 'Prince Edward Island', 'QC' => 'Quebec',
                                                  'SK' => 'Saskatchewan', 'YT' => 'Yukon'];
                                    $selectedProvince = $old['delivery_province'] ?? $businessAddress['delivery_province'] ?? '';
                                    foreach ($provinces as $code => $name):
                                    ?>
                                        <option value="<?= $code ?>" <?= $selectedProvince === $code ? 'selected' : '' ?>><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="delivery_postal_code"><?= $t['lbl_postal'] ?> <span class="required" aria-hidden="true">*</span></label>
                                <input id="delivery_postal_code" type="text" name="delivery_postal_code" class="form-input <?= !empty($errors['delivery_postal_code']) ? 'error' : '' ?>"
                                       value="<?= htmlspecialchars($old['delivery_postal_code'] ?? $businessAddress['delivery_postal_code'] ?? '') ?>"
                                       placeholder="A1A 1A1"
                                       aria-required="true">
                                <?php if (!empty($errors['delivery_postal_code'])): ?>
                                    <div class="form-error"><?= htmlspecialchars($errors['delivery_postal_code']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group" style="grid-column:1/-1;">
                                <label class="form-label"><?= $t['lbl_delivery_type'] ?> <span class="required" aria-hidden="true">*</span></label>
                                <div style="display:flex;gap:12px;margin-bottom:12px;">
                                    <label style="flex:1;cursor:pointer;">
                                        <input type="radio" name="delivery_type" value="scheduled" id="dt_scheduled"
                                               <?= ($old['delivery_type'] ?? 'scheduled') === 'scheduled' ? 'checked' : '' ?>
                                               onchange="toggleDeliveryType()" style="display:none;">
                                        <div class="dt-card" id="card_scheduled"
                                             style="border:2px solid #4f46e5;background:#eef2ff;border-radius:10px;padding:14px 18px;text-align:center;transition:all .2s;">
                                            <div style="font-size:20px;margin-bottom:4px;">📅</div>
                                            <div style="font-weight:700;color:#3730a3;font-size:14px;"><?= $t['dt_scheduled_title'] ?></div>
                                            <div style="font-size:12px;color:#6b7280;margin-top:2px;"><?= $t['dt_scheduled_desc'] ?></div>
                                        </div>
                                    </label>
                                    <label style="flex:1;cursor:pointer;">
                                        <input type="radio" name="delivery_type" value="same_day" id="dt_same_day"
                                               <?= ($old['delivery_type'] ?? '') === 'same_day' ? 'checked' : '' ?>
                                               onchange="toggleDeliveryType()" style="display:none;">
                                        <div class="dt-card" id="card_same_day"
                                             style="border:2px solid #e5e7eb;background:white;border-radius:10px;padding:14px 18px;text-align:center;transition:all .2s;">
                                            <div style="font-size:20px;margin-bottom:4px;">☀️</div>
                                            <div style="font-weight:700;color:#374151;font-size:14px;"><?= $t['dt_same_day_title'] ?></div>
                                            <div style="font-size:12px;color:#6b7280;margin-top:2px;"><?= $t['dt_same_day_desc'] ?></div>
                                        </div>
                                    </label>
                                    <label style="flex:1;cursor:pointer;">
                                        <input type="radio" name="delivery_type" value="express" id="dt_express"
                                               <?= ($old['delivery_type'] ?? '') === 'express' ? 'checked' : '' ?>
                                               onchange="toggleDeliveryType()" style="display:none;">
                                        <div class="dt-card" id="card_express"
                                             style="border:2px solid #e5e7eb;background:white;border-radius:10px;padding:14px 18px;text-align:center;transition:all .2s;">
                                            <div style="font-size:20px;margin-bottom:4px;">⚡</div>
                                            <div style="font-weight:700;color:#374151;font-size:14px;"><?= $t['dt_express_title'] ?></div>
                                            <div style="font-size:12px;color:#6b7280;margin-top:2px;"><?= $t['dt_express_desc'] ?></div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Scheduled fields (future date + time window) -->
                                <div id="scheduled_fields" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                                    <div>
                                        <label class="form-label" for="scheduled_date"><?= $t['lbl_delivery_date'] ?> <span class="required">*</span></label>
                                        <input id="scheduled_date" type="date" name="scheduled_date" class="form-input"
                                               value="<?= htmlspecialchars($old['scheduled_date'] ?? '') ?>"
                                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                               max="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                                    </div>
                                    <div>
                                        <label class="form-label" for="scheduled_time_from"><?= $t['lbl_window_from'] ?> <span class="required">*</span></label>
                                        <input id="scheduled_time_from" type="time" name="scheduled_time_from" class="form-input"
                                               value="<?= htmlspecialchars($old['scheduled_time_from'] ?? '09:00') ?>">
                                    </div>
                                    <div>
                                        <label class="form-label" for="scheduled_time_to"><?= $t['lbl_window_to'] ?> <span class="required">*</span></label>
                                        <input id="scheduled_time_to" type="time" name="scheduled_time_to" class="form-input"
                                               value="<?= htmlspecialchars($old['scheduled_time_to'] ?? '17:00') ?>">
                                    </div>
                                </div>

                                <!-- Same Day fields (today locked + time window) -->
                                <div id="same_day_fields" style="display:none;">
                                    <input type="hidden" id="same_day_date" name="same_day_date" value="<?= date('Y-m-d') ?>">
                                    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:10px 14px;font-size:13px;color:#166534;margin-bottom:10px;">
                                        ☀️ <strong><?= $t['same_day_notice_title'] ?> &mdash; <?= date('l, F j, Y') ?></strong><br>
                                        <span style="font-size:12px;"><?= $t['same_day_notice_sub'] ?></span>
                                    </div>
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                        <div>
                                            <label class="form-label" for="same_day_time_from"><?= $t['lbl_earliest_time'] ?> <span class="required">*</span></label>
                                            <input id="same_day_time_from" type="time" name="same_day_time_from" class="form-input"
                                                   value="<?= htmlspecialchars($old['same_day_time_from'] ?? '') ?>">
                                        </div>
                                        <div>
                                            <label class="form-label" for="same_day_time_to"><?= $t['lbl_latest_time'] ?> <span class="required">*</span></label>
                                            <input id="same_day_time_to" type="time" name="same_day_time_to" class="form-input"
                                                   value="<?= htmlspecialchars($old['same_day_time_to'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Express notice (no date/time needed) -->
                                <div id="express_notice" style="display:none;background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:12px 14px;font-size:13px;color:#92400e;">
                                    <?= $t['express_notice'] ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Selection -->
                    <div class="form-card">
                        <h3 class="form-section-title"><i class="fas fa-shopping-cart"></i> <?= $t['sec_items'] ?></h3>

                        <div class="tabs">
                            <button type="button" class="tab-btn active" data-tab="catalog">
                                <i class="fas fa-store"></i> <?= $t['tab_catalog'] ?>
                            </button>
                            <button type="button" class="tab-btn" data-tab="shopping">
                                <i class="fas fa-list"></i> <?= $t['tab_shopping'] ?>
                            </button>
                        </div>

                        <!-- Catalog Tab -->
                        <div class="tab-content active" id="tab-catalog">
                            <!-- Suppliers List View -->
                            <div id="suppliersView">
                                <p class="tab-intro-text"><?= $t['select_supplier'] ?></p>
                                <?php if (empty($suppliers)): ?>
                                    <p class="no-suppliers-msg"><?= $t['no_suppliers'] ?></p>
                                <?php else: ?>
                                    <div class="suppliers-grid">
                                        <?php foreach ($suppliers as $supplier):
                                            $supplierDisplayName = $supplier['company_name'] ?? $supplier['name'] ?? 'Unknown Supplier';
                                        ?>
                                            <div class="supplier-card" data-supplier-id="<?= $supplier['id'] ?>"
                                                 data-supplier-name="<?= htmlspecialchars($supplierDisplayName) ?>">
                                                <div class="supplier-logo">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <div class="supplier-name"><?= htmlspecialchars($supplierDisplayName) ?></div>
                                                <div class="supplier-meta"><?= $supplier['product_count'] ?> <?= $t['products_suffix'] ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Supplier Products View (Hidden by default) -->
                            <div id="supplierProducts">
                                <div class="supplier-products-header">
                                    <button type="button" class="btn-back" id="backToSuppliers" aria-label="<?= htmlspecialchars($t['btn_back']) ?>">
                                        <i class="fas fa-arrow-left"></i> <?= $t['btn_back'] ?>
                                    </button>
                                    <span class="current-supplier-name" id="currentSupplierName"></span>
                                </div>

                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="catalogSearch" placeholder="<?= htmlspecialchars($t['ph_search']) ?>">
                                </div>

                                <div class="catalog-scroll" id="productsContainer">
                                    <!-- Products will be loaded here dynamically -->
                                </div>
                            </div>

                            <!-- Hidden product templates for each supplier -->
                            <?php foreach ($productsBySupplier as $supplierId => $products): ?>
                                <template id="supplier-products-<?= $supplierId ?>">
                                    <?php foreach ($products as $product): ?>
                                        <div class="product-item" data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>">
                                            <img src="<?= $product['image'] ? asset(str_starts_with($product['image'], 'uploads/') ? $product['image'] : 'uploads/supplier-products/' . $product['image']) : asset('images/logo.png') ?>"
                                                 alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                                            <div class="product-info">
                                                <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                                <div class="product-sku"><?= $t['sku'] ?>: <?= htmlspecialchars($product['sku'] ?? 'N/A') ?> <?= $product['unit'] ? '• ' . htmlspecialchars($product['unit']) : '' ?></div>
                                            </div>
                                            <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                                            <input type="number" name="catalog_items[<?= $product['id'] ?>]"
                                                   class="product-qty" min="0"
                                                   value="<?= (int)($old['catalog_items'][$product['id']] ?? 0) ?>"
                                                   placeholder="0"
                                                   data-price="<?= $product['price'] ?>"
                                                   data-weight="<?= $product['weight_kg'] ?? 0 ?>"
                                                   data-product-id="<?= $product['id'] ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </template>
                            <?php endforeach; ?>
                        </div>

                        <!-- Shopping List Tab -->
                        <div class="tab-content" id="tab-shopping">
                            <p class="tab-intro-text"><?= $t['shopping_desc'] ?></p>

                            <!-- Excel Upload Section -->
                            <div class="excel-upload-section" id="excelUploadSection">
                                <div class="excel-upload-title">
                                    <i class="fas fa-file-excel"></i> <?= $t['excel_title'] ?>
                                </div>
                                <div class="excel-upload-desc">
                                    <?= $t['excel_desc'] ?>
                                </div>
                                <div class="excel-upload-actions">
                                    <button type="button" class="btn-download-template" id="downloadTemplate">
                                        <i class="fas fa-download"></i> <?= $t['btn_dl_template'] ?>
                                    </button>
                                    <label class="btn-upload-excel">
                                        <i class="fas fa-upload"></i> <?= $t['btn_upload_excel'] ?>
                                        <input type="file" class="upload-file-input" id="excelFileInput"
                                               accept=".xlsx,.xls,.csv">
                                    </label>
                                </div>
                                <div class="upload-info">
                                    <?= $t['excel_supports'] ?>
                                </div>
                                <div class="upload-result" id="uploadResult"></div>
                            </div>

                            <div class="divider-or"><?= $t['or_manually'] ?></div>

                            <div class="shopping-items-list" id="shoppingItemsList">
                                <!-- Items will be added here dynamically -->
                            </div>

                            <button type="button" class="btn-add-item" id="addShoppingItem">
                                <i class="fas fa-plus"></i> <?= $t['btn_add_item'] ?>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="summary-column">
                    <div class="form-card summary-section">
                        <h3 class="form-section-title"><i class="fas fa-receipt"></i> <?= $t['sec_summary'] ?></h3>

                        <!-- Tier Badge -->
                        <div id="tierBadge" style="display: none;"></div>

                        <!-- Items List -->
                        <div id="summaryItems">
                            <p class="summary-empty-text"><?= $t['no_items'] ?></p>
                        </div>

                        <!-- Delivery Route Display -->
                        <div class="delivery-route-section" id="deliveryRouteSection" style="display: none;">
                            <div class="delivery-route-header">
                                <label><i class="fas fa-route"></i> <?= $t['lbl_route'] ?></label>
                                <span class="delivery-route-distance" id="routeDistanceDisplay">0 km</span>
                            </div>
                            <div class="route-legs" id="routeLegs"></div>
                            <div class="geocoding-status" id="geocodingStatus" style="display: none;"></div>
                            <div class="delivery-manual-fallback" id="manualDistanceFallback" style="display: none;">
                                <label class="manual-distance-label"><?= $t['lbl_manual'] ?></label>
                                <input type="number" id="manualDistance" min="0" max="500" value="0" step="1">
                                <span>km</span>
                            </div>
                            <input type="hidden" id="deliveryDistance" name="delivery_distance" value="0">
                        </div>

                        <!-- Fee Breakdown -->
                        <div class="fee-breakdown" id="feeBreakdown" style="display: none;">
                            <div class="fee-row">
                                <span><?= $t['items_total'] ?></span>
                                <span id="itemsTotal">$0.00</span>
                            </div>
                            <div class="fee-row">
                                <span><?= $t['service_fee'] ?> (<span id="serviceFeePercent">0</span>%)</span>
                                <span id="serviceFeeAmount">$0.00</span>
                            </div>
                            <div class="fee-row">
                                <span><?= $t['handling'] ?> (<span id="handlingWeightInfo">0 kg</span> × $0.20/kg)</span>
                                <span id="handlingFeeAmount">$0.00</span>
                            </div>
                            <div class="fee-row" id="deliveryFeeRow">
                                <span><?= $t['delivery'] ?> (<span id="deliveryInfo">Free</span>)</span>
                                <span id="deliveryFeeAmount">$0.00</span>
                            </div>
                        </div>

                        <!-- Subtotal -->
                        <div class="summary-subtotal" id="summarySubtotal" style="display: none;">
                            <span><?= $t['subtotal'] ?></span>
                            <span id="subtotalAmount">$0.00</span>
                        </div>

                        <!-- Tax Section -->
                        <div class="tax-section" id="taxSection" style="display: none;">
                            <div class="tax-row">
                                <span><?= $t['gst'] ?></span>
                                <span id="gstAmount">$0.00</span>
                            </div>
                            <div class="tax-row">
                                <span><?= $t['qst'] ?></span>
                                <span id="qstAmount">$0.00</span>
                            </div>
                        </div>

                        <!-- Tip Section -->
                        <div class="tip-section" id="tipSection" style="display: none;">
                            <div class="tip-header">
                                <i class="fas fa-heart"></i> <?= $t['tip_optional'] ?>
                            </div>
                            <p class="tip-description"><?= $t['tip_desc'] ?></p>
                            <div class="tip-options">
                                <button type="button" class="tip-btn" data-tip="0" onclick="selectTip(0)" aria-pressed="false"><?= $t['tip_skip'] ?></button>
                                <button type="button" class="tip-btn" data-tip="15" onclick="selectTip(15)" aria-pressed="false">15%</button>
                                <button type="button" class="tip-btn" data-tip="18" onclick="selectTip(18)" aria-pressed="false">18%</button>
                                <button type="button" class="tip-btn" data-tip="20" onclick="selectTip(20)" aria-pressed="false">20%</button>
                                <button type="button" class="tip-btn" data-tip="custom" onclick="selectTip('custom')" aria-pressed="false"><?= $t['custom'] ?></button>
                            </div>
                            <div class="tip-custom-input" id="tipCustomInput" style="display: none;">
                                <div class="tip-custom-row">
                                    <span class="tip-currency">$</span>
                                    <input type="number" id="tipCustomAmount" class="tip-custom-amount-input"
                                           min="0" max="9999" step="0.01" placeholder="0.00"
                                           oninput="updateCustomTip()">
                                </div>
                            </div>
                            <div class="tip-display" id="tipDisplay" style="display: none;">
                                <span id="tipLabel">Tip</span>
                                <span id="tipAmount">$0.00</span>
                            </div>
                            <input type="hidden" name="tip_percentage" id="tipPercentage" value="0">
                            <input type="hidden" name="tip_custom_amount" id="tipCustomAmountHidden" value="0">
                        </div>

                        <!-- Total -->
                        <div class="summary-total" id="summaryTotal" style="display: none;">
                            <span><?= $t['est_total'] ?></span>
                            <span id="totalAmount">$0.00</span>
                        </div>

                        <div class="summary-note">
                            <i class="fas fa-info-circle"></i>
                            <?= $t['summary_note'] ?>
                        </div>

                        <button type="submit" name="action" value="draft" class="btn-submit">
                            <i class="fas fa-save"></i> <?= $t['btn_save_draft'] ?>
                        </button>
                        <button type="button" class="btn btn-secondary" style="width:100%;justify-content:center;margin-top:8px;" onclick="history.back()">
                            <?= $t['btn_cancel'] ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>

    <script>
        // ── Delivery type toggle ──────────────────────────────────────────
        function toggleDeliveryType() {
            const isScheduled = document.getElementById('dt_scheduled').checked;
            const isSameDay   = document.getElementById('dt_same_day').checked;
            const isExpress   = document.getElementById('dt_express').checked;

            document.getElementById('scheduled_fields').style.display = isScheduled ? 'grid' : 'none';
            document.getElementById('same_day_fields').style.display  = isSameDay   ? 'block' : 'none';
            document.getElementById('express_notice').style.display   = isExpress   ? 'block' : 'none';

            // Scheduled card
            document.getElementById('card_scheduled').style.borderColor = isScheduled ? '#4f46e5' : '#e5e7eb';
            document.getElementById('card_scheduled').style.background  = isScheduled ? '#eef2ff' : 'white';
            document.getElementById('card_scheduled').querySelector('div:nth-child(2)').style.color = isScheduled ? '#3730a3' : '#374151';

            // Same Day card
            document.getElementById('card_same_day').style.borderColor = isSameDay ? '#16a34a' : '#e5e7eb';
            document.getElementById('card_same_day').style.background  = isSameDay ? '#f0fdf4' : 'white';
            document.getElementById('card_same_day').querySelector('div:nth-child(2)').style.color = isSameDay ? '#15803d' : '#374151';

            // Express card
            document.getElementById('card_express').style.borderColor = isExpress ? '#dc2626' : '#e5e7eb';
            document.getElementById('card_express').style.background  = isExpress ? '#fff5f5' : 'white';
            document.getElementById('card_express').querySelector('div:nth-child(2)').style.color = isExpress ? '#dc2626' : '#374151';
        }
        // Run on page load to apply correct initial state
        document.addEventListener('DOMContentLoaded', toggleDeliveryType);

        // Translations for dynamically generated JS content
        const jsT = {
            noItemsYet:        <?= json_encode($t['no_items']) ?>,
            yourAddress:       <?= json_encode($currentLang === 'fr' ? 'Votre adresse' : 'Your Address') ?>,
            supplier:          <?= json_encode($currentLang === 'fr' ? 'Fournisseur' : 'Supplier') ?>,
            geocodingLookup:   <?= json_encode('<i class="fas fa-spinner fa-spin"></i> ' . ($currentLang === 'fr' ? "Recherche de l'adresse..." : 'Looking up address...')) ?>,
            geocodingNotFound: <?= json_encode('<i class="fas fa-exclamation-triangle"></i> ' . ($currentLang === 'fr' ? 'Code postal introuvable. Entrez la distance manuellement.' : 'Could not locate postal code. Enter distance manually.')) ?>,
            geocodingUnavail:  <?= json_encode('<i class="fas fa-exclamation-triangle"></i> ' . ($currentLang === 'fr' ? 'Géocodage indisponible. Entrez la distance manuellement.' : 'Geocoding unavailable. Enter distance manually.')) ?>,
            awaitingCoords:    <?= json_encode($currentLang === 'fr' ? 'En attente des coordonnées de livraison' : 'Awaiting delivery address coordinates') ?>,
            unknownSupLoc:     <?= json_encode($currentLang === 'fr' ? 'Emplacement de certains fournisseurs inconnu' : 'Some supplier locations unknown') ?>,
            shoppingItems:     <?= json_encode($currentLang === 'fr' ? "article(s) de la liste d'achats" : 'shopping list item(s)') ?>,
            freeDelivery:      <?= json_encode($currentLang === 'fr' ? 'Gratuit' : 'Free') ?>,
            tipCustom:         <?= json_encode($currentLang === 'fr' ? 'Pourboire (Personnalisé)' : 'Tip (Custom)') ?>,
            tipPrefix:         <?= json_encode($currentLang === 'fr' ? 'Pourboire' : 'Tip') ?>,
            phItemDesc:        <?= json_encode($currentLang === 'fr' ? "Description de l'article" : 'Item description') ?>,
            phQty:             <?= json_encode($currentLang === 'fr' ? 'Qté' : 'Qty') ?>,
            unitEach:          <?= json_encode($currentLang === 'fr' ? 'Unité' : 'Each') ?>,
            unitBox:           <?= json_encode($currentLang === 'fr' ? 'Boîte' : 'Box') ?>,
            unitCase:          <?= json_encode($currentLang === 'fr' ? 'Caisse' : 'Case') ?>,
            unitPack:          'Pack',
            unitKg:            'Kg',
            unitLb:            'Lb',
            excelEmpty:        <?= json_encode($currentLang === 'fr' ? 'Fichier vide ou sans lignes de données' : 'File is empty or has no data rows') ?>,
            excelNoDescCol:    <?= json_encode($currentLang === 'fr' ? 'Impossible de trouver la colonne "Item Description". Veuillez utiliser le modèle.' : 'Could not find "Item Description" column. Please use the template.') ?>,
            excelSuccessPrefix:<?= json_encode($currentLang === 'fr' ? 'Ajouté avec succès' : 'Successfully added') ?>,
            excelSuccessSuffix:<?= json_encode($currentLang === 'fr' ? "article(s) à votre liste d'achats" : 'item(s) to your shopping list') ?>,
            excelNoItems:      <?= json_encode($currentLang === 'fr' ? 'Aucun article valide trouvé dans le fichier' : 'No valid items found in the file') ?>,
            excelReadError:    <?= json_encode($currentLang === 'fr' ? "Erreur de lecture. Assurez-vous qu'il s'agit d'un fichier Excel valide." : "Error reading file. Please ensure it's a valid Excel file.") ?>,
            excelReadError2:   <?= json_encode($currentLang === 'fr' ? 'Erreur de lecture du fichier' : 'Error reading file') ?>,
            excelInvalidFile:  <?= json_encode($currentLang === 'fr' ? 'Veuillez télécharger un fichier Excel valide (.xlsx, .xls) ou CSV' : 'Please upload a valid Excel file (.xlsx, .xls) or CSV file') ?>,
            excelSheetName:    <?= json_encode($currentLang === 'fr' ? "Liste d'achats" : 'Shopping List') ?>,
        };

        // === Supplier coordinates and delivery coordinates from server ===
        const SUPPLIER_COORDS = <?= json_encode($supplierCoords ?? []) ?>;
        const DELIVERY_COORDS = {
            lat: <?= json_encode($businessAddress['delivery_latitude'] ?? null) ?>,
            lng: <?= json_encode($businessAddress['delivery_longitude'] ?? null) ?>
        };
        const ROAD_FACTOR = 1.3;

        // Build product-to-supplier mapping from templates
        const productToSupplier = {};
        document.querySelectorAll('template[id^="supplier-products-"]').forEach(template => {
            const supplierId = template.id.replace('supplier-products-', '');
            const clone = template.content.cloneNode(true);
            clone.querySelectorAll('.product-qty').forEach(input => {
                productToSupplier[input.dataset.productId] = supplierId;
            });
        });

        // Haversine distance in km
        function haversineDistance(lat1, lng1, lat2, lng2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // Current delivery coordinates (may be updated by geocoding)
        let currentDeliveryCoords = {
            lat: DELIVERY_COORDS.lat,
            lng: DELIVERY_COORDS.lng
        };
        let geocodingInProgress = false;

        // Geocode delivery address via our server-side proxy (avoids Nominatim CORS/User-Agent issues)
        let geocodeTimeout = null;
        function geocodeDeliveryAddress() {
            const postal   = (document.querySelector('input[name="delivery_postal_code"]')?.value || '').trim();
            const city     = (document.querySelector('input[name="delivery_city"]')?.value || '').trim();
            const street   = (document.querySelector('input[name="delivery_street"]')?.value || '').trim();
            const province = (document.querySelector('select[name="delivery_province"]')?.value || '').trim();

            if (!postal && !city) return; // nothing to geocode

            const statusEl = document.getElementById('geocodingStatus');
            statusEl.style.display = 'block';
            statusEl.className = 'geocoding-status';
            statusEl.innerHTML = jsT.geocodingLookup;
            geocodingInProgress = true;

            fetch('/api/geocode-address', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ street, city, province, postal_code: postal })
            })
            .then(r => r.json())
            .then(data => {
                geocodingInProgress = false;
                if (data.success && data.lat) {
                    currentDeliveryCoords.lat = data.lat;
                    currentDeliveryCoords.lng = data.lng;
                    statusEl.style.display = 'none';
                    document.getElementById('manualDistanceFallback').style.display = 'none';
                    updateRouteDisplay();
                    updateSummary();
                } else {
                    statusEl.className = 'geocoding-status error';
                    statusEl.innerHTML = jsT.geocodingNotFound;
                    currentDeliveryCoords.lat = null;
                    currentDeliveryCoords.lng = null;
                    showManualFallback();
                }
            })
            .catch(() => {
                geocodingInProgress = false;
                statusEl.className = 'geocoding-status error';
                statusEl.innerHTML = jsT.geocodingUnavail;
                currentDeliveryCoords.lat = null;
                currentDeliveryCoords.lng = null;
                showManualFallback();
            });
        }

        // Keep backward-compat alias used by the page-load init below
        function geocodePostalCode() { geocodeDeliveryAddress(); }

        function showManualFallback() {
            document.getElementById('manualDistanceFallback').style.display = 'flex';
            document.getElementById('manualDistance').addEventListener('input', function() {
                const dist = parseFloat(this.value) || 0;
                document.getElementById('deliveryDistance').value = dist;
                updateSummary();
            });
        }

        // Calculate and display the delivery route
        function updateRouteDisplay() {
            const routeSection = document.getElementById('deliveryRouteSection');
            const routeLegs = document.getElementById('routeLegs');
            const distanceDisplay = document.getElementById('routeDistanceDisplay');
            const hiddenInput = document.getElementById('deliveryDistance');

            // Find which suppliers have selected items
            const involvedSupplierIds = new Set();
            for (const [productId, qty] of Object.entries(selectedItems)) {
                if (qty > 0 && productToSupplier[productId]) {
                    involvedSupplierIds.add(productToSupplier[productId]);
                }
            }

            if (involvedSupplierIds.size === 0 || !currentDeliveryCoords.lat || !currentDeliveryCoords.lng) {
                // Can't calculate route
                if (involvedSupplierIds.size > 0 && (!currentDeliveryCoords.lat || !currentDeliveryCoords.lng)) {
                    // Items selected but no delivery coords - show manual
                    routeSection.style.display = 'block';
                    routeLegs.innerHTML = `<div class="route-leg"><i class="fas fa-info-circle"></i> <span>${jsT.awaitingCoords}</span></div>`;
                    distanceDisplay.textContent = '-- km';
                    showManualFallback();
                }
                return;
            }

            // Check if all involved suppliers have coordinates
            const supplierWaypoints = [];
            let missingCoords = false;
            involvedSupplierIds.forEach(sid => {
                if (SUPPLIER_COORDS[sid]) {
                    supplierWaypoints.push({
                        id: sid,
                        ...SUPPLIER_COORDS[sid]
                    });
                } else {
                    missingCoords = true;
                }
            });

            if (missingCoords || supplierWaypoints.length === 0) {
                routeSection.style.display = 'block';
                routeLegs.innerHTML = `<div class="route-leg"><i class="fas fa-exclamation-triangle"></i> <span>${jsT.unknownSupLoc}</span></div>`;
                distanceDisplay.textContent = '-- km';
                showManualFallback();
                return;
            }

            // Optimize route: start from farthest supplier, nearest-neighbor to customer
            const customerCoord = { lat: currentDeliveryCoords.lat, lng: currentDeliveryCoords.lng };
            const orderedRoute = optimizeRoute(supplierWaypoints, customerCoord);

            // Calculate total distance and build leg display
            let totalDistance = 0;
            let legsHtml = '';

            for (let i = 0; i < orderedRoute.length; i++) {
                const stop = orderedRoute[i];
                const isLast = i === orderedRoute.length - 1;
                const isFirst = i === 0;

                // Stop name
                const stopName = isLast ? jsT.yourAddress : (stop.name || jsT.supplier);
                const stopCity = isLast ? '' : (stop.city ? ` (${stop.city})` : '');
                const iconClass = isLast ? 'fas fa-map-marker-alt' : 'fas fa-warehouse';
                const destClass = isLast ? ' route-destination' : '';

                legsHtml += `<div class="route-stop${destClass}"><i class="${iconClass}"></i> ${stopName}${stopCity}</div>`;

                // Leg distance (between this stop and next)
                if (i < orderedRoute.length - 1) {
                    const next = orderedRoute[i + 1];
                    const legDist = haversineDistance(stop.lat, stop.lng, next.lat, next.lng) * ROAD_FACTOR;
                    totalDistance += legDist;

                    const nextName = (i + 1 === orderedRoute.length - 1) ? jsT.yourAddress : (next.name || jsT.supplier);
                    legsHtml += `<div class="route-leg"><i class="fas fa-arrow-down"></i> <span>${legDist.toFixed(1)} km</span></div>`;
                }
            }

            totalDistance = Math.round(totalDistance * 10) / 10;

            routeSection.style.display = 'block';
            routeLegs.innerHTML = legsHtml;
            distanceDisplay.textContent = totalDistance + ' km';
            hiddenInput.value = totalDistance;
            document.getElementById('manualDistanceFallback').style.display = 'none';
            document.getElementById('geocodingStatus').style.display = 'none';

        }

        // Nearest-neighbor route optimization
        function optimizeRoute(suppliers, customer) {
            if (suppliers.length === 0) return [customer];
            if (suppliers.length === 1) return [suppliers[0], customer];

            const unvisited = [...suppliers];
            const route = [];

            // Start from farthest supplier from customer
            let farthestIdx = 0;
            let farthestDist = 0;
            unvisited.forEach((s, i) => {
                const d = haversineDistance(s.lat, s.lng, customer.lat, customer.lng);
                if (d > farthestDist) {
                    farthestDist = d;
                    farthestIdx = i;
                }
            });

            route.push(unvisited.splice(farthestIdx, 1)[0]);

            // Nearest-neighbor for remaining
            while (unvisited.length > 0) {
                const current = route[route.length - 1];
                let nearestIdx = 0;
                let nearestDist = Infinity;
                unvisited.forEach((s, i) => {
                    const d = haversineDistance(current.lat, current.lng, s.lat, s.lng);
                    if (d < nearestDist) {
                        nearestDist = d;
                        nearestIdx = i;
                    }
                });
                route.push(unvisited.splice(nearestIdx, 1)[0]);
            }

            route.push(customer);
            return route;
        }

        // Re-geocode whenever any delivery address field changes
        ['delivery_postal_code', 'delivery_city', 'delivery_street'].forEach(name => {
            const el = document.querySelector(`input[name="${name}"]`);
            if (!el) return;
            el.addEventListener('change', () => { clearTimeout(geocodeTimeout); geocodeTimeout = setTimeout(geocodeDeliveryAddress, 400); });
            el.addEventListener('input',  () => { clearTimeout(geocodeTimeout); geocodeTimeout = setTimeout(geocodeDeliveryAddress, 900); });
        });
        const provinceSelect = document.querySelector('select[name="delivery_province"]');
        if (provinceSelect) {
            provinceSelect.addEventListener('change', () => { clearTimeout(geocodeTimeout); geocodeTimeout = setTimeout(geocodeDeliveryAddress, 400); });
        }

        // Store selected quantities across supplier switches
        let selectedItems = {};
        <?php if (!empty($old['catalog_items'])): ?>
        // Restore catalog selections from previous submit attempt
        Object.assign(selectedItems, <?= json_encode(array_map('intval', $old['catalog_items'])) ?>);
        <?php endif; ?>

        // Prevent Enter key from submitting the form in input fields
        document.getElementById('requestForm').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const target = e.target;
                const tagName = target.tagName.toLowerCase();

                // Allow Enter in textareas (for multi-line input)
                if (tagName === 'textarea') {
                    return;
                }

                // Prevent Enter from submitting in input and select fields
                if (tagName === 'input' || tagName === 'select') {
                    e.preventDefault();

                    // If it's a quantity input, update the summary and optionally move to next input
                    if (target.classList.contains('product-qty')) {
                        target.dispatchEvent(new Event('change'));
                        target.blur();
                    }

                    // For shopping list inputs, just blur
                    if (target.closest('.shopping-item')) {
                        target.blur();
                    }
                }
            }
        });

        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            });
        });

        // Supplier card click - show products
        document.querySelectorAll('.supplier-card').forEach(card => {
            card.addEventListener('click', function() {
                const supplierId = this.dataset.supplierId;
                const supplierName = this.dataset.supplierName;
                showSupplierProducts(supplierId, supplierName);
            });
        });

        // Back to suppliers
        document.getElementById('backToSuppliers').addEventListener('click', function() {
            // Save current quantities before going back
            saveCurrentQuantities();
            showSuppliersView();
        });

        function showSupplierProducts(supplierId, supplierName) {
            const template = document.getElementById('supplier-products-' + supplierId);
            const container = document.getElementById('productsContainer');

            if (template) {
                // Clone template content
                container.innerHTML = '';
                container.appendChild(template.content.cloneNode(true));

                // Restore any previously selected quantities
                container.querySelectorAll('.product-qty').forEach(input => {
                    const productId = input.dataset.productId;
                    if (selectedItems[productId]) {
                        input.value = selectedItems[productId];
                        if (selectedItems[productId] > 0) {
                            input.closest('.product-item').classList.add('selected');
                        }
                    }
                });

                // Add event listeners to new quantity inputs
                container.querySelectorAll('.product-qty').forEach(input => {
                    input.addEventListener('change', function() {
                        const item = this.closest('.product-item');
                        const productId = this.dataset.productId;
                        const qty = parseInt(this.value) || 0;

                        // Save to selectedItems
                        if (qty > 0) {
                            selectedItems[productId] = qty;
                            item.classList.add('selected');
                        } else {
                            delete selectedItems[productId];
                            item.classList.remove('selected');
                        }
                        updateSummary();
                    });
                });
            }

            document.getElementById('currentSupplierName').textContent = supplierName;
            document.getElementById('suppliersView').style.display = 'none';
            document.getElementById('supplierProducts').style.display = 'block';
            document.getElementById('catalogSearch').value = '';
        }

        function showSuppliersView() {
            document.getElementById('suppliersView').style.display = 'block';
            document.getElementById('supplierProducts').style.display = 'none';
        }

        function saveCurrentQuantities() {
            document.querySelectorAll('#productsContainer .product-qty').forEach(input => {
                const productId = input.dataset.productId;
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    selectedItems[productId] = qty;
                } else {
                    delete selectedItems[productId];
                }
            });
        }

        // Catalog search (for products view)
        document.getElementById('catalogSearch').addEventListener('input', function() {
            const search = this.value.toLowerCase();
            document.querySelectorAll('#productsContainer .product-item').forEach(item => {
                const name = item.dataset.name;
                item.style.display = name.includes(search) ? 'flex' : 'none';
            });
        });

        // Shopping list items
        let shoppingItemIndex = 0;
        document.getElementById('addShoppingItem').addEventListener('click', function() {
            const html = `
                <div class="shopping-item" data-index="${shoppingItemIndex}">
                    <input type="text" name="shopping_items[${shoppingItemIndex}][description]"
                           placeholder="${jsT.phItemDesc}" required>
                    <input type="number" name="shopping_items[${shoppingItemIndex}][quantity]"
                           placeholder="${jsT.phQty}" min="1" value="1">
                    <select name="shopping_items[${shoppingItemIndex}][unit]">
                        <option value="each">${jsT.unitEach}</option>
                        <option value="box">${jsT.unitBox}</option>
                        <option value="case">${jsT.unitCase}</option>
                        <option value="pack">${jsT.unitPack}</option>
                        <option value="kg">${jsT.unitKg}</option>
                        <option value="lb">${jsT.unitLb}</option>
                    </select>
                    <button type="button" class="btn-remove" onclick="removeShoppingItem(${shoppingItemIndex})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.getElementById('shoppingItemsList').insertAdjacentHTML('beforeend', html);
            shoppingItemIndex++;
            updateSummary();
        });

        function removeShoppingItem(index) {
            document.querySelector(`.shopping-item[data-index="${index}"]`).remove();
            updateSummary();
        }

        // Before form submit, inject hidden inputs for selected items
        document.getElementById('requestForm').addEventListener('submit', function(e) {
            // Save current quantities
            saveCurrentQuantities();

            // Remove any existing hidden catalog inputs
            this.querySelectorAll('input[name^="catalog_items["]').forEach(input => {
                if (input.type === 'hidden') {
                    input.remove();
                }
            });

            // Add hidden inputs for all selected items
            for (const [productId, qty] of Object.entries(selectedItems)) {
                if (qty > 0) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = `catalog_items[${productId}]`;
                    hidden.value = qty;
                    this.appendChild(hidden);
                }
            }
        });

        // Pricing Tiers Configuration
        const PRICING_TIERS = {
            1: { maxAmount: 500, serviceFee: 0.25, freeDeliveryKm: 5, perKmRate: 1.00, vehicle: 'Small Car/Van' },
            2: { maxAmount: 1500, serviceFee: 0.20, freeDeliveryKm: 5, perKmRate: 1.30, vehicle: 'Medium Truck/Van' },
            3: { maxAmount: 3000, serviceFee: 0.15, freeDeliveryKm: 5, perKmRate: 2.00, vehicle: 'Large Truck/Forklift' },
            4: { maxAmount: Infinity, serviceFee: 0.12, freeDeliveryKm: 5, perKmRate: 2.20, vehicle: 'Large Truck/Forklift' }
        };

        // Weight-based handling fee: $0.20 per kg
        const HANDLING_RATE_PER_KG = 0.20;

        // Currently selected tip percentage
        let selectedTipPercent = 0;

        // Tax Rates (Quebec)
        const GST_RATE = 0.05;       // 5%
        const QST_RATE = 0.09975;    // 9.975%

        // Get tier based on items total
        function getTier(itemsTotal) {
            if (itemsTotal <= 500) return 1;
            if (itemsTotal <= 1500) return 2;
            if (itemsTotal <= 3000) return 3;
            return 4;
        }

        // Calculate delivery fee
        function calculateDeliveryFee(distance, tier) {
            const tierConfig = PRICING_TIERS[tier];
            if (distance <= tierConfig.freeDeliveryKm) {
                return 0;
            }
            const extraKm = distance - tierConfig.freeDeliveryKm;
            return extraKm * tierConfig.perKmRate;
        }

        // Manual distance fallback triggers summary update
        document.getElementById('manualDistance')?.addEventListener('input', function() {
            document.getElementById('deliveryDistance').value = parseFloat(this.value) || 0;
            updateSummary();
        });

        function updateSummary() {
            let catalogTotal = 0;
            let totalWeightKg = 0;
            let catalogItems = [];

            // Build product info lookup from all templates
            const productInfo = {};
            document.querySelectorAll('template[id^="supplier-products-"]').forEach(template => {
                const clone = template.content.cloneNode(true);
                clone.querySelectorAll('.product-item').forEach(item => {
                    const input = item.querySelector('.product-qty');
                    if (input) {
                        const productId = input.dataset.productId;
                        productInfo[productId] = {
                            name: item.querySelector('.product-name').textContent,
                            price: parseFloat(input.dataset.price),
                            weight: parseFloat(input.dataset.weight) || 0
                        };
                    }
                });
            });

            // Calculate totals from selectedItems
            for (const [productId, qty] of Object.entries(selectedItems)) {
                if (qty > 0 && productInfo[productId]) {
                    const info = productInfo[productId];
                    catalogTotal += qty * info.price;
                    totalWeightKg += qty * info.weight;
                    catalogItems.push({
                        name: info.name,
                        qty: qty,
                        price: info.price,
                        total: qty * info.price
                    });
                }
            }

            const shoppingCount = document.querySelectorAll('.shopping-item').length;
            const summaryDiv = document.getElementById('summaryItems');
            const tierBadgeDiv = document.getElementById('tierBadge');
            const deliveryRouteSection = document.getElementById('deliveryRouteSection');
            const feeBreakdown = document.getElementById('feeBreakdown');
            const summarySubtotal = document.getElementById('summarySubtotal');
            const taxSection = document.getElementById('taxSection');
            const tipSection = document.getElementById('tipSection');
            const totalDiv = document.getElementById('summaryTotal');

            if (catalogItems.length === 0 && shoppingCount === 0) {
                // No items - hide everything
                summaryDiv.innerHTML = `<p class="summary-empty-text">${jsT.noItemsYet}</p>`;
                tierBadgeDiv.style.display = 'none';
                deliveryRouteSection.style.display = 'none';
                feeBreakdown.style.display = 'none';
                summarySubtotal.style.display = 'none';
                taxSection.style.display = 'none';
                tipSection.style.display = 'none';
                totalDiv.style.display = 'none';
            } else {
                // Get tier based on catalog total
                const tier = getTier(catalogTotal);
                const tierConfig = PRICING_TIERS[tier];

                // Show tier badge
                tierBadgeDiv.style.display = 'block';
                tierBadgeDiv.innerHTML = `<span class="tier-badge tier-${tier}">
                    <i class="fas fa-layer-group"></i> Tier ${tier} - ${tierConfig.vehicle}
                </span>`;

                // Build items HTML
                let html = '';
                catalogItems.forEach(item => {
                    html += `<div class="summary-item item-row">
                        <span>${item.qty}x ${item.name.substring(0, 20)}${item.name.length > 20 ? '...' : ''}</span>
                        <span>$${item.total.toFixed(2)}</span>
                    </div>`;
                });
                if (shoppingCount > 0) {
                    html += `<div class="summary-item item-row">
                        <span><i class="fas fa-list"></i> ${shoppingCount} ${jsT.shoppingItems}</span>
                        <span>TBD</span>
                    </div>`;
                }
                summaryDiv.innerHTML = html;

                // Show delivery route and recalculate
                deliveryRouteSection.style.display = 'block';
                updateRouteDisplay();

                // Get delivery distance (auto-calculated or manual)
                const distance = parseFloat(document.getElementById('deliveryDistance').value) || 0;

                // Calculate fees - weight-based handling
                const serviceFee = catalogTotal * tierConfig.serviceFee;
                const handlingFee = totalWeightKg * HANDLING_RATE_PER_KG;
                const deliveryFee = calculateDeliveryFee(distance, tier);

                // Update fee breakdown
                feeBreakdown.style.display = 'block';
                document.getElementById('itemsTotal').textContent = '$' + catalogTotal.toFixed(2) + (shoppingCount > 0 ? '+' : '');
                document.getElementById('serviceFeePercent').textContent = (tierConfig.serviceFee * 100).toFixed(0);
                document.getElementById('serviceFeeAmount').textContent = '$' + serviceFee.toFixed(2);
                document.getElementById('handlingWeightInfo').textContent = totalWeightKg.toFixed(1) + ' kg';
                document.getElementById('handlingFeeAmount').textContent = '$' + handlingFee.toFixed(2);

                // Update delivery info
                if (distance <= tierConfig.freeDeliveryKm) {
                    document.getElementById('deliveryInfo').textContent = `${jsT.freeDelivery} ≤${tierConfig.freeDeliveryKm}km`;
                    document.getElementById('deliveryFeeAmount').textContent = '$0.00';
                } else {
                    const extraKm = distance - tierConfig.freeDeliveryKm;
                    document.getElementById('deliveryInfo').textContent = `${extraKm}km × $${tierConfig.perKmRate.toFixed(2)}`;
                    document.getElementById('deliveryFeeAmount').textContent = '$' + deliveryFee.toFixed(2);
                }

                // Calculate subtotal (before tax and tip)
                const subtotal = catalogTotal + serviceFee + handlingFee + deliveryFee;
                summarySubtotal.style.display = 'flex';
                document.getElementById('subtotalAmount').textContent = '$' + subtotal.toFixed(2) + (shoppingCount > 0 ? '+' : '');

                // Calculate taxes
                const gst = subtotal * GST_RATE;
                const qst = subtotal * QST_RATE;
                taxSection.style.display = 'block';
                document.getElementById('gstAmount').textContent = '$' + gst.toFixed(2);
                document.getElementById('qstAmount').textContent = '$' + qst.toFixed(2);

                // Show tip section and calculate tip
                tipSection.style.display = 'block';
                let tipAmount = 0;
                if (selectedTipPercent === 'custom') {
                    tipAmount = parseFloat(document.getElementById('tipCustomAmount').value) || 0;
                } else if (selectedTipPercent > 0) {
                    tipAmount = catalogTotal * (selectedTipPercent / 100);
                }
                if (tipAmount > 0) {
                    document.getElementById('tipDisplay').style.display = 'flex';
                    if (selectedTipPercent === 'custom') {
                        document.getElementById('tipLabel').textContent = jsT.tipCustom;
                    } else {
                        document.getElementById('tipLabel').textContent = jsT.tipPrefix + ' (' + selectedTipPercent + '%)';
                    }
                    document.getElementById('tipAmount').textContent = '$' + tipAmount.toFixed(2);
                } else {
                    document.getElementById('tipDisplay').style.display = 'none';
                }
                // Store actual tip amount for form submission
                document.getElementById('tipCustomAmountHidden').value = tipAmount.toFixed(2);

                // Calculate total (subtotal + taxes + tip - tips are NOT taxed)
                const total = subtotal + gst + qst + tipAmount;
                totalDiv.style.display = 'flex';
                document.getElementById('totalAmount').textContent = '$' + total.toFixed(2) + (shoppingCount > 0 ? '+' : '');
            }
        }

        // Tip selection handler
        function selectTip(percent) {
            selectedTipPercent = percent;
            if (percent === 'custom') {
                document.getElementById('tipPercentage').value = 0;
                document.getElementById('tipCustomInput').style.display = 'block';
                document.getElementById('tipCustomAmount').focus();
            } else {
                document.getElementById('tipPercentage').value = percent;
                document.getElementById('tipCustomInput').style.display = 'none';
                document.getElementById('tipCustomAmount').value = '';
                document.getElementById('tipCustomAmountHidden').value = 0;
            }
            // Update active state and aria-pressed on buttons
            document.querySelectorAll('.tip-btn').forEach(btn => {
                const btnTip = btn.dataset.tip;
                const isActive = btnTip === String(percent);
                btn.classList.toggle('active', isActive);
                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
            updateSummary();
        }

        // Custom tip input handler
        function updateCustomTip() {
            updateSummary();
        }

        // ==========================================
        // Excel Upload Functionality
        // ==========================================

        // Download template
        document.getElementById('downloadTemplate').addEventListener('click', function() {
            // Create template data
            const templateData = [
                ['Item Description', 'Quantity', 'Unit', 'Preferred Brand', 'Notes'],
                ['Office Paper A4 (500 sheets)', '10', 'box', 'Staples', 'White, 80gsm'],
                ['Ballpoint Pens (Black)', '5', 'pack', '', '12 per pack'],
                ['Hand Sanitizer 500ml', '20', 'each', 'Purell', ''],
                ['Coffee Beans 1kg', '3', 'kg', 'Lavazza', 'Medium roast'],
                ['', '', '', '', '']
            ];

            // Create workbook
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_array ? XLSX.utils.aoa_to_sheet(templateData) : XLSX.utils.aoa_to_sheet(templateData);

            // Set column widths
            ws['!cols'] = [
                { wch: 35 },  // Item Description
                { wch: 10 },  // Quantity
                { wch: 10 },  // Unit
                { wch: 20 },  // Preferred Brand
                { wch: 30 }   // Notes
            ];

            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, jsT.excelSheetName);

            // Generate and download
            XLSX.writeFile(wb, 'OCS_Shopping_List_Template.xlsx');
        });

        // Handle file upload
        document.getElementById('excelFileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const resultDiv = document.getElementById('uploadResult');
            resultDiv.className = 'upload-result';
            resultDiv.style.display = 'none';

            const reader = new FileReader();

            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });

                    // Get first sheet
                    const sheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[sheetName];

                    // Convert to JSON
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                    if (jsonData.length < 2) {
                        showUploadError(jsT.excelEmpty);
                        return;
                    }

                    // Get header row (first row)
                    const headers = jsonData[0].map(h => String(h).toLowerCase().trim());

                    // Map column indices
                    const colMap = {
                        description: findColumnIndex(headers, ['item description', 'description', 'item', 'product']),
                        quantity: findColumnIndex(headers, ['quantity', 'qty', 'amount']),
                        unit: findColumnIndex(headers, ['unit', 'units', 'uom']),
                        brand: findColumnIndex(headers, ['preferred brand', 'brand', 'manufacturer']),
                        notes: findColumnIndex(headers, ['notes', 'note', 'comments', 'comment'])
                    };

                    if (colMap.description === -1) {
                        showUploadError(jsT.excelNoDescCol);
                        return;
                    }

                    // Process data rows (skip header)
                    let addedCount = 0;
                    for (let i = 1; i < jsonData.length; i++) {
                        const row = jsonData[i];
                        const description = row[colMap.description];

                        // Skip empty rows
                        if (!description || String(description).trim() === '') continue;

                        const quantity = colMap.quantity !== -1 ? (parseInt(row[colMap.quantity]) || 1) : 1;
                        const unit = colMap.unit !== -1 ? (row[colMap.unit] || 'each') : 'each';
                        const brand = colMap.brand !== -1 ? (row[colMap.brand] || '') : '';
                        const notes = colMap.notes !== -1 ? (row[colMap.notes] || '') : '';

                        // Add shopping item
                        addShoppingItemFromExcel(description, quantity, unit, brand, notes);
                        addedCount++;
                    }

                    if (addedCount > 0) {
                        showUploadSuccess(`${jsT.excelSuccessPrefix} ${addedCount} ${jsT.excelSuccessSuffix}`);
                        updateSummary();
                    } else {
                        showUploadError(jsT.excelNoItems);
                    }

                } catch (err) {
                    console.error('Excel parse error:', err);
                    showUploadError(jsT.excelReadError);
                }
            };

            reader.onerror = function() {
                showUploadError(jsT.excelReadError2);
            };

            reader.readAsArrayBuffer(file);

            // Reset file input for re-upload
            e.target.value = '';
        });

        function findColumnIndex(headers, possibleNames) {
            for (const name of possibleNames) {
                const idx = headers.indexOf(name);
                if (idx !== -1) return idx;
            }
            return -1;
        }

        function addShoppingItemFromExcel(description, quantity, unit, brand, notes) {
            // Build full description with brand and notes
            let fullDescription = String(description).trim();
            if (brand && String(brand).trim()) {
                fullDescription += ' (Brand: ' + String(brand).trim() + ')';
            }
            if (notes && String(notes).trim()) {
                fullDescription += ' - ' + String(notes).trim();
            }

            // Map unit to valid select value
            const validUnits = ['each', 'box', 'case', 'pack', 'kg', 'lb'];
            let mappedUnit = String(unit).toLowerCase().trim();
            if (!validUnits.includes(mappedUnit)) {
                mappedUnit = 'each';
            }

            const html = `
                <div class="shopping-item" data-index="${shoppingItemIndex}">
                    <input type="text" name="shopping_items[${shoppingItemIndex}][description]"
                           placeholder="${jsT.phItemDesc}" value="${escapeHtml(fullDescription)}" required>
                    <input type="number" name="shopping_items[${shoppingItemIndex}][quantity]"
                           placeholder="${jsT.phQty}" min="1" value="${quantity}">
                    <select name="shopping_items[${shoppingItemIndex}][unit]">
                        <option value="each" ${mappedUnit === 'each' ? 'selected' : ''}>${jsT.unitEach}</option>
                        <option value="box" ${mappedUnit === 'box' ? 'selected' : ''}>${jsT.unitBox}</option>
                        <option value="case" ${mappedUnit === 'case' ? 'selected' : ''}>${jsT.unitCase}</option>
                        <option value="pack" ${mappedUnit === 'pack' ? 'selected' : ''}>${jsT.unitPack}</option>
                        <option value="kg" ${mappedUnit === 'kg' ? 'selected' : ''}>${jsT.unitKg}</option>
                        <option value="lb" ${mappedUnit === 'lb' ? 'selected' : ''}>${jsT.unitLb}</option>
                    </select>
                    <button type="button" class="btn-remove" onclick="removeShoppingItem(${shoppingItemIndex})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.getElementById('shoppingItemsList').insertAdjacentHTML('beforeend', html);
            shoppingItemIndex++;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML.replace(/"/g, '&quot;');
        }

        function showUploadSuccess(message) {
            const resultDiv = document.getElementById('uploadResult');
            resultDiv.className = 'upload-result success';
            resultDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
        }

        function showUploadError(message) {
            const resultDiv = document.getElementById('uploadResult');
            resultDiv.className = 'upload-result error';
            resultDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
        }

        // Drag and drop support
        const uploadSection = document.getElementById('excelUploadSection');

        uploadSection.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadSection.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadSection.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                const validTypes = [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel',
                    'text/csv'
                ];
                const validExtensions = ['.xlsx', '.xls', '.csv'];
                const ext = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();

                if (validTypes.includes(file.type) || validExtensions.includes(ext)) {
                    // Trigger the file input change handler
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('excelFileInput').files = dataTransfer.files;
                    document.getElementById('excelFileInput').dispatchEvent(new Event('change'));
                } else {
                    showUploadError(jsT.excelInvalidFile);
                }
            }
        });
        // === Restore state from previous submit attempt ===
        <?php if (!empty($old)): ?>
        (function() {
            <?php if (!empty($old['shopping_items'])): ?>
            // Restore shopping list items
            const oldShoppingItems = <?= json_encode(array_values($old['shopping_items'])) ?>;
            oldShoppingItems.forEach(function(item) {
                const desc = item.description || '';
                const qty  = parseInt(item.quantity) || 1;
                const unit = item.unit || 'each';
                if (desc.trim()) {
                    addShoppingItemFromExcel(desc, qty, unit, '', '');
                }
            });
            // Switch to shopping list tab only if no catalog items were selected
            if (oldShoppingItems.length > 0 && Object.keys(selectedItems).length === 0) {
                document.querySelector('.tab-btn[data-tab="shopping"]')?.click();
            }
            <?php endif; ?>

            <?php $oldTip = (int)($old['tip_percentage'] ?? 0); $oldCustomTip = (float)($old['tip_custom_amount'] ?? 0); ?>
            <?php if ($oldTip > 0 || $oldCustomTip > 0): ?>
            // Restore tip selection
            <?php if ($oldTip > 0): ?>
            selectTip(<?= $oldTip ?>);
            <?php elseif ($oldCustomTip > 0): ?>
            selectTip('custom');
            document.getElementById('tipCustomAmount').value = '<?= number_format($oldCustomTip, 2, '.', '') ?>';
            document.getElementById('tipCustomAmountHidden').value = '<?= number_format($oldCustomTip, 2, '.', '') ?>';
            <?php endif; ?>
            <?php endif; ?>

            // Refresh summary with restored data
            updateSummary();

            // Scroll to first error field
            <?php if (!empty($errors)): ?>
            const firstErr = document.querySelector('.form-input.error');
            if (firstErr) {
                setTimeout(function() {
                    firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstErr.focus();
                }, 100);
            }
            <?php endif; ?>
        })();
        <?php endif; ?>

        // === Initial geocoding on page load ===
        (function() {
            if (currentDeliveryCoords.lat && currentDeliveryCoords.lng) {
                // Already have stored coordinates — render route immediately
                updateRouteDisplay();
                return;
            }
            // Geocode the pre-filled address using our server-side proxy
            const hasPostal = (document.querySelector('input[name="delivery_postal_code"]')?.value || '').trim().length >= 3;
            const hasCity   = (document.querySelector('input[name="delivery_city"]')?.value || '').trim().length >= 2;
            if (hasPostal || hasCity) {
                geocodeDeliveryAddress();
            }
        })();
    </script>
<?php require __DIR__ . '/../layout-footer.php'; ?>
