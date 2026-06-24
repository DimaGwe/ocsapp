<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'page_title'    => 'My Shipments',
        'portal_sub'    => 'Distribution Portal',
        'nav_dashboard' => 'Dashboard',
        'nav_requests'  => 'Requests',
        'nav_shipments' => 'Shipments',
        'nav_routes'    => 'Routes',
        'nav_logout'    => 'Logout',
        'new_shipment'  => 'New Shipment',
        'stat_total'    => 'Total',
        'stat_drafts'   => 'Drafts',
        'stat_pending'  => 'Pending',
        'stat_progress' => 'In Progress',
        'stat_completed'=> 'Completed',
        'filter_all'    => 'All',
        'filter_draft'  => 'Draft',
        'filter_submitted' => 'Submitted',
        'filter_quoted' => 'Quoted',
        'filter_paid'   => 'Paid',
        'filter_in_transit' => 'In Transit',
        'filter_completed'  => 'Completed',
        'col_shipment'  => 'Shipment',
        'col_type'      => 'Type',
        'col_destinations' => 'Destinations',
        'col_status'    => 'Status',
        'col_amount'    => 'Amount',
        'col_date'      => 'Date',
        'type_parcel'   => 'Parcel',
        'type_product'  => 'Product',
        'type_multi'    => 'Multi-Drop',
        'stops_count'   => '%d stops',
        'one_dest'      => '1 destination',
        'multi_label'   => 'Multiple',
        'empty_title'   => 'No shipments found',
        'empty_desc'    => 'Start by creating your first shipment to send packages or products.',
        'create_shipment' => 'Create Shipment',
    ],
    'fr' => [
        'page_title'    => 'Mes envois',
        'portal_sub'    => 'Portail de distribution',
        'nav_dashboard' => 'Tableau de bord',
        'nav_requests'  => 'Demandes',
        'nav_shipments' => 'Envois',
        'nav_routes'    => 'Routes',
        'nav_logout'    => 'D&#233;connexion',
        'new_shipment'  => 'Nouvel envoi',
        'stat_total'    => 'Total',
        'stat_drafts'   => 'Brouillons',
        'stat_pending'  => 'En attente',
        'stat_progress' => 'En cours',
        'stat_completed'=> 'Compl&#233;t&#233;',
        'filter_all'    => 'Tous',
        'filter_draft'  => 'Brouillon',
        'filter_submitted' => 'Soumis',
        'filter_quoted' => 'Cotation re&#231;ue',
        'filter_paid'   => 'Pay&#233;',
        'filter_in_transit' => 'En transit',
        'filter_completed'  => 'Compl&#233;t&#233;',
        'col_shipment'  => 'Envoi',
        'col_type'      => 'Type',
        'col_destinations' => 'Destinations',
        'col_status'    => 'Statut',
        'col_amount'    => 'Montant',
        'col_date'      => 'Date',
        'type_parcel'   => 'Colis',
        'type_product'  => 'Produit',
        'type_multi'    => 'Multi-arr&#234;ts',
        'stops_count'   => '%d arr&#234;ts',
        'one_dest'      => '1 destination',
        'multi_label'   => 'Multiple',
        'empty_title'   => 'Aucun envoi trouv&#233;',
        'empty_desc'    => 'Commencez par cr&#233;er votre premier envoi pour exp&#233;dier des colis ou des produits.',
        'create_shipment' => 'Cr&#233;er un envoi',
    ],
];
$_paginationPage = $currentPage;
$currentPage = 'shipments';
$_pageTranslations = $translations; // save before layout-header.php overwrites $translations
$pageTitle = $translations[$currentLang]['page_title'] ?? $translations['en']['page_title'];
require __DIR__ . '/../layout-header.php';
$currentPage = $_paginationPage;
$t = $_pageTranslations[$currentLang] ?? $_pageTranslations['en'];
unset($_pageTranslations);
?>
<div class="page-header">
            <h1 class="page-title"><?= $t['page_title'] ?></h1>
            <a href="<?= url('distribution/shipments/create') ?>" class="btn-primary">
                <i class="fas fa-plus"></i> <?= $t['new_shipment'] ?>
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
                <div class="stat-label"><?= $t['stat_total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['drafts'] ?? 0 ?></div>
                <div class="stat-label"><?= $t['stat_drafts'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['pending'] ?? 0 ?></div>
                <div class="stat-label"><?= $t['stat_pending'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['in_progress'] ?? 0 ?></div>
                <div class="stat-label"><?= $t['stat_progress'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['completed'] ?? 0 ?></div>
                <div class="stat-label"><?= $t['stat_completed'] ?></div>
            </div>
        </div>

        <div class="filters">
            <a href="<?= url('distribution/shipments') ?>" class="filter-btn <?= empty($currentStatus) ? 'active' : '' ?>"><?= $t['filter_all'] ?></a>
            <a href="<?= url('distribution/shipments?status=draft') ?>" class="filter-btn <?= $currentStatus === 'draft' ? 'active' : '' ?>"><?= $t['filter_draft'] ?></a>
            <a href="<?= url('distribution/shipments?status=submitted') ?>" class="filter-btn <?= $currentStatus === 'submitted' ? 'active' : '' ?>"><?= $t['filter_submitted'] ?></a>
            <a href="<?= url('distribution/shipments?status=quoted') ?>" class="filter-btn <?= $currentStatus === 'quoted' ? 'active' : '' ?>"><?= $t['filter_quoted'] ?></a>
            <a href="<?= url('distribution/shipments?status=paid') ?>" class="filter-btn <?= $currentStatus === 'paid' ? 'active' : '' ?>"><?= $t['filter_paid'] ?></a>
            <a href="<?= url('distribution/shipments?status=in_transit') ?>" class="filter-btn <?= $currentStatus === 'in_transit' ? 'active' : '' ?>"><?= $t['filter_in_transit'] ?></a>
            <a href="<?= url('distribution/shipments?status=completed') ?>" class="filter-btn <?= $currentStatus === 'completed' ? 'active' : '' ?>"><?= $t['filter_completed'] ?></a>
        </div>

        <div class="section-card">
            <?php if (empty($shipments)): ?>
                <div class="empty-state">
                    <i class="fas fa-truck"></i>
                    <h3><?= $t['empty_title'] ?></h3>
                    <p><?= $t['empty_desc'] ?></p>
                    <a href="<?= url('distribution/shipments/create') ?>" class="btn-primary">
                        <i class="fas fa-plus"></i> <?= $t['create_shipment'] ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th><?= $t['col_shipment'] ?></th>
                                <th><?= $t['col_type'] ?></th>
                                <th><?= $t['col_destinations'] ?></th>
                                <th><?= $t['col_status'] ?></th>
                                <th><?= $t['col_amount'] ?></th>
                                <th><?= $t['col_date'] ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $typeLabels = [
                                'parcel'              => $t['type_parcel'],
                                'product_fulfillment' => $t['type_product'],
                                'multi_drop'          => $t['type_multi'],
                            ];
                            ?>
                            <?php foreach ($shipments as $shipment): ?>
                                <tr>
                                    <td>
                                        <a href="<?= url('distribution/shipments/show?id=' . $shipment['id']) ?>" class="shipment-number">
                                            <?= htmlspecialchars($shipment['shipment_number']) ?>
                                        </a>
                                        <div style="font-size: 12px; color: #666;">
                                            <?= htmlspecialchars($shipment['pickup_city']) ?> &rarr;
                                            <?php if ($shipment['is_multi_drop']): ?>
                                                <?= $t['multi_label'] ?>
                                            <?php else: ?>
                                                <?= htmlspecialchars($shipment['destination_city'] ?? 'N/A') ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= $typeLabels[$shipment['shipment_type']] ?? $t['type_parcel'] ?>
                                    </td>
                                    <td>
                                        <?php if ($shipment['is_multi_drop']): ?>
                                            <?= sprintf($t['stops_count'], $shipment['destinations_count'] ?? 0) ?>
                                        <?php else: ?>
                                            <?= $t['one_dest'] ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $shipment['status'] ?>">
                                            <?= $statusLabels[$shipment['status']] ?? ucwords(str_replace('_', ' ', $shipment['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($shipment['total_amount'] > 0): ?>
                                            $<?= number_format($shipment['total_amount'], 2) ?>
                                        <?php else: ?>
                                            <span style="color: #999;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: #666;">
                                        <?= date('M j, Y', strtotime($shipment['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= url('distribution/shipments?page=' . ($currentPage - 1) . ($currentStatus ? '&status=' . $currentStatus : '')) ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $currentPage): ?>
                                <span class="active"><?= $i ?></span>
                            <?php else: ?>
                                <a href="<?= url('distribution/shipments?page=' . $i . ($currentStatus ? '&status=' . $currentStatus : '')) ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= url('distribution/shipments?page=' . ($currentPage + 1) . ($currentStatus ? '&status=' . $currentStatus : '')) ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
<?php require __DIR__ . '/../layout-footer.php'; ?>
