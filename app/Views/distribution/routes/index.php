<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = ([
    'en' => [
        'page_title'      => 'Recurring Routes - OCSAPP Distribution',
        'portal_sub'      => 'Distribution Portal',
        'nav_dashboard'   => 'Dashboard',
        'nav_requests'    => 'Requests',
        'nav_shipments'   => 'Shipments',
        'nav_routes'      => 'Routes',
        'nav_logout'      => 'Logout',
        'title'           => 'Recurring Routes',
        'btn_new_route'   => 'New Route',
        'stat_total'      => 'Total Routes',
        'stat_active'     => 'Active',
        'stat_paused'     => 'Paused',
        'empty_title'     => 'No recurring routes yet',
        'empty_desc'      => 'Create a recurring route for regular shipments to the same destinations.',
        'btn_create'      => 'Create Route',
        'lbl_frequency'   => 'Frequency',
        'lbl_next_run'    => 'Next Run',
        'lbl_stop'        => 'stop',
        'lbl_stops'       => 'stops',
        'lbl_shipments'   => 'shipments',
        'btn_view'        => 'View',
        'btn_edit'        => 'Edit',
        'status_active'   => 'Active',
        'status_paused'   => 'Paused',
        'status_cancelled'=> 'Cancelled',
    ],
    'fr' => [
        'page_title'      => 'Routes récurrentes - OCSAPP Distribution',
        'portal_sub'      => 'Portail de Distribution',
        'nav_dashboard'   => 'Tableau de bord',
        'nav_requests'    => 'Demandes',
        'nav_shipments'   => 'Envois',
        'nav_routes'      => 'Routes',
        'nav_logout'      => 'Déconnexion',
        'title'           => 'Routes récurrentes',
        'btn_new_route'   => 'Nouvelle route',
        'stat_total'      => 'Total routes',
        'stat_active'     => 'Actives',
        'stat_paused'     => 'En pause',
        'empty_title'     => 'Aucune route récurrente',
        'empty_desc'      => 'Créez une route récurrente pour des envois réguliers vers les mêmes destinations.',
        'btn_create'      => 'Créer une route',
        'lbl_frequency'   => 'Fréquence',
        'lbl_next_run'    => 'Prochaine exécution',
        'lbl_stop'        => 'arrêt',
        'lbl_stops'       => 'arrêts',
        'lbl_shipments'   => 'envois',
        'btn_view'        => 'Voir',
        'btn_edit'        => 'Modifier',
        'status_active'   => 'Actif',
        'status_paused'   => 'En pause',
        'status_cancelled'=> 'Annulé',
    ],
])[$currentLang] ?? [];

$currentPage = 'routes';
$pageTitle = $t['page_title'];
$_pageT = $t; // preserve before layout-header.php overwrites $t
require __DIR__ . '/../layout-header.php';
$t = $_pageT; unset($_pageT); // restore page-specific translations
?>

        <div class="page-header">
            <h1 class="page-title"><?= $t['title'] ?></h1>
            <a href="<?= url('distribution/routes/create') ?>" class="btn-primary">
                <i class="fas fa-plus"></i> <?= $t['btn_new_route'] ?>
            </a>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
                <div class="stat-label"><?= $t['stat_total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['active'] ?? 0 ?></div>
                <div class="stat-label"><?= $t['stat_active'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['paused'] ?? 0 ?></div>
                <div class="stat-label"><?= $t['stat_paused'] ?></div>
            </div>
        </div>

        <div class="section-card">
            <?php if (empty($routes)): ?>
                <div class="empty-state">
                    <i class="fas fa-route"></i>
                    <h3><?= $t['empty_title'] ?></h3>
                    <p><?= $t['empty_desc'] ?></p>
                    <a href="<?= url('distribution/routes/create') ?>" class="btn-primary">
                        <i class="fas fa-plus"></i> <?= $t['btn_create'] ?>
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($routes as $route): ?>
                    <div class="route-card">
                        <div class="route-icon">
                            <i class="fas fa-route"></i>
                        </div>
                        <div class="route-info">
                            <div class="route-name">
                                <a href="<?= url('distribution/routes/show?id=' . $route['id']) ?>" style="color: inherit; text-decoration: none;">
                                    <?= htmlspecialchars($route['route_name']) ?>
                                </a>
                            </div>
                            <div class="route-meta">
                                <?= htmlspecialchars($route['pickup_city']) ?> &rarr;
                                <?php
                                $destinations = json_decode($route['destinations_template'], true);
                                $cnt = count($destinations);
                                echo $cnt . ' ' . ($cnt > 1 ? $t['lbl_stops'] : $t['lbl_stop']);
                                ?>
                                &bull; <?= $route['shipments_count'] ?> <?= $t['lbl_shipments'] ?>
                            </div>
                        </div>
                        <div class="route-schedule">
                            <div class="schedule-label"><?= $t['lbl_frequency'] ?></div>
                            <div class="schedule-value"><?= ucfirst($route['frequency']) ?></div>
                        </div>
                        <div class="route-schedule">
                            <div class="schedule-label"><?= $t['lbl_next_run'] ?></div>
                            <div class="schedule-value">
                                <?= $route['next_generation_date'] ? date('M j', strtotime($route['next_generation_date'])) : '-' ?>
                            </div>
                        </div>
                        <div class="route-status">
                            <span class="badge badge-<?= $route['status'] ?>">
                                <?= ucfirst($route['status']) ?>
                            </span>
                        </div>
                        <div class="route-actions">
                            <a href="<?= url('distribution/routes/show?id=' . $route['id']) ?>" class="btn-sm btn-outline">
                                <?= $t['btn_view'] ?>
                            </a>
                            <?php if ($route['status'] === 'active'): ?>
                                <a href="<?= url('distribution/routes/edit?id=' . $route['id']) ?>" class="btn-sm btn-outline">
                                    <?= $t['btn_edit'] ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
<?php require __DIR__ . '/../layout-footer.php'; ?>
