<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'page_title'        => 'My Procurement Requests',
        'portal_sub'        => 'Distribution Portal',
        'nav_dashboard'     => 'Dashboard',
        'nav_requests'      => 'Requests',
        'nav_invoices'      => 'Invoices',
        'nav_logout'        => 'Logout',
        'new_request'       => 'New Request',
        'filter_all'        => 'All',
        'filter_draft'      => 'Draft',
        'filter_submitted'  => 'Submitted',
        'filter_quoted'     => 'Quoted',
        'filter_pending_payment' => 'Pending Payment',
        'filter_processing' => 'Processing',
        'filter_completed'  => 'Completed',
        'col_request'       => 'Request',
        'col_items'         => 'Items',
        'col_status'        => 'Status',
        'col_invoice'       => 'Invoice',
        'col_created'       => 'Created',
        'col_action'        => 'Action',
        'empty_title'       => 'No requests found',
        'empty_desc'        => 'Create your first procurement request to get started.',
        'pay'               => 'Pay',
        'view'              => 'View',
    ],
    'fr' => [
        'page_title'        => 'Mes demandes d\'approvisionnement',
        'portal_sub'        => 'Portail de distribution',
        'nav_dashboard'     => 'Tableau de bord',
        'nav_requests'      => 'Demandes',
        'nav_invoices'      => 'Factures',
        'nav_logout'        => 'D&#233;connexion',
        'new_request'       => 'Nouvelle demande',
        'filter_all'        => 'Toutes',
        'filter_draft'      => 'Brouillon',
        'filter_submitted'  => 'Soumis',
        'filter_quoted'     => 'Cotation re&#231;ue',
        'filter_pending_payment' => 'Paiement en attente',
        'filter_processing' => 'En traitement',
        'filter_completed'  => 'Compl&#233;t&#233;',
        'col_request'       => 'Demande',
        'col_items'         => 'Articles',
        'col_status'        => 'Statut',
        'col_invoice'       => 'Facture',
        'col_created'       => 'Cr&#233;&#233;e le',
        'col_action'        => 'Action',
        'empty_title'       => 'Aucune demande trouv&#233;e',
        'empty_desc'        => 'Cr&#233;ez votre premi&#232;re demande d\'approvisionnement pour commencer.',
        'pay'               => 'Payer',
        'view'              => 'Voir',
    ],
];
$navPage    = 'requests';   // sidebar active-state (separate from pagination)
$currentPage = 'requests';  // layout-header reads this for nav
$pageTitle = $translations[$currentLang]['page_title'] ?? $translations['en']['page_title'];
$_pageT = $translations[$currentLang] ?? $translations['en'];
require __DIR__ . '/../layout-header.php';
$t = $_pageT;
unset($_pageT);
$paginationPage = (int)($_GET['page'] ?? 1); // numeric page for pagination links

$statusLabels = [
    'draft'           => $currentLang === 'fr' ? 'Brouillon' : 'Draft',
    'submitted'       => $currentLang === 'fr' ? 'Soumis' : 'Submitted',
    'quoted'          => $currentLang === 'fr' ? 'Cotation re&#231;ue' : 'Quoted',
    'pending_payment' => $currentLang === 'fr' ? 'Paiement en attente' : 'Pending Payment',
    'paid'            => $currentLang === 'fr' ? 'Pay&#233;' : 'Paid',
    'processing'      => $currentLang === 'fr' ? 'En traitement' : 'Processing',
    'ready'           => $currentLang === 'fr' ? 'Pr&#234;t' : 'Ready',
    'completed'       => $currentLang === 'fr' ? 'Compl&#233;t&#233;' : 'Completed',
    'cancelled'       => $currentLang === 'fr' ? 'Annul&#233;' : 'Cancelled',
];
?>
        <div class="page-header">
            <h1 class="page-title"><?= $t['page_title'] ?></h1>
            <a href="<?= url('distribution/requests/create') ?>" class="btn-primary">
                <i class="fas fa-plus"></i> <?= $t['new_request'] ?>
            </a>
        </div>

        <nav class="filters" aria-label="<?= $currentLang === 'fr' ? 'Filtrer les demandes' : 'Filter requests' ?>">
            <a href="<?= url('distribution/requests') ?>" class="filter-btn <?= empty($currentStatus) ? 'active' : '' ?>"><?= $t['filter_all'] ?></a>
            <a href="<?= url('distribution/requests?status=draft') ?>" class="filter-btn <?= $currentStatus === 'draft' ? 'active' : '' ?>"><?= $t['filter_draft'] ?></a>
            <a href="<?= url('distribution/requests?status=submitted') ?>" class="filter-btn <?= $currentStatus === 'submitted' ? 'active' : '' ?>"><?= $t['filter_submitted'] ?></a>
            <a href="<?= url('distribution/requests?status=quoted') ?>" class="filter-btn <?= $currentStatus === 'quoted' ? 'active' : '' ?>"><?= $t['filter_quoted'] ?></a>
            <a href="<?= url('distribution/requests?status=pending_payment') ?>" class="filter-btn <?= $currentStatus === 'pending_payment' ? 'active' : '' ?>"><?= $t['filter_pending_payment'] ?></a>
            <a href="<?= url('distribution/requests?status=processing') ?>" class="filter-btn <?= $currentStatus === 'processing' ? 'active' : '' ?>"><?= $t['filter_processing'] ?></a>
            <a href="<?= url('distribution/requests?status=completed') ?>" class="filter-btn <?= $currentStatus === 'completed' ? 'active' : '' ?>"><?= $t['filter_completed'] ?></a>
        </nav>

        <div class="card">
            <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3><?= $t['empty_title'] ?></h3>
                    <p><?= $t['empty_desc'] ?></p>
                    <a href="<?= url('distribution/requests/create') ?>" class="btn-primary">
                        <i class="fas fa-plus"></i> <?= $t['new_request'] ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <?php
                        $frMonths = [1=>'jan.','fév.','mars','avr.','mai','juin','juil.','août','sep.','oct.','nov.','déc.'];
                        ?>
                        <thead>
                            <tr>
                                <th scope="col"><?= $t['col_request'] ?></th>
                                <th scope="col"><?= $t['col_items'] ?></th>
                                <th scope="col"><?= $t['col_status'] ?></th>
                                <th scope="col"><?= $t['col_invoice'] ?></th>
                                <th scope="col"><?= $t['col_created'] ?></th>
                                <th scope="col"><?= $t['col_action'] ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                                <?php
                                $ts = strtotime($request['created_at']);
                                $dateStr = $currentLang === 'fr'
                                    ? date('j', $ts) . ' ' . $frMonths[(int)date('n', $ts)] . ' ' . date('Y', $ts)
                                    : date('M j, Y', $ts);
                                ?>
                                <tr>
                                    <td>
                                        <div class="request-number"><?= htmlspecialchars($request['request_number']) ?></div>
                                        <div class="request-name"><?= htmlspecialchars($request['request_name']) ?></div>
                                    </td>
                                    <td>
                                        <div class="item-count">
                                            <?php if ($request['catalog_items_count'] > 0): ?>
                                                <span>
                                                    <i class="fas fa-box" aria-hidden="true"></i>
                                                    <span class="sr-only"><?= $currentLang === 'fr' ? 'Articles catalogue:' : 'Catalog items:' ?></span>
                                                    <?= $request['catalog_items_count'] ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($request['shopping_items_count'] > 0): ?>
                                                <span>
                                                    <i class="fas fa-list" aria-hidden="true"></i>
                                                    <span class="sr-only"><?= $currentLang === 'fr' ? 'Articles liste:' : 'Shopping list items:' ?></span>
                                                    <?= $request['shopping_items_count'] ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $request['status'] ?>">
                                            <?= $statusLabels[$request['status']] ?? ucwords(str_replace('_', ' ', $request['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($request['invoice_number']): ?>
                                            <span class="invoice-number"><?= htmlspecialchars($request['invoice_number']) ?></span>
                                            <br>
                                            <span class="invoice-total"><?= $currentLang === 'fr' ? number_format($request['invoice_total'], 2, ',', ' ') . ' $' : '$' . number_format($request['invoice_total'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-placeholder">--</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="col-date"><?= $dateStr ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if (in_array($request['status'], ['quoted', 'approved', 'pending_payment', 'awaiting_payment']) && !empty($request['payment_link_token'])): ?>
                                                <a href="<?= url('distribution/pay?token=' . $request['payment_link_token']) ?>" class="btn-pay">
                                                    <i class="fas fa-credit-card" aria-hidden="true"></i> <?= $t['pay'] ?>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= url('distribution/requests/show?id=' . $request['id']) ?>" class="btn-view">
                                                <i class="fas fa-eye" aria-hidden="true"></i> <?= $t['view'] ?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (($totalPages ?? 1) > 1): ?>
                    <?php $statusParam = $currentStatus ? '&status=' . urlencode($currentStatus) : ''; ?>
                    <div class="pagination" role="navigation" aria-label="<?= $currentLang === 'fr' ? 'Pagination' : 'Pagination' ?>">
                        <?php if ($paginationPage > 1): ?>
                            <a href="<?= url('distribution/requests?page=' . ($paginationPage - 1) . $statusParam) ?>"
                               aria-label="<?= $currentLang === 'fr' ? 'Page précédente' : 'Previous page' ?>">
                                <i class="fas fa-chevron-left" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $paginationPage): ?>
                                <span class="active" aria-current="page"><?= $i ?></span>
                            <?php else: ?>
                                <a href="<?= url('distribution/requests?page=' . $i . $statusParam) ?>"
                                   aria-label="<?= ($currentLang === 'fr' ? 'Page ' : 'Page ') . $i ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($paginationPage < $totalPages): ?>
                            <a href="<?= url('distribution/requests?page=' . ($paginationPage + 1) . $statusParam) ?>"
                               aria-label="<?= $currentLang === 'fr' ? 'Page suivante' : 'Next page' ?>">
                                <i class="fas fa-chevron-right" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
<?php require __DIR__ . '/../layout-footer.php'; ?>
