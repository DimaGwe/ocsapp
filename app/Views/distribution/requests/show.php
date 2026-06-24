<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'portal_sub'        => 'Distribution Portal',
        'nav_dashboard'     => 'Dashboard',
        'nav_requests'      => 'Requests',
        'nav_logout'        => 'Logout',
        'breadcrumb_req'    => 'Requests',
        'btn_edit'          => 'Edit',
        'btn_submit'        => 'Submit Request',
        'btn_delete'        => 'Delete',
        'btn_pay_now'       => 'Pay Now',
        'btn_cancel'        => 'Cancel',
        'status_draft_msg'  => "Ready to submit when you're done editing",
        'status_pending_msg'=> 'Awaiting review from our team',
        'status_approved_msg'=> 'Approved! Payment link sent to your email',
        'status_paid_msg'   => 'Payment received - procurement starting soon',
        'status_procurement_msg' => "We're procuring your items from suppliers",
        'status_processing_msg'  => 'Supplier(s) are preparing your order and should be ready for pick up shortly.',
        'status_in_transit_msg'  => 'Your order is on its way!',
        'status_delivered_msg'   => 'Order delivered successfully',
        'status_cancelled_msg'   => 'This request has been cancelled',
        'label_delivery_address' => 'Delivery Address',
        'label_preferred_date'   => 'Preferred Delivery Date',
        'label_created'          => 'Created',
        'label_submitted'        => 'Submitted',
        'not_specified'          => 'Not specified',
        'label_notes'            => 'Notes',
        'card_catalog_items'     => 'Catalog Items',
        'col_product'            => 'Product',
        'col_qty'                => 'Qty',
        'col_unit_price'         => 'Unit Price',
        'col_total'              => 'Total',
        'card_shopping_items'    => 'Shopping List Items',
        'col_description'        => 'Description',
        'col_unit'               => 'Unit',
        'col_est_price'          => 'Est. Price',
        'col_actual_price'       => 'Actual Price',
        'pending_label'          => 'Pending',
        'card_summary'           => 'Summary',
        'subtotal'               => 'Subtotal',
        'tax_label'              => 'Tax',
        'delivery_fee'           => 'Delivery Fee',
        'total_label'            => 'Total',
        'items_total'            => 'Items Total',
        'service_fee'            => 'Service Fee',
        'handling'               => 'Handling',
        'free_delivery'          => 'Free',
        'gst'                    => 'GST (5%)',
        'qst'                    => 'QST (9.975%)',
        'tip'                    => 'Tip',
        'tip_custom'             => 'Custom',
        'est_total'              => 'Estimated Total',
        'shopping_note'          => 'Shopping list items will be quoted after review. Final total may vary.',
        'card_invoice'           => 'Invoice',
        'label_invoice_number'   => 'Invoice Number',
        'label_status'           => 'Status',
        'label_due_date'         => 'Due Date',
        'card_documents'         => 'Documents',
        'download_invoice'       => 'Download Invoice',
        'download_po'            => 'Download Purchase Order',
        'card_activity'          => 'Activity',
        'no_activity'            => 'No activity recorded yet.',
        'modal_delete_title'     => 'Delete Draft',
        'modal_delete_confirm'   => 'Are you sure you want to delete this draft request? This action cannot be undone.',
        'btn_keep_draft'         => 'Keep Draft',
        'btn_yes_delete'         => 'Yes, Delete',
        'modal_cancel_title'     => 'Cancel Request',
        'modal_cancel_confirm'   => 'Are you sure you want to cancel this request? This action cannot be undone.',
        'reason_optional'        => 'Reason (optional)',
        'reason_placeholder'     => 'Why are you cancelling?',
        'btn_keep_request'       => 'Keep Request',
        'btn_yes_cancel'         => 'Yes, Cancel',
        'catalog_items_label'    => 'Catalog Items',
        'shopping_items_label'   => 'Shopping Items',
    ],
    'fr' => [
        'portal_sub'        => 'Portail de distribution',
        'nav_dashboard'     => 'Tableau de bord',
        'nav_requests'      => 'Demandes',
        'nav_logout'        => 'D&#233;connexion',
        'breadcrumb_req'    => 'Demandes',
        'btn_edit'          => 'Modifier',
        'btn_submit'        => 'Soumettre la demande',
        'btn_delete'        => 'Supprimer',
        'btn_pay_now'       => 'Payer maintenant',
        'btn_cancel'        => 'Annuler',
        'status_draft_msg'  => 'Pr&#234;t &#224; soumettre lorsque vous avez termin&#233;',
        'status_pending_msg'=> 'En attente d\'examen par notre &#233;quipe',
        'status_approved_msg'=> 'Approuv&#233;&#160;! Lien de paiement envoy&#233; &#224; votre courriel',
        'status_paid_msg'   => 'Paiement re&#231;u &#8212; approvisionnement d&#233;butant bient&#244;t',
        'status_procurement_msg' => 'Nous approvisionnons vos articles aupr&#232;s des fournisseurs',
        'status_processing_msg'  => 'Le(s) fournisseur(s) pr&#233;parent votre commande et elle devrait &#234;tre pr&#234;te pour la cueillette sous peu.',
        'status_in_transit_msg'  => 'Votre commande est en route&#160;!',
        'status_delivered_msg'   => 'Commande livr&#233;e avec succ&#232;s',
        'status_cancelled_msg'   => 'Cette demande a &#233;t&#233; annul&#233;e',
        'label_delivery_address' => 'Adresse de livraison',
        'label_preferred_date'   => 'Date de livraison pr&#233;f&#233;r&#233;e',
        'label_created'          => 'Cr&#233;&#233;e le',
        'label_submitted'        => 'Soumise le',
        'not_specified'          => 'Non sp&#233;cifi&#233;',
        'label_notes'            => 'Notes',
        'card_catalog_items'     => 'Articles du catalogue',
        'col_product'            => 'Produit',
        'col_qty'                => 'Qt&#233;',
        'col_unit_price'         => 'Prix unitaire',
        'col_total'              => 'Total',
        'card_shopping_items'    => 'Articles de la liste d\'achats',
        'col_description'        => 'Description',
        'col_unit'               => 'Unit&#233;',
        'col_est_price'          => 'Prix estim&#233;',
        'col_actual_price'       => 'Prix r&#233;el',
        'pending_label'          => 'En attente',
        'card_summary'           => 'R&#233;sum&#233;',
        'subtotal'               => 'Sous-total',
        'tax_label'              => 'Taxe',
        'delivery_fee'           => 'Frais de livraison',
        'total_label'            => 'Total',
        'items_total'            => 'Total des articles',
        'service_fee'            => 'Frais de service',
        'handling'               => 'Manutention',
        'free_delivery'          => 'Gratuit',
        'gst'                    => 'TPS (5\u00a0%)',
        'qst'                    => 'TVQ (9,975\u00a0%)',
        'tip'                    => 'Pourboire',
        'tip_custom'             => 'Personnalis&#233;',
        'est_total'              => 'Total estim&#233;',
        'shopping_note'          => 'Les articles de la liste d\'achats seront cotis&#233;s apr&#232;s examen. Le total final peut varier.',
        'card_invoice'           => 'Facture',
        'label_invoice_number'   => 'Num&#233;ro de facture',
        'label_status'           => 'Statut',
        'label_due_date'         => 'Date d\'&#233;ch&#233;ance',
        'card_documents'         => 'Documents',
        'download_invoice'       => 'T&#233;l&#233;charger la facture',
        'download_po'            => 'T&#233;l&#233;charger le bon de commande',
        'card_activity'          => 'Activit&#233;',
        'no_activity'            => 'Aucune activit&#233; enregistr&#233;e pour l\'instant.',
        'modal_delete_title'     => 'Supprimer le brouillon',
        'modal_delete_confirm'   => 'Voulez-vous vraiment supprimer ce brouillon&#160;? Cette action est irr&#233;versible.',
        'btn_keep_draft'         => 'Garder le brouillon',
        'btn_yes_delete'         => 'Oui, supprimer',
        'modal_cancel_title'     => 'Annuler la demande',
        'modal_cancel_confirm'   => 'Voulez-vous vraiment annuler cette demande&#160;? Cette action est irr&#233;versible.',
        'reason_optional'        => 'Raison (facultatif)',
        'reason_placeholder'     => 'Pourquoi annulez-vous\u00a0?',
        'btn_keep_request'       => 'Garder la demande',
        'btn_yes_cancel'         => 'Oui, annuler',
        'catalog_items_label'    => 'Articles du catalogue',
        'shopping_items_label'   => 'Articles de la liste',
    ],
];
$currentPage = 'requests';
$pageTitle = $currentLang === 'fr' ? 'Détails de la demande' : 'Request Details';
$_pageT = $translations[$currentLang] ?? $translations['en'];
require __DIR__ . '/../layout-header.php';
$t = $_pageT; unset($_pageT);

// ── Status bar ────────────────────────────────────────────────────────────
$drStatus = $request['status'] ?? 'draft';

$stageMap = [
    'submitted'        => 1,
    'pending'          => 1,
    'approved'         => 2,
    'awaiting_supplier'=> 2,
    'quoted'           => 3,
    'pending_payment'  => 3,
    'awaiting_payment' => 3,
    'paid'             => 4,
    'procurement'      => 4,
    'processing'       => 4,
    'ready'            => 5,  // driver assigned, awaiting acceptance
    'in_transit'       => 7,  // driver collected all items, en route to business
    'delivered'        => 8,
    'completed'        => 8,
];
$drActiveIdx = $stageMap[$drStatus] ?? 0;
// Advance to stage 6 (Collecting) once driver has accepted the assignment
if ($drStatus === 'ready' && !empty($driverAccepted)) {
    $drActiveIdx = 6;
}

$drStages = $currentLang === 'fr' ? [
    ['label' => 'Soumis',            'icon' => 'paper-plane'],
    ['label' => 'Approuvé',          'icon' => 'clipboard-check'],
    ['label' => 'Paiement',          'icon' => 'credit-card'],
    ['label' => 'Payé',              'icon' => 'check-circle'],
    ['label' => 'Chauffeur assigné', 'icon' => 'user-check'],
    ['label' => 'Cueillette',        'icon' => 'box-open'],
    ['label' => 'En Route',          'icon' => 'truck-moving'],
    ['label' => 'Livré',             'icon' => 'check-double'],
] : [
    ['label' => 'Submitted',         'icon' => 'paper-plane'],
    ['label' => 'Approved',          'icon' => 'clipboard-check'],
    ['label' => 'Awaiting Payment',  'icon' => 'credit-card'],
    ['label' => 'Paid',              'icon' => 'check-circle'],
    ['label' => 'Driver Assigned',   'icon' => 'user-check'],
    ['label' => 'Collecting',        'icon' => 'box-open'],
    ['label' => 'Delivering',        'icon' => 'truck-moving'],
    ['label' => 'Delivered',         'icon' => 'check-double'],
];
?>
        <style>
          .dr-progress-wrap { background: white; border-radius: 12px; padding: 24px 28px 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
          .dr-progress-track { display: flex; align-items: flex-start; justify-content: space-between; position: relative; }
          .dr-progress-track::before { content: ''; position: absolute; top: 20px; left: 0; right: 0; height: 3px; background: #e5e7eb; z-index: 0; }
          .dr-stage-step { display: flex; flex-direction: column; align-items: center; gap: 6px; flex: 1; position: relative; z-index: 1; }
          .dr-stage-dot { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; background: #e5e7eb; color: #9ca3af; border: 3px solid white; box-shadow: 0 0 0 2px #e5e7eb; transition: all 0.3s; }
          .dr-stage-dot.done   { background: var(--primary, #00b207); color: white; box-shadow: 0 0 0 2px var(--primary, #00b207); }
          .dr-stage-dot.active { background: #3b82f6; color: white; box-shadow: 0 0 0 2px #3b82f6; }
          .dr-stage-label { font-size: 11px; font-weight: 600; color: #9ca3af; text-align: center; text-transform: uppercase; letter-spacing: 0.4px; line-height: 1.2; max-width: 72px; }
          .dr-stage-label.done   { color: var(--primary, #00b207); }
          .dr-stage-label.active { color: #3b82f6; }
          .dr-progress-fill { position: absolute; top: 20px; left: 0; height: 3px; background: var(--primary, #00b207); z-index: 0; transition: width 0.4s ease; }
        </style>

        <div class="breadcrumb">
            <a href="<?= url('distribution/requests') ?>"><?= $t['breadcrumb_req'] ?></a>
            <span> / <?= htmlspecialchars($request['request_number']) ?></span>
        </div>

        <div class="page-header">
            <div>
                <h1 class="page-title"><?= htmlspecialchars($request['request_name']) ?></h1>
                <p class="page-subtitle"><?= htmlspecialchars($request['request_number']) ?></p>
            </div>
            <div class="header-actions">
                <?php if ($request['status'] === 'draft'): ?>
                    <a href="<?= url('distribution/requests/edit?id=' . $request['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> <?= $t['btn_edit'] ?>
                    </a>
                    <form method="POST" action="<?= url('distribution/requests/submit') ?>" style="display: inline;">
                        <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> <?= $t['btn_submit'] ?>
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal()">
                        <i class="fas fa-trash"></i> <?= $t['btn_delete'] ?>
                    </button>
                <?php endif; ?>
                <?php if (in_array($request['status'], ['quoted', 'approved', 'pending_payment', 'awaiting_payment']) && !empty($request['payment_link_token'])): ?>
                    <a href="<?= url('distribution/pay?token=' . $request['payment_link_token']) ?>" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> <?= $t['btn_pay_now'] ?>
                    </a>
                <?php elseif ($request['status'] === 'quoted' && $invoice): ?>
                    <a href="<?= url('distribution/invoices/pay?id=' . $invoice['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> <?= $t['btn_pay_now'] ?>
                    </a>
                <?php endif; ?>
                <?php if (in_array($request['status'], ['submitted', 'quoted'])): ?>
                    <button type="button" class="btn btn-danger" onclick="showCancelModal()">
                        <i class="fas fa-times"></i> <?= $t['btn_cancel'] ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!in_array($drStatus, ['draft', 'cancelled', 'expired'])): ?>
        <div class="dr-progress-wrap">
            <div class="dr-progress-track" id="drProgressTrack">
                <?php foreach ($drStages as $i => $stage):
                    $stageNum = $i + 1;
                    $cls = $stageNum < $drActiveIdx ? 'done' : ($stageNum === $drActiveIdx ? 'active' : '');
                ?>
                <div class="dr-stage-step">
                    <div class="dr-stage-dot <?= $cls ?>">
                        <i class="fas fa-<?= $stage['icon'] ?>"></i>
                    </div>
                    <span class="dr-stage-label <?= $cls ?>"><?= $stage['label'] ?></span>
                </div>
                <?php endforeach; ?>
                <div class="dr-progress-fill" id="drProgressFill"></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Order Completion Timeline ── -->
        <?php
        $tlStatus    = $request['status'] ?? 'draft';
        $tlDeadline  = $request['order_deadline'] ?? null;
        $tlSubmitted = $request['submitted_at'] ?? null;
        $tlType      = $request['delivery_type'] ?? 'scheduled';
        $tlActive    = $tlDeadline && !in_array($tlStatus, ['draft','cancelled','delivered','completed']);

        if ($tlActive || ($tlDeadline && in_array($tlStatus, ['delivered','completed']))):
            $deadlineTs  = strtotime($tlDeadline);
            $submittedTs = $tlSubmitted ? strtotime($tlSubmitted) : null;
            $totalSecs   = ($submittedTs && $deadlineTs) ? max(1, $deadlineTs - $submittedTs) : 0;
            $nowTs       = time();
            $done        = in_array($tlStatus, ['delivered','completed']);

            $typeConfig = match($tlType) {
                'express'  => ['label' => '⚡ Express ASAP', 'color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca', 'promise' => 'Delivered within 2 hours'],
                'same_day' => ['label' => '☀️ Same Day',     'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a', 'promise' => 'Delivered today during business hours'],
                default    => ['label' => '📅 Scheduled',    'color' => '#4f46e5', 'bg' => '#eef2ff', 'border' => '#c7d2fe', 'promise' => 'Delivered on scheduled date'],
            };
            $c = $typeConfig;
        ?>
        <div style="background:<?= $c['bg'] ?>;border:1.5px solid <?= $c['border'] ?>;border-radius:12px;padding:20px 24px;margin-bottom:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-size:20px;"><?= $tlType === 'express' ? '⚡' : ($tlType === 'same_day' ? '☀️' : '📅') ?></span>
                    <div>
                        <div style="font-weight:700;font-size:15px;color:<?= $c['color'] ?>;"><?= $c['label'] ?> — Order Timeline</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:1px;"><?= $c['promise'] ?></div>
                    </div>
                </div>
                <?php if ($done): ?>
                    <span style="background:#d1fae5;color:#059669;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">✓ Completed</span>
                <?php else: ?>
                    <span id="tlBadge" style="background:<?= $c['color'] ?>;color:white;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">In Progress</span>
                <?php endif; ?>
            </div>

            <!-- Milestones row -->
            <div style="display:flex;align-items:center;gap:0;margin-bottom:14px;position:relative;">
                <!-- Submitted -->
                <div style="display:flex;flex-direction:column;align-items:center;flex:0 0 auto;">
                    <div style="width:32px;height:32px;border-radius:50%;background:<?= $c['color'] ?>;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-flag-checkered" style="color:white;font-size:13px;"></i>
                    </div>
                    <div style="font-size:10px;color:#6b7280;margin-top:4px;text-align:center;max-width:70px;">Submitted<br><?= $tlSubmitted ? date('g:i A', $submittedTs) : '—' ?></div>
                </div>

                <!-- Progress bar -->
                <div style="flex:1;height:6px;background:#e5e7eb;border-radius:3px;margin:0 4px;position:relative;overflow:hidden;" id="tlBarWrap">
                    <div id="tlBar" style="height:100%;border-radius:3px;background:<?= $c['color'] ?>;transition:width 1s linear;width:<?= $done ? '100' : min(100, round(max(0, $nowTs - ($submittedTs ?? $nowTs)) / $totalSecs * 100)) ?>%;"></div>
                </div>

                <!-- Deadline -->
                <div style="display:flex;flex-direction:column;align-items:center;flex:0 0 auto;">
                    <div style="width:32px;height:32px;border-radius:50%;background:<?= $done ? '#059669' : '#e5e7eb' ?>;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-<?= $done ? 'check' : 'map-marker-alt' ?>" style="color:<?= $done ? 'white' : '#9ca3af' ?>;font-size:13px;"></i>
                    </div>
                    <div style="font-size:10px;color:#6b7280;margin-top:4px;text-align:center;max-width:70px;">Deadline<br><?= date('g:i A', $deadlineTs) ?></div>
                </div>
            </div>

            <!-- Countdown / Elapsed -->
            <?php if (!$done): ?>
            <div style="display:flex;align-items:center;gap:8px;">
                <i class="fas fa-hourglass-half" style="color:<?= $c['color'] ?>;"></i>
                <span id="tlCountdown" style="font-size:14px;font-weight:700;color:<?= $c['color'] ?>;"></span>
                <span style="font-size:13px;color:#6b7280;">· Deadline: <?= date('M j, Y \a\t g:i A', $deadlineTs) ?></span>
            </div>
            <script>
            (function(){
                const deadline   = <?= $deadlineTs * 1000 ?>;
                const submitted  = <?= ($submittedTs ?? $nowTs) * 1000 ?>;
                const total      = <?= $totalSecs * 1000 ?>;
                const el         = document.getElementById('tlCountdown');
                const bar        = document.getElementById('tlBar');
                const badge      = document.getElementById('tlBadge');
                if (!el) return;

                function fmt(s) {
                    if (s < 0) return '00:00';
                    const h = Math.floor(s / 3600);
                    const m = Math.floor((s % 3600) / 60);
                    const sec = s % 60;
                    return h > 0
                        ? `${h}h ${String(m).padStart(2,'0')}m`
                        : `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
                }

                function tick() {
                    const now      = Date.now();
                    const secsLeft = Math.floor((deadline - now) / 1000);
                    const elapsed  = now - submitted;
                    const pct      = total > 0 ? Math.min(100, Math.round(elapsed / total * 100)) : 100;

                    if (bar) bar.style.width = pct + '%';

                    if (secsLeft <= 0) {
                        el.textContent = 'Deadline reached';
                        if (badge) { badge.textContent = 'Overdue'; badge.style.background = '#dc2626'; }
                        if (bar)   bar.style.background = '#dc2626';
                        return;
                    }

                    el.textContent = fmt(secsLeft) + ' remaining';

                    // Colour shift when under 20% time left
                    if (pct >= 80) {
                        const warn = '#dc2626';
                        el.style.color = warn;
                        if (bar)   bar.style.background = warn;
                        if (badge) badge.style.background = warn;
                    }

                    setTimeout(tick, 1000);
                }
                tick();
            })();
            </script>
            <?php else: ?>
            <div style="font-size:13px;color:#059669;font-weight:600;">
                <i class="fas fa-check-circle"></i> Order completed · Deadline was <?= date('M j, Y \a\t g:i A', $deadlineTs) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="content-grid">
            <div class="main-column">
                <!-- Status Card -->
                <div class="card">
                    <div class="card-header status-header">
                        <span class="badge badge-<?= $request['status'] ?>">
                            <?= ucwords(str_replace('_', ' ', $request['status'])) ?>
                        </span>
                        <?php if ($request['status'] === 'draft'): ?>
                            <span class="status-msg"><?= $t['status_draft_msg'] ?></span>
                        <?php elseif ($request['status'] === 'pending'): ?>
                            <span class="status-msg"><?= $t['status_pending_msg'] ?></span>
                        <?php elseif ($request['status'] === 'approved'): ?>
                            <span class="status-msg"><?= $t['status_approved_msg'] ?></span>
                        <?php elseif ($request['status'] === 'paid'): ?>
                            <span class="status-msg"><?= $t['status_paid_msg'] ?></span>
                        <?php elseif ($request['status'] === 'procurement'): ?>
                            <span class="status-msg"><?= $t['status_procurement_msg'] ?></span>
                        <?php elseif ($request['status'] === 'processing'): ?>
                            <span class="status-msg"><?= $t['status_processing_msg'] ?></span>
                        <?php elseif ($request['status'] === 'in_transit'): ?>
                            <span class="status-msg"><?= $t['status_in_transit_msg'] ?></span>
                        <?php elseif ($request['status'] === 'delivered'): ?>
                            <span class="status-msg"><?= $t['status_delivered_msg'] ?></span>
                        <?php elseif ($request['status'] === 'cancelled'): ?>
                            <span class="status-msg status-msg-cancelled"><?= $t['status_cancelled_msg'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label"><?= $t['label_delivery_address'] ?></div>
                                <div class="info-value">
                                    <?= htmlspecialchars($request['delivery_street']) ?><br>
                                    <?= htmlspecialchars($request['delivery_city']) ?>, <?= htmlspecialchars($request['delivery_province']) ?><br>
                                    <?= htmlspecialchars($request['delivery_postal_code']) ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><?= $t['label_preferred_date'] ?></div>
                                <div class="info-value">
                                    <?= $request['preferred_delivery_date'] ? date('F j, Y', strtotime($request['preferred_delivery_date'])) : $t['not_specified'] ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><?= $t['label_created'] ?></div>
                                <div class="info-value"><?= date('F j, Y g:i A', strtotime($request['created_at'])) ?></div>
                            </div>
                            <?php if ($request['submitted_at']): ?>
                                <div class="info-item">
                                    <div class="info-label"><?= $t['label_submitted'] ?></div>
                                    <div class="info-value"><?= date('F j, Y g:i A', strtotime($request['submitted_at'])) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($request['notes']): ?>
                            <div class="info-item notes-item">
                                <div class="info-label"><?= $t['label_notes'] ?></div>
                                <div class="info-value"><?= nl2br(htmlspecialchars($request['notes'])) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ── Supplier Switch Banner ── -->
                <?php if (!empty($request['supplier_switch_pending'])): ?>
                <?php
                    $switchDeadlineTs = $request['supplier_switch_deadline'] ? strtotime($request['supplier_switch_deadline']) : null;
                    $switchOld = (float)($request['supplier_switch_old_amount'] ?? 0);
                    $switchNew = (float)($request['supplier_switch_new_amount'] ?? 0);
                    $switchDiff = $switchNew - $switchOld;
                ?>
                <div id="supplierSwitchBanner" style="background:#fff7ed;border:2px solid #f59e0b;border-radius:12px;padding:24px;margin-bottom:24px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                        <div style="width:44px;height:44px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-sync-alt" style="color:#d97706;font-size:18px;"></i>
                        </div>
                        <div>
                            <h3 style="margin:0;color:#92400e;font-size:17px;">Action Required: Supplier Switch</h3>
                            <p style="margin:4px 0 0;color:#78350f;font-size:13px;">One of your suppliers was unavailable. We found a backup — please review and confirm.</p>
                        </div>
                    </div>

                    <?php if ($request['supplier_switch_notes']): ?>
                    <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:14px 16px;margin-bottom:16px;font-size:14px;color:#374151;line-height:1.6;">
                        <i class="fas fa-info-circle" style="color:#d97706;margin-right:6px;"></i>
                        <?= htmlspecialchars($request['supplier_switch_notes']) ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($switchOld > 0 && $switchNew > 0): ?>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                        <div style="background:white;border-radius:8px;padding:14px;text-align:center;border:1px solid #e5e7eb;">
                            <div style="font-size:12px;color:#9ca3af;text-transform:uppercase;font-weight:600;margin-bottom:4px;">Previous Total</div>
                            <div style="font-size:20px;font-weight:700;color:#9ca3af;text-decoration:line-through;">$<?= number_format($switchOld, 2) ?></div>
                        </div>
                        <div style="background:white;border-radius:8px;padding:14px;text-align:center;border:2px solid <?= $switchDiff > 0 ? '#fca5a5' : '#6ee7b7' ?>;">
                            <div style="font-size:12px;color:#9ca3af;text-transform:uppercase;font-weight:600;margin-bottom:4px;">Updated Total</div>
                            <div style="font-size:20px;font-weight:700;color:<?= $switchDiff > 0 ? '#dc2626' : '#059669' ?>;">$<?= number_format($switchNew, 2) ?></div>
                            <?php if ($switchDiff != 0): ?>
                            <div style="font-size:12px;color:<?= $switchDiff > 0 ? '#dc2626' : '#059669' ?>;margin-top:3px;"><?= $switchDiff > 0 ? '+' : '' ?>$<?= number_format($switchDiff, 2) ?> CAD</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($switchDeadlineTs): ?>
                    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-clock" style="color:#d97706;"></i>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#92400e;">Respond by: <?= date('F j, Y \a\t g:i A', $switchDeadlineTs) ?></div>
                            <div id="switchCountdown" style="font-size:12px;color:#d97706;font-weight:700;margin-top:2px;"></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <form method="POST" action="<?= url('distribution/requests/confirm-price-change') ?>" style="display:inline;">
                            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?>">
                            <button type="submit" onclick="return confirm('Confirm the updated pricing and proceed with this order?')"
                                style="padding:12px 24px;background:linear-gradient(135deg,#00b207,#008505);color:white;border:none;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;">
                                <i class="fas fa-check"></i> Accept &amp; Proceed
                            </button>
                        </form>
                        <form method="POST" action="<?= url('distribution/requests/decline-price-change') ?>" style="display:inline;">
                            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?>">
                            <button type="submit" onclick="return confirm('Cancel this order? This cannot be undone.')"
                                style="padding:12px 24px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border:none;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;">
                                <i class="fas fa-times"></i> Cancel Order
                            </button>
                        </form>
                    </div>
                </div>
                <?php if ($switchDeadlineTs): ?>
                <script>
                (function(){
                    const deadline = <?= $switchDeadlineTs * 1000 ?>;
                    const el = document.getElementById('switchCountdown');
                    if (!el) return;
                    function update() {
                        const diff = Math.floor((deadline - Date.now()) / 1000);
                        if (diff <= 0) { el.textContent = 'Window has expired — please contact support.'; el.style.color = '#dc2626'; return; }
                        const h = Math.floor(diff / 3600), m = Math.floor((diff % 3600) / 60), s = diff % 60;
                        el.textContent = `Time remaining: ${h}h ${m}m ${s}s`;
                        setTimeout(update, 1000);
                    }
                    update();
                })();
                </script>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Catalog Items grouped by supplier -->
                <?php if (!empty($catalogItems)):
                    // Group by supplier
                    $bySupplier = [];
                    foreach ($catalogItems as $item) {
                        $key = $item['supplier_name'] ?? 'Unknown Supplier';
                        $bySupplier[$key][] = $item;
                    }
                ?>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-box"></i> <?= $t['card_catalog_items'] ?></h3>
                        </div>
                        <?php foreach ($bySupplier as $supplierName => $items): ?>
                            <div style="margin-bottom:20px;">
                                <div style="display:flex;align-items:center;gap:8px;padding:8px 0 10px;border-bottom:2px solid #e5e7eb;margin-bottom:0;">
                                    <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:#f0fdf4;border-radius:50%;flex-shrink:0;">
                                        <i class="fas fa-store" style="color:#00b207;font-size:12px;"></i>
                                    </span>
                                    <span style="font-size:14px;font-weight:700;color:#111827;"><?= htmlspecialchars($supplierName) ?></span>
                                    <span style="font-size:12px;color:#6b7280;margin-left:2px;">(<?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?>)</span>
                                </div>
                                <table class="items-table" style="margin-top:0;">
                                    <thead>
                                        <tr>
                                            <th><?= $t['col_product'] ?></th>
                                            <th><?= $t['col_qty'] ?></th>
                                            <th><?= $t['col_unit_price'] ?></th>
                                            <th><?= $t['col_total'] ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="product-cell">
                                                        <img src="<?= $item['image'] ? asset(str_starts_with($item['image'], 'uploads/') ? $item['image'] : 'uploads/supplier-products/' . $item['image']) : asset('images/logo.png') ?>"
                                                             alt="<?= htmlspecialchars($item['product_name']) ?>" class="product-image"
                                                             onerror="this.src='<?= asset('images/logo.png') ?>'"
                                                             loading="lazy">
                                                        <div>
                                                            <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                                            <div class="product-sku">SKU: <?= htmlspecialchars($item['sku'] ?? '') ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td>$<?= number_format($item['unit_price'], 2) ?></td>
                                                <td><strong>$<?= number_format($item['quantity'] * $item['unit_price'], 2) ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Shopping List Items -->
                <?php if (!empty($shoppingItems)): ?>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list"></i> <?= $t['card_shopping_items'] ?></h3>
                        </div>
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th><?= $t['col_description'] ?></th>
                                    <th><?= $t['col_qty'] ?></th>
                                    <th><?= $t['col_unit'] ?></th>
                                    <th><?= $t['col_est_price'] ?></th>
                                    <th><?= $t['col_actual_price'] ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shoppingItems as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="product-name"><?= htmlspecialchars($item['item_description']) ?></div>
                                            <?php if ($item['notes']): ?>
                                                <div class="product-sku"><?= htmlspecialchars($item['notes']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td><?= htmlspecialchars(ucfirst($item['unit'])) ?></td>
                                        <td><?= $item['estimated_price'] ? '$' . number_format($item['estimated_price'], 2) : '--' ?></td>
                                        <td>
                                            <?php if ($item['actual_price']): ?>
                                                <strong>$<?= number_format($item['actual_price'], 2) ?></strong>
                                            <?php else: ?>
                                                <span class="text-placeholder"><?= $t['pending_label'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="sidebar-column">
                <!-- Order Summary -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-receipt"></i> <?= $t['card_summary'] ?></h3>
                    </div>
                    <div class="card-body">
                    <?php /* Always show full detailed breakdown regardless of invoice */ ?>
                        <?php if (isset($summary['tier'])): ?>
                            <span class="tier-badge tier-<?= $summary['tier'] ?>">
                                <i class="fas fa-layer-group"></i> Tier <?= $summary['tier'] ?> - <?= htmlspecialchars($summary['tier_vehicle']) ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($catalogTotal > 0): ?>
                            <div class="summary-item">
                                <span><?= $t['catalog_items_label'] ?> (<?= count($catalogItems) ?>)</span>
                                <span>$<?= number_format($catalogTotal, 2) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($shoppingItems)): ?>
                            <div class="summary-item">
                                <span><?= $t['shopping_items_label'] ?> (<?= count($shoppingItems) ?>)</span>
                                <span><?= $shoppingEstimate > 0 ? '$' . number_format($shoppingEstimate, 2) : 'TBD' ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($summary) && ($summary['items_total'] > 0 || $catalogTotal > 0)): ?>
                            <div class="fee-breakdown">
                                <div class="fee-row">
                                    <span><?= $t['items_total'] ?></span>
                                    <span>$<?= number_format($summary['items_total'] ?: $catalogTotal, 2) ?><?= !empty($shoppingItems) ? '+' : '' ?></span>
                                </div>
                                <div class="fee-row">
                                    <span><?= $t['service_fee'] ?> (<?= $summary['service_fee_percent'] ?>%)</span>
                                    <span>$<?= number_format($summary['service_fee'], 2) ?></span>
                                </div>
                                <div class="fee-row">
                                    <span><?= $t['handling'] ?> (<?= number_format($summary['total_weight_kg'], 1) ?> kg &times; $0.20/kg)</span>
                                    <span>$<?= number_format($summary['handling_fee'], 2) ?></span>
                                </div>
                                <div class="fee-row">
                                    <span><?= $t['delivery_fee'] ?> (<?= $summary['delivery_distance'] <= $summary['free_delivery_km'] ? $t['free_delivery'] . ' &le;' . $summary['free_delivery_km'] . 'km' : ($summary['delivery_distance'] - $summary['free_delivery_km']) . 'km &times; $' . number_format($summary['per_km_rate'], 2) ?>)</span>
                                    <span>$<?= number_format($summary['delivery_fee'], 2) ?></span>
                                </div>
                            </div>

                            <div class="summary-subtotal">
                                <span><?= $t['subtotal'] ?></span>
                                <span>$<?= number_format($summary['subtotal'], 2) ?><?= !empty($shoppingItems) ? '+' : '' ?></span>
                            </div>

                            <div class="tax-section">
                                <div class="tax-row">
                                    <span><?= $t['gst'] ?></span>
                                    <span>$<?= number_format($summary['gst_amount'], 2) ?></span>
                                </div>
                                <div class="tax-row">
                                    <span><?= $t['qst'] ?></span>
                                    <span>$<?= number_format($summary['qst_amount'], 2) ?></span>
                                </div>
                            </div>

                            <?php if (!empty($summary['tip_amount']) && $summary['tip_amount'] > 0): ?>
                            <div class="fee-breakdown" style="margin-top: 8px;">
                                <div class="fee-row">
                                    <span><?= $t['tip'] ?> <?= (int)$summary['tip_percentage'] > 0 ? '(' . (int)$summary['tip_percentage'] . '%)' : '(' . $t['tip_custom'] . ')' ?></span>
                                    <span>$<?= number_format($summary['tip_amount'], 2) ?></span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="summary-total">
                                <span><?= $t['est_total'] ?></span>
                                <span>$<?= number_format($summary['total_amount'], 2) ?><?= !empty($shoppingItems) ? '+' : '' ?></span>
                            </div>
                        <?php else: ?>
                            <div class="summary-total">
                                <span><?= $t['est_total'] ?></span>
                                <span>$<?= number_format($catalogTotal + $shoppingEstimate, 2) ?><?= !empty($shoppingItems) ? '+' : '' ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($shoppingItems)): ?>
                            <div class="summary-note">
                                <i class="fas fa-info-circle"></i>
                                <?= $t['shopping_note'] ?>
                            </div>
                        <?php endif; ?>
                    </div><!-- /card-body -->
                </div>

                <!-- Invoice Details -->
                <?php if ($invoice): ?>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-file-invoice"></i> <?= $t['card_invoice'] ?></h3>
                        </div>
                        <div class="card-body">
                        <div class="info-item">
                            <div class="info-label"><?= $t['label_invoice_number'] ?></div>
                            <div class="info-value"><?= htmlspecialchars($invoice['invoice_number']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><?= $t['label_status'] ?></div>
                            <div class="info-value">
                                <span class="badge badge-<?= $invoice['status'] ?>">
                                    <?= ucfirst($invoice['status']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><?= $t['label_due_date'] ?></div>
                            <div class="info-value"><?= date('F j, Y', strtotime($invoice['due_date'])) ?></div>
                        </div>
                        <?php if ($invoice['status'] === 'pending'): ?>
                            <a href="<?= url('distribution/invoices/pay?id=' . $invoice['id']) ?>" class="btn btn-primary btn-full" style="margin-top: 8px;">
                                <i class="fas fa-credit-card"></i> <?= $t['btn_pay_now'] ?>
                            </a>
                        <?php endif; ?>
                        </div><!-- /card-body -->
                    </div>
                <?php endif; ?>

                <!-- Documents -->
                <?php if (in_array($request['status'], ['paid', 'procurement', 'processing', 'ready', 'in_transit', 'delivered', 'completed'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-file-pdf"></i> <?= $t['card_documents'] ?></h3>
                        </div>
                        <div class="card-body doc-links">
                            <a href="<?= url('distribution/documents/invoice?id=' . $request['id']) ?>" class="btn btn-secondary btn-full" target="_blank">
                                <i class="fas fa-file-invoice"></i> <?= $t['download_invoice'] ?>
                            </a>
                            <a href="<?= url('distribution/documents/purchase-order?id=' . $request['id']) ?>" class="btn btn-secondary btn-full" target="_blank">
                                <i class="fas fa-file-alt"></i> <?= $t['download_po'] ?>
                            </a>
                            <a href="<?= url('distribution/documents/sales-order?id=' . $request['id']) ?>" class="btn btn-secondary btn-full" target="_blank">
                                <i class="fas fa-receipt"></i> <?= $currentLang === 'fr' ? 'Télécharger le bon de vente' : 'Download Sales Order' ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Status History -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-history"></i> <?= $t['card_activity'] ?></h3>
                    </div>
                    <?php if (empty($statusHistory)): ?>
                        <div class="card-body">
                            <p class="text-placeholder"><?= $t['no_activity'] ?></p>
                        </div>
                    <?php else: ?>
                        <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($statusHistory as $history): ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-status">
                                            <?= ucwords(str_replace('_', ' ', $history['new_status'] ?? '')) ?>
                                        </div>
                                        <?php if ($history['notes']): ?>
                                            <div class="timeline-note"><?= htmlspecialchars($history['notes']) ?></div>
                                        <?php endif; ?>
                                        <div class="timeline-date"><?= date('M j, Y g:i A', strtotime($history['created_at'])) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        </div><!-- /card-body -->
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <!-- Delete Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal">
            <h3><?= $t['modal_delete_title'] ?></h3>
            <p><?= $t['modal_delete_confirm'] ?></p>
            <form method="POST" action="<?= url('distribution/requests/delete') ?>">
                <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?>">
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="hideDeleteModal()"><?= $t['btn_keep_draft'] ?></button>
                    <button type="submit" class="btn btn-danger"><?= $t['btn_yes_delete'] ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div class="modal-overlay" id="cancelModal">
        <div class="modal">
            <h3><?= $t['modal_cancel_title'] ?></h3>
            <p><?= $t['modal_cancel_confirm'] ?></p>
            <form method="POST" action="<?= url('distribution/requests/cancel') ?>">
                <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?>">
                <div class="form-group modal-form-group">
                    <label class="form-label"><?= $t['reason_optional'] ?></label>
                    <textarea name="cancel_reason" class="form-input" rows="3" placeholder="<?= htmlspecialchars(html_entity_decode($t['reason_placeholder'], ENT_QUOTES, 'UTF-8')) ?>"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="hideCancelModal()"><?= $t['btn_keep_request'] ?></button>
                    <button type="submit" class="btn btn-danger"><?= $t['btn_yes_cancel'] ?></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showDeleteModal() {
            document.getElementById('deleteModal').classList.add('active');
        }
        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) hideDeleteModal();
        });

        function showCancelModal() {
            document.getElementById('cancelModal').classList.add('active');
        }
        function hideCancelModal() {
            document.getElementById('cancelModal').classList.remove('active');
        }
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) hideCancelModal();
        });
    </script>
    <script>
    (function() {
        const track = document.getElementById('drProgressTrack');
        const fill  = document.getElementById('drProgressFill');
        if (!track || !fill) return;
        const dots  = track.querySelectorAll('.dr-stage-dot');
        const total = dots.length;
        const activeIdx = <?= max(0, $drActiveIdx - 1) ?>;
        if (total <= 1) return;
        const pct = (activeIdx / (total - 1)) * 100;
        fill.style.width = pct + '%';
    })();
    </script>

    <?php if (!in_array($request['status'] ?? '', ['draft', 'cancelled', 'expired', 'delivered', 'completed'])): ?>
    <style>
    #drStatusToast {
        position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px);
        background: #1e293b; color: #fff; padding: 14px 24px; border-radius: 10px;
        font-size: 14px; font-weight: 600; box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        display: flex; align-items: center; gap: 10px; z-index: 9999;
        transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), opacity 0.3s;
        opacity: 0; pointer-events: none;
    }
    #drStatusToast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
    #drStatusToast .toast-icon { font-size: 18px; }
    </style>
    <div id="drStatusToast"><span class="toast-icon">🔄</span> <span id="drToastMsg">Status updated — reloading…</span></div>
    <script>
    (function() {
        var drId           = <?= (int)$request['id'] ?>;
        var curStatus      = <?= json_encode($request['status'] ?? '') ?>;
        var curDriverAccepted = <?= json_encode(!empty($driverAccepted)) ?>;
        var endpoint       = '<?= url('api/distribution/request/status') ?>?id=' + drId;
        var reloading      = false;

        function showToast(msg) {
            var t = document.getElementById('drStatusToast');
            document.getElementById('drToastMsg').textContent = msg;
            t.classList.add('show');
        }

        function poll() {
            if (reloading) return;
            fetch(endpoint)
                .then(function(r) { return r.ok ? r.json() : null; })
                .then(function(data) {
                    if (!data || !data.success) return;
                    var statusChanged = data.status !== curStatus;
                    var driverAcceptedChanged = data.driver_accepted !== curDriverAccepted;
                    if (statusChanged || driverAcceptedChanged) {
                        reloading = true;
                        var label = data.status.replace(/_/g, ' ').replace(/\b\w/g, function(c) { return c.toUpperCase(); });
                        showToast('Status updated to ' + label + ' — reloading…');
                        setTimeout(function() { location.reload(); }, 1800);
                    }
                })
                .catch(function() {});
        }

        // Poll every 4 seconds
        setInterval(poll, 4000);
    })();
    </script>
    <?php endif; ?>

<?php require __DIR__ . '/../layout-footer.php'; ?>
