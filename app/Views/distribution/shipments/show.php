<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'portal_sub'        => 'Distribution Portal',
        'back_shipments'    => 'Back to Shipments',
        'type_parcel'       => 'Parcel Delivery',
        'type_fulfillment'  => 'Product Fulfillment',
        'type_multi'        => 'Multi-Drop Route',
        'type_shipment'     => 'Shipment',
        'created'           => 'Created',
        'btn_edit'          => 'Edit',
        'btn_submit'        => 'Submit',
        'btn_pay_now'       => 'Pay Now',
        'btn_cancel'        => 'Cancel',
        'cancel_confirm'    => 'Are you sure you want to cancel this shipment?',
        'status_draft'      => 'Draft - Not yet submitted',
        'status_submitted'  => 'Awaiting Quote',
        'status_quoted'     => 'Quote Ready - Awaiting Payment',
        'status_pending_payment' => 'Payment Pending',
        'status_paid'       => 'Payment Received - Scheduling',
        'status_scheduled'  => 'Pickup Scheduled',
        'status_picked_up'  => 'Package Picked Up',
        'status_in_transit' => 'In Transit',
        'status_delivered'  => 'Delivered',
        'status_completed'  => 'Completed',
        'status_cancelled'  => 'Cancelled',
        'scheduled_for'     => 'Scheduled for',
        'requested_pickup'  => 'Requested pickup',
        'sec_locations'     => 'Locations',
        'lbl_pickup'        => 'Pickup',
        'lbl_delivery_stops'=> 'Delivery Stops',
        'lbl_delivery'      => 'Delivery',
        'sec_package'       => 'Package Details',
        'lbl_total_packages'=> 'Total Packages',
        'lbl_total_weight'  => 'Total Weight',
        'not_specified'     => 'Not specified',
        'lbl_description'   => 'Description',
        'sec_items'         => 'Items',
        'col_item'          => 'Item',
        'col_sku'           => 'SKU',
        'col_qty'           => 'Qty',
        'col_value'         => 'Value',
        'sec_notes'         => 'Notes',
        'sec_quote'         => 'Quote',
        'base_rate'         => 'Base Rate',
        'stops_label'       => 'Stops',
        'weight_surcharge'  => 'Weight Surcharge',
        'distance_surcharge'=> 'Distance Surcharge',
        'rush_surcharge'    => 'Rush Surcharge',
        'subtotal'          => 'Subtotal',
        'tax_label'         => 'Tax',
        'total_label'       => 'Total',
        'quote_valid'       => 'Quote valid until',
        'sec_status_history'=> 'Status History',
        'no_status_updates' => 'No status updates yet.',
        'sec_share_tracking'=> 'Share Tracking',
        'share_desc'        => 'Share this link with your recipient to track the shipment:',
        'copied_alert'      => 'Tracking URL copied to clipboard!',
    ],
    'fr' => [
        'portal_sub'        => 'Portail de distribution',
        'back_shipments'    => 'Retour aux envois',
        'type_parcel'       => 'Livraison de colis',
        'type_fulfillment'  => 'Ex&#233;cution de commande',
        'type_multi'        => 'Route multi-arr&#234;ts',
        'type_shipment'     => 'Envoi',
        'created'           => 'Cr&#233;&#233; le',
        'btn_edit'          => 'Modifier',
        'btn_submit'        => 'Soumettre',
        'btn_pay_now'       => 'Payer maintenant',
        'btn_cancel'        => 'Annuler',
        'cancel_confirm'    => 'Voulez-vous vraiment annuler cet envoi\u00a0?',
        'status_draft'      => 'Brouillon &#8212; non soumis',
        'status_submitted'  => 'En attente de cotation',
        'status_quoted'     => 'Cotation pr&#234;te &#8212; en attente de paiement',
        'status_pending_payment' => 'Paiement en attente',
        'status_paid'       => 'Paiement re&#231;u &#8212; planification en cours',
        'status_scheduled'  => 'Ramassage planifi&#233;',
        'status_picked_up'  => 'Colis ramass&#233;',
        'status_in_transit' => 'En transit',
        'status_delivered'  => 'Livr&#233;',
        'status_completed'  => 'Compl&#233;t&#233;',
        'status_cancelled'  => 'Annul&#233;',
        'scheduled_for'     => 'Planifi&#233; pour le',
        'requested_pickup'  => 'Ramassage demand&#233; le',
        'sec_locations'     => 'Emplacements',
        'lbl_pickup'        => 'Ramassage',
        'lbl_delivery_stops'=> 'Arr&#234;ts de livraison',
        'lbl_delivery'      => 'Livraison',
        'sec_package'       => 'D&#233;tails du colis',
        'lbl_total_packages'=> 'Total des colis',
        'lbl_total_weight'  => 'Poids total',
        'not_specified'     => 'Non sp&#233;cifi&#233;',
        'lbl_description'   => 'Description',
        'sec_items'         => 'Articles',
        'col_item'          => 'Article',
        'col_sku'           => 'R&#233;f.',
        'col_qty'           => 'Qt&#233;',
        'col_value'         => 'Valeur',
        'sec_notes'         => 'Notes',
        'sec_quote'         => 'Cotation',
        'base_rate'         => 'Tarif de base',
        'stops_label'       => 'Arr&#234;ts',
        'weight_surcharge'  => 'Suppl&#233;ment de poids',
        'distance_surcharge'=> 'Suppl&#233;ment de distance',
        'rush_surcharge'    => 'Suppl&#233;ment urgence',
        'subtotal'          => 'Sous-total',
        'tax_label'         => 'Taxe',
        'total_label'       => 'Total',
        'quote_valid'       => 'Cotation valable jusqu\'au',
        'sec_status_history'=> 'Historique du statut',
        'no_status_updates' => 'Aucune mise &#224; jour de statut pour l\'instant.',
        'sec_share_tracking'=> 'Partager le suivi',
        'share_desc'        => 'Partagez ce lien avec votre destinataire pour suivre l\'envoi&#160;:',
        'copied_alert'      => 'URL de suivi copi&#233;e dans le presse-papiers\u00a0!',
    ],
];
$currentPage = 'shipments';
$pageTitle = $currentLang === 'fr' ? "Détails de l'envoi" : 'Shipment Details';
$_pageTranslations = $translations; // save before layout-header.php overwrites $translations
require __DIR__ . '/../layout-header.php';
$t = $_pageTranslations[$currentLang] ?? $_pageTranslations['en'];
unset($_pageTranslations);
?>
<div class="page-header">
            <div>
                <h1 class="page-title"><?= htmlspecialchars($shipment['shipment_number']) ?></h1>
                <p class="page-subtitle">
                    <?php
                    $typeLabels = [
                        'parcel'              => $t['type_parcel'],
                        'product_fulfillment' => $t['type_fulfillment'],
                        'multi_drop'          => $t['type_multi'],
                    ];
                    echo $typeLabels[$shipment['shipment_type']] ?? $t['type_shipment'];
                    ?>
                    &bull; <?= $t['created'] ?> <?= date('M j, Y', strtotime($shipment['created_at'])) ?>
                </p>
            </div>
            <div class="header-actions">
                <?php if ($shipment['status'] === 'draft'): ?>
                    <a href="<?= url('distribution/shipments/edit?id=' . $shipment['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> <?= $t['btn_edit'] ?>
                    </a>
                    <form action="<?= url('distribution/shipments/submit') ?>" method="POST" style="display: inline;">
                        <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="shipment_id" value="<?= $shipment['id'] ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> <?= $t['btn_submit'] ?>
                        </button>
                    </form>
                <?php endif; ?>
                <?php if ($shipment['status'] === 'quoted'): ?>
                    <a href="<?= url('distribution/payment/checkout?shipment_id=' . $shipment['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> <?= $t['btn_pay_now'] ?>
                    </a>
                <?php endif; ?>
                <?php if (in_array($shipment['status'], ['draft', 'submitted', 'quoted', 'pending_payment'])): ?>
                    <form action="<?= url('distribution/shipments/cancel') ?>" method="POST" style="display: inline;"
                          onsubmit="return confirm('<?= addslashes(html_entity_decode($t['cancel_confirm'], ENT_QUOTES, 'UTF-8')) ?>');">
                        <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="shipment_id" value="<?= $shipment['id'] ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> <?= $t['btn_cancel'] ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status Banner -->
        <?php
        $statusMessages = [
            'draft'           => $t['status_draft'],
            'submitted'       => $t['status_submitted'],
            'quoted'          => $t['status_quoted'],
            'pending_payment' => $t['status_pending_payment'],
            'paid'            => $t['status_paid'],
            'scheduled'       => $t['status_scheduled'],
            'picked_up'       => $t['status_picked_up'],
            'in_transit'      => $t['status_in_transit'],
            'delivered'       => $t['status_delivered'],
            'completed'       => $t['status_completed'],
            'cancelled'       => $t['status_cancelled'],
        ];
        ?>
        <div class="status-banner <?= $shipment['status'] ?>">
            <div class="status-info">
                <h3><?= $statusMessages[$shipment['status']] ?? ucwords($shipment['status']) ?></h3>
                <p>
                    <?php if ($shipment['scheduled_for']): ?>
                        <?= $t['scheduled_for'] ?> <?= date('M j, Y', strtotime($shipment['scheduled_for'])) ?>
                    <?php elseif ($shipment['requested_pickup_date']): ?>
                        <?= $t['requested_pickup'] ?>: <?= date('M j, Y', strtotime($shipment['requested_pickup_date'])) ?>
                    <?php endif; ?>
                </p>
            </div>
            <span class="badge badge-<?= $shipment['status'] ?>">
                <?= ucwords(str_replace('_', ' ', $shipment['status'])) ?>
            </span>
        </div>

        <div class="content-grid">
            <div>
                <!-- Addresses -->
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-map-marker-alt"></i> <?= $t['sec_locations'] ?>
                    </div>

                    <div class="address-block">
                        <h4><i class="fas fa-box"></i> <?= $t['lbl_pickup'] ?></h4>
                        <p>
                            <?= htmlspecialchars($shipment['pickup_street']) ?><br>
                            <?= htmlspecialchars($shipment['pickup_city']) ?>, <?= htmlspecialchars($shipment['pickup_province']) ?> <?= htmlspecialchars($shipment['pickup_postal_code']) ?>
                            <?php if ($shipment['pickup_contact_name']): ?>
                                <br><small><?= htmlspecialchars($shipment['pickup_contact_name']) ?> <?= $shipment['pickup_contact_phone'] ? '- ' . htmlspecialchars($shipment['pickup_contact_phone']) : '' ?></small>
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if ($shipment['is_multi_drop'] && !empty($destinations)): ?>
                        <h4 style="font-size: 13px; color: #666; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-flag-checkered"></i> <?= $t['lbl_delivery_stops'] ?> (<?= count($destinations) ?>)
                        </h4>
                        <ul class="destination-list">
                            <?php foreach ($destinations as $dest): ?>
                                <li class="destination-item">
                                    <div class="destination-number"><?= $dest['sequence_order'] ?></div>
                                    <div class="destination-details">
                                        <div class="destination-name"><?= htmlspecialchars($dest['destination_name']) ?></div>
                                        <div class="destination-address">
                                            <?= htmlspecialchars($dest['street']) ?>, <?= htmlspecialchars($dest['city']) ?>, <?= htmlspecialchars($dest['province']) ?>
                                            <?php if ($dest['contact_name']): ?>
                                                <br><?= htmlspecialchars($dest['contact_name']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <span class="destination-status badge-<?= $dest['status'] ?>">
                                        <?= ucwords($dest['status']) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="address-block">
                            <h4><i class="fas fa-flag-checkered"></i> <?= $t['lbl_delivery'] ?></h4>
                            <p>
                                <?= htmlspecialchars($shipment['destination_street']) ?><br>
                                <?= htmlspecialchars($shipment['destination_city']) ?>, <?= htmlspecialchars($shipment['destination_province']) ?> <?= htmlspecialchars($shipment['destination_postal_code']) ?>
                                <?php if ($shipment['destination_contact_name']): ?>
                                    <br><small><?= htmlspecialchars($shipment['destination_contact_name']) ?> <?= $shipment['destination_contact_phone'] ? '- ' . htmlspecialchars($shipment['destination_contact_phone']) : '' ?></small>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Package Details -->
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-boxes"></i> <?= $t['sec_package'] ?>
                    </div>
                    <div class="info-grid">
                        <div class="info-group">
                            <div class="info-label"><?= $t['lbl_total_packages'] ?></div>
                            <div class="info-value"><?= $shipment['total_packages'] ?? 1 ?></div>
                        </div>
                        <div class="info-group">
                            <div class="info-label"><?= $t['lbl_total_weight'] ?></div>
                            <div class="info-value"><?= $shipment['total_weight_kg'] ? $shipment['total_weight_kg'] . ' kg' : $t['not_specified'] ?></div>
                        </div>
                    </div>
                    <?php if ($shipment['package_description']): ?>
                        <div class="info-group">
                            <div class="info-label"><?= $t['lbl_description'] ?></div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($shipment['package_description'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Items -->
                <?php if (!empty($items)): ?>
                    <div class="section-card">
                        <div class="section-title">
                            <i class="fas fa-list-alt"></i> <?= $t['sec_items'] ?> (<?= count($items) ?>)
                        </div>
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 10px; text-align: left; font-size: 12px; color: #666;"><?= $t['col_item'] ?></th>
                                    <th style="padding: 10px; text-align: left; font-size: 12px; color: #666;"><?= $t['col_sku'] ?></th>
                                    <th style="padding: 10px; text-align: center; font-size: 12px; color: #666;"><?= $t['col_qty'] ?></th>
                                    <th style="padding: 10px; text-align: right; font-size: 12px; color: #666;"><?= $t['col_value'] ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 12px;"><?= htmlspecialchars($item['item_name']) ?></td>
                                        <td style="padding: 12px; color: #666;"><?= htmlspecialchars($item['item_sku'] ?? '-') ?></td>
                                        <td style="padding: 12px; text-align: center;"><?= $item['quantity'] ?></td>
                                        <td style="padding: 12px; text-align: right;"><?= $item['unit_value'] ? '$' . number_format($item['unit_value'], 2) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Notes -->
                <?php if ($shipment['business_notes']): ?>
                    <div class="section-card">
                        <div class="section-title">
                            <i class="fas fa-sticky-note"></i> <?= $t['sec_notes'] ?>
                        </div>
                        <p style="font-size: 14px; color: #666; line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($shipment['business_notes'])) ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <!-- Quote/Payment -->
                <?php if ($quote): ?>
                    <div class="section-card">
                        <div class="section-title">
                            <i class="fas fa-file-invoice-dollar"></i> <?= $t['sec_quote'] ?>
                        </div>
                        <div class="quote-summary">
                            <div class="quote-row">
                                <span><?= $t['base_rate'] ?></span>
                                <span>$<?= number_format($quote['base_rate'], 2) ?></span>
                            </div>
                            <?php if ($quote['stops_total'] > 0): ?>
                                <div class="quote-row">
                                    <span><?= $t['stops_label'] ?> (<?= $quote['stops_count'] ?> x $<?= number_format($quote['per_stop_rate'], 2) ?>)</span>
                                    <span>$<?= number_format($quote['stops_total'], 2) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($quote['weight_surcharge'] > 0): ?>
                                <div class="quote-row">
                                    <span><?= $t['weight_surcharge'] ?></span>
                                    <span>$<?= number_format($quote['weight_surcharge'], 2) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($quote['distance_surcharge'] > 0): ?>
                                <div class="quote-row">
                                    <span><?= $t['distance_surcharge'] ?></span>
                                    <span>$<?= number_format($quote['distance_surcharge'], 2) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($quote['rush_surcharge'] > 0): ?>
                                <div class="quote-row">
                                    <span><?= $t['rush_surcharge'] ?></span>
                                    <span>$<?= number_format($quote['rush_surcharge'], 2) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="quote-row">
                                <span><?= $t['subtotal'] ?></span>
                                <span>$<?= number_format($quote['subtotal'], 2) ?></span>
                            </div>
                            <div class="quote-row">
                                <span><?= $t['tax_label'] ?> (<?= number_format($quote['tax_rate'], 2) ?>%)</span>
                                <span>$<?= number_format($quote['tax_amount'], 2) ?></span>
                            </div>
                            <div class="quote-row total">
                                <span><?= $t['total_label'] ?></span>
                                <span>$<?= number_format($quote['total_amount'], 2) ?></span>
                            </div>
                        </div>
                        <p style="font-size: 12px; color: #666; margin-top: 12px; text-align: center;">
                            <?= $t['quote_valid'] ?> <?= date('M j, Y', strtotime($quote['valid_until'])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Status Timeline -->
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-history"></i> <?= $t['sec_status_history'] ?>
                    </div>
                    <?php if (empty($statusHistory)): ?>
                        <p style="font-size: 14px; color: #666; text-align: center; padding: 20px;">
                            <?= $t['no_status_updates'] ?>
                        </p>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($statusHistory as $history): ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <h4><?= ucwords(str_replace('_', ' ', $history['new_status'])) ?></h4>
                                        <?php if ($history['notes']): ?>
                                            <p><?= htmlspecialchars($history['notes']) ?></p>
                                        <?php endif; ?>
                                        <time><?= date('M j, Y g:i A', strtotime($history['created_at'])) ?></time>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tracking Link -->
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-share-alt"></i> <?= $t['sec_share_tracking'] ?>
                    </div>
                    <p style="font-size: 13px; color: #666; margin-bottom: 12px;">
                        <?= $t['share_desc'] ?>
                    </p>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" readonly value="<?= url('distribution/shipments/track?number=' . $shipment['shipment_number']) ?>"
                               style="flex: 1; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 12px;" id="trackingUrl">
                        <button onclick="copyTrackingUrl()" class="btn btn-secondary" style="padding: 10px 16px;">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    <script>
        function copyTrackingUrl() {
            const input = document.getElementById('trackingUrl');
            input.select();
            document.execCommand('copy');
            alert(<?= json_encode(html_entity_decode($t['copied_alert'], ENT_QUOTES, 'UTF-8')) ?>);
        }
    </script>
<?php require __DIR__ . '/../layout-footer.php'; ?>
