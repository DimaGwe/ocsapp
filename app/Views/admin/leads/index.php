<?php
$activePage  = 'leads';
$pageNum     = $currentPage ?? 1;
$currentPage = $activePage;
$currentLang = $_SESSION['language'] ?? 'fr';

$t = [
    'en' => [
        'page_title'        => 'Leads CRM',
        'add_lead'          => 'Add Lead',
        'total_active'      => 'Active Pipeline',
        'won'               => 'Won',
        'lost'              => 'Lost',
        'overdue'           => 'Overdue',
        'conversion'        => 'Conversion',
        'pipeline_stages'   => 'Pipeline Stages',
        'new'               => 'New',
        'contacted'         => 'Contacted',
        'qualified'         => 'Qualified',
        'proposal'          => 'Proposal',
        'negotiation'       => 'Negotiation',
        'all_status'        => 'All Status',
        'all_priority'      => 'All Priority',
        'all_sources'       => 'All Sources',
        'all_interests'     => 'All Interests',
        'all_assignees'     => 'All Assignees',
        'urgent'            => 'Urgent',
        'high'              => 'High',
        'medium'            => 'Medium',
        'low'               => 'Low',
        'manual'            => 'Manual',
        'website'           => 'Website',
        'referral'          => 'Referral',
        'social_media'      => 'Social Media',
        'event'             => 'Event',
        'cold_call'         => 'Cold Call',
        'seller'            => 'Seller',
        'buyer'             => 'Buyer',
        'business'          => 'Business',
        'supplier'          => 'Supplier',
        'driver'            => 'Driver',
        'delivery_partner'  => 'Delivery Partner',
        'investor'          => 'Investor',
        'search_ph'         => 'Search leads...',
        'filter'            => 'Filter',
        'clear'             => 'Clear',
        'lead'              => 'Lead',
        'contact'           => 'Contact',
        'interest'          => 'Interest',
        'source'            => 'Source',
        'status'            => 'Status',
        'priority'          => 'Priority',
        'assigned'          => 'Assigned',
        'follow_up'         => 'Follow-up',
        'actions'           => 'Actions',
        'no_leads'          => 'No leads found',
        'add_first'         => 'Add your first lead to get started',
        'showing'           => 'Showing',
        'of'                => 'of',
        'leads_label'       => 'leads',
        'certified'         => 'Certified',
        'stuck'             => 'Stuck',
        'not_started'       => 'Not Started',
        'pending_approval'  => 'Pending',
        'unassigned'        => 'Unassigned',
        'overdue_label'     => 'Overdue',
        'today_label'       => 'Today',
        'activities'        => 'activities',
    ],
    'fr' => [
        'page_title'        => 'CRM Prospects',
        'add_lead'          => 'Ajouter Prospect',
        'total_active'      => 'Pipeline Actif',
        'won'               => 'Gagné',
        'lost'              => 'Perdu',
        'overdue'           => 'En retard',
        'conversion'        => 'Conversion',
        'pipeline_stages'   => 'Étapes Pipeline',
        'new'               => 'Nouveau',
        'contacted'         => 'Contacté',
        'qualified'         => 'Qualifié',
        'proposal'          => 'Proposition',
        'negotiation'       => 'Négociation',
        'all_status'        => 'Tous les statuts',
        'all_priority'      => 'Toutes priorités',
        'all_sources'       => 'Toutes sources',
        'all_interests'     => 'Tous intérêts',
        'all_assignees'     => 'Tous assignés',
        'urgent'            => 'Urgent',
        'high'              => 'Haute',
        'medium'            => 'Moyenne',
        'low'               => 'Basse',
        'manual'            => 'Manuel',
        'website'           => 'Site web',
        'referral'          => 'Référence',
        'social_media'      => 'Réseaux sociaux',
        'event'             => 'Événement',
        'cold_call'         => 'Appel à froid',
        'seller'            => 'Vendeur',
        'buyer'             => 'Acheteur',
        'business'          => 'Entreprise',
        'supplier'          => 'Fournisseur',
        'driver'            => 'Chauffeur',
        'delivery_partner'  => 'Partenaire livraison',
        'investor'          => 'Investisseur',
        'search_ph'         => 'Rechercher prospects...',
        'filter'            => 'Filtrer',
        'clear'             => 'Effacer',
        'lead'              => 'Prospect',
        'contact'           => 'Contact',
        'interest'          => 'Intérêt',
        'source'            => 'Source',
        'status'            => 'Statut',
        'priority'          => 'Priorité',
        'assigned'          => 'Assigné',
        'follow_up'         => 'Suivi',
        'actions'           => 'Actions',
        'no_leads'          => 'Aucun prospect trouvé',
        'add_first'         => 'Ajoutez votre premier prospect pour commencer',
        'showing'           => 'Affichage',
        'of'                => 'sur',
        'leads_label'       => 'prospects',
        'certified'         => 'Certifié',
        'stuck'             => 'Bloqué',
        'not_started'       => 'Non commencé',
        'pending_approval'  => 'En attente',
        'unassigned'        => 'Non assigné',
        'overdue_label'     => 'En retard',
        'today_label'       => "Aujourd'hui",
        'activities'        => 'activités',
    ],
];
$t = $t[$currentLang] ?? $t['en'];
$pageTitle = $t['page_title'];

// Pre-calculate stats
$s             = $stats ?? [];
$total         = (int)($s['total'] ?? 0);
$newCount      = (int)($s['new_count'] ?? 0);
$contactedCnt  = (int)($s['contacted_count'] ?? 0);
$qualifiedCnt  = (int)($s['qualified_count'] ?? 0);
$proposalCnt   = (int)($s['proposal_count'] ?? 0);
$negoCnt       = (int)($s['negotiation_count'] ?? 0);
$wonCount      = (int)($s['won_count'] ?? 0);
$lostCount     = (int)($s['lost_count'] ?? 0);
$overdueCount  = (int)($s['overdue_count'] ?? 0);
$activeCount   = $newCount + $contactedCnt + $qualifiedCnt + $proposalCnt + $negoCnt;
$totalClosed   = $wonCount + $lostCount;
$convRate      = $totalClosed > 0 ? round(($wonCount / $totalClosed) * 100) : 0;

ob_start();
?>
<?php /* Page styles moved to public/assets/css/pages/admin-leads.css (auto-linked by layout.php) */ ?>

<!-- Page Header -->
<div class="leads-header">
    <h1><?= $t['page_title'] ?></h1>
    <a href="<?= url('admin/leads/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> <?= $t['add_lead'] ?>
    </a>
</div>

<!-- Flash Messages -->
<?php if ($flash = getFlash('success')): ?>
    <div class="flash-ok"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash) ?></div>
<?php endif; ?>
<?php if ($flash = getFlash('error')): ?>
    <div class="flash-err"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="summary-grid">
    <div class="summary-card sc-active">
        <div class="summary-icon"><i class="fas fa-stream"></i></div>
        <div>
            <div class="val"><?= $activeCount ?></div>
            <div class="lbl"><?= $t['total_active'] ?></div>
            <div class="sub"><?= $total ?> total leads</div>
        </div>
    </div>
    <div class="summary-card sc-won">
        <div class="summary-icon"><i class="fas fa-trophy"></i></div>
        <div>
            <div class="val"><?= $wonCount ?></div>
            <div class="lbl"><?= $t['won'] ?></div>
            <div class="sub"><?= $convRate ?>% <?= $t['conversion'] ?></div>
        </div>
    </div>
    <div class="summary-card sc-lost">
        <div class="summary-icon"><i class="fas fa-times-circle"></i></div>
        <div>
            <div class="val"><?= $lostCount ?></div>
            <div class="lbl"><?= $t['lost'] ?></div>
            <div class="sub"><?= $totalClosed ?> closed total</div>
        </div>
    </div>
    <div class="summary-card sc-overdue">
        <div class="summary-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <div class="val"><?= $overdueCount ?></div>
            <div class="lbl"><?= $t['overdue'] ?></div>
            <div class="sub">need follow-up</div>
        </div>
    </div>
</div>

<!-- Pipeline Strip -->
<div class="pipeline-card">
    <div class="pipeline-label"><?= $t['pipeline_stages'] ?></div>
    <div class="pipeline-stages">
        <a href="<?= url('admin/leads?status=new') ?>" class="ps-stage ps-new">
            <div class="ps-count"><?= $newCount ?></div>
            <div class="ps-name"><?= $t['new'] ?></div>
            <div class="ps-bar"></div>
        </a>
        <div class="ps-arrow"><i class="fas fa-chevron-right"></i></div>
        <a href="<?= url('admin/leads?status=contacted') ?>" class="ps-stage ps-contacted">
            <div class="ps-count"><?= $contactedCnt ?></div>
            <div class="ps-name"><?= $t['contacted'] ?></div>
            <div class="ps-bar"></div>
        </a>
        <div class="ps-arrow"><i class="fas fa-chevron-right"></i></div>
        <a href="<?= url('admin/leads?status=qualified') ?>" class="ps-stage ps-qualified">
            <div class="ps-count"><?= $qualifiedCnt ?></div>
            <div class="ps-name"><?= $t['qualified'] ?></div>
            <div class="ps-bar"></div>
        </a>
        <div class="ps-arrow"><i class="fas fa-chevron-right"></i></div>
        <a href="<?= url('admin/leads?status=proposal') ?>" class="ps-stage ps-proposal">
            <div class="ps-count"><?= $proposalCnt ?></div>
            <div class="ps-name"><?= $t['proposal'] ?></div>
            <div class="ps-bar"></div>
        </a>
        <div class="ps-arrow"><i class="fas fa-chevron-right"></i></div>
        <a href="<?= url('admin/leads?status=negotiation') ?>" class="ps-stage ps-negotiation">
            <div class="ps-count"><?= $negoCnt ?></div>
            <div class="ps-name"><?= $t['negotiation'] ?></div>
            <div class="ps-bar"></div>
        </a>
        <div class="ps-arrow"><i class="fas fa-chevron-right"></i></div>
        <a href="<?= url('admin/leads?status=won') ?>" class="ps-stage ps-won">
            <div class="ps-count"><?= $wonCount ?></div>
            <div class="ps-name"><?= $t['won'] ?></div>
            <div class="ps-bar"></div>
        </a>
    </div>
</div>

<!-- Filters Bar -->
<form method="GET" action="<?= url('admin/leads') ?>" class="filters-bar">
    <select name="status" class="filter-select">
        <option value=""><?= $t['all_status'] ?></option>
        <?php foreach (['new','contacted','qualified','proposal','negotiation','won','lost'] as $s): ?>
        <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>>
            <?= ucfirst($s) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <select name="priority" class="filter-select">
        <option value=""><?= $t['all_priority'] ?></option>
        <option value="urgent"  <?= ($filters['priority'] ?? '') === 'urgent'  ? 'selected' : '' ?>><?= $t['urgent'] ?></option>
        <option value="high"    <?= ($filters['priority'] ?? '') === 'high'    ? 'selected' : '' ?>><?= $t['high'] ?></option>
        <option value="medium"  <?= ($filters['priority'] ?? '') === 'medium'  ? 'selected' : '' ?>><?= $t['medium'] ?></option>
        <option value="low"     <?= ($filters['priority'] ?? '') === 'low'     ? 'selected' : '' ?>><?= $t['low'] ?></option>
    </select>

    <select name="source" class="filter-select">
        <option value=""><?= $t['all_sources'] ?></option>
        <option value="manual"      <?= ($filters['source'] ?? '') === 'manual'      ? 'selected' : '' ?>><?= $t['manual'] ?></option>
        <option value="website"     <?= ($filters['source'] ?? '') === 'website'     ? 'selected' : '' ?>><?= $t['website'] ?></option>
        <option value="referral"    <?= ($filters['source'] ?? '') === 'referral'    ? 'selected' : '' ?>><?= $t['referral'] ?></option>
        <option value="social_media"<?= ($filters['source'] ?? '') === 'social_media'? 'selected' : '' ?>><?= $t['social_media'] ?></option>
        <option value="event"       <?= ($filters['source'] ?? '') === 'event'       ? 'selected' : '' ?>><?= $t['event'] ?></option>
        <option value="cold_call"   <?= ($filters['source'] ?? '') === 'cold_call'   ? 'selected' : '' ?>><?= $t['cold_call'] ?></option>
    </select>

    <select name="interest" class="filter-select">
        <option value=""><?= $t['all_interests'] ?></option>
        <option value="seller"          <?= ($filters['interest'] ?? '') === 'seller'          ? 'selected' : '' ?>><?= $t['seller'] ?></option>
        <option value="buyer"           <?= ($filters['interest'] ?? '') === 'buyer'           ? 'selected' : '' ?>><?= $t['buyer'] ?></option>
        <option value="business"        <?= ($filters['interest'] ?? '') === 'business'        ? 'selected' : '' ?>><?= $t['business'] ?></option>
        <option value="supplier"        <?= ($filters['interest'] ?? '') === 'supplier'        ? 'selected' : '' ?>><?= $t['supplier'] ?></option>
        <option value="driver"          <?= ($filters['interest'] ?? '') === 'driver'          ? 'selected' : '' ?>><?= $t['driver'] ?></option>
        <option value="delivery_partner"<?= ($filters['interest'] ?? '') === 'delivery_partner'? 'selected' : '' ?>><?= $t['delivery_partner'] ?></option>
        <option value="investor"        <?= ($filters['interest'] ?? '') === 'investor'        ? 'selected' : '' ?>><?= $t['investor'] ?></option>
    </select>

    <?php if (!empty($admins)): ?>
    <select name="assigned_to" class="filter-select">
        <option value=""><?= $t['all_assignees'] ?></option>
        <?php foreach ($admins as $admin): ?>
        <option value="<?= $admin['id'] ?>" <?= (string)($filters['assigned_to'] ?? '') === (string)$admin['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($admin['name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>

    <input type="text" name="search" class="search-input"
           placeholder="<?= $t['search_ph'] ?>"
           value="<?= htmlspecialchars($filters['search'] ?? '') ?>">

    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fas fa-search"></i> <?= $t['filter'] ?>
    </button>
    <a href="<?= url('admin/leads') ?>" class="btn btn-ghost btn-sm"><?= $t['clear'] ?></a>
</form>

<!-- Table -->
<?php if (empty($leads)): ?>
<div class="table-wrap">
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <h3><?= $t['no_leads'] ?></h3>
        <p><?= $t['add_first'] ?></p>
        <a href="<?= url('admin/leads/create') ?>" class="btn btn-primary" style="margin-top:14px;">
            <i class="fas fa-plus"></i> <?= $t['add_lead'] ?>
        </a>
    </div>
</div>
<?php else: ?>
<?php
$perPage  = 20;
$startNum = (($pageNum - 1) * $perPage) + 1;
$endNum   = min($pageNum * $perPage, $total);
?>
<div class="table-wrap">
    <div class="results-bar">
        <span><?= $t['showing'] ?> <strong><?= $startNum ?>-<?= $endNum ?></strong> <?= $t['of'] ?> <strong><?= $total ?></strong> <?= $t['leads_label'] ?></span>
    </div>
    <div style="overflow-x:auto;">
    <table class="leads-tbl">
        <thead>
            <tr>
                <th><?= $t['lead'] ?></th>
                <th class="hide-mobile"><?= $t['contact'] ?></th>
                <th><?= $t['interest'] ?></th>
                <th class="hide-tablet"><?= $t['source'] ?></th>
                <th><?= $t['status'] ?></th>
                <th class="hide-mobile"><?= $t['priority'] ?></th>
                <th class="hide-tablet"><?= $t['assigned'] ?></th>
                <th class="hide-tablet"><?= $t['follow_up'] ?></th>
                <th><?= $t['actions'] ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($leads as $lead): ?>
        <?php
            $isDriver    = in_array($lead['interest_type'], ['driver', 'delivery_partner']);
            $driverId    = $lead['driver_user_id'] ?? null;
            $certified   = !empty($lead['training_certified']);
            $passed      = (int)($lead['training_modules_passed'] ?? 0);
            $stuck       = (int)($lead['training_stuck'] ?? 0);
            $actCount    = (int)($lead['activity_count'] ?? 0);

            $followUp = null; $fuClass = 'fu-none';
            if ($lead['next_follow_up']) {
                $fuTs = strtotime($lead['next_follow_up']);
                $today = strtotime('today');
                $followUp = date('M j', $fuTs);
                if      ($fuTs < $today)  $fuClass = 'fu-overdue';
                elseif  ($fuTs === $today) $fuClass = 'fu-today';
                else                       $fuClass = 'fu-upcoming';
            }

            $intClass = 'int-' . strtolower(str_replace(' ', '_', $lead['interest_type']));
            $intLabel = ucwords(str_replace('_', ' ', $lead['interest_type']));
        ?>
        <tr>
            <!-- Lead name + company -->
            <td>
                <div class="lead-name">
                    <a href="<?= url('admin/leads/view?id=' . $lead['id']) ?>">
                        <?= htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) ?>
                    </a>
                    <?php if ($actCount > 0): ?>
                    <span class="activity-dot" title="<?= $actCount ?> <?= $t['activities'] ?>">
                        <i class="fas fa-comment-dots"></i><?= $actCount ?>
                    </span>
                    <?php endif; ?>
                </div>
                <?php if ($lead['company_name']): ?>
                <div class="lead-company"><?= htmlspecialchars($lead['company_name']) ?></div>
                <?php endif; ?>
            </td>

            <!-- Contact -->
            <td class="hide-mobile">
                <div class="contact-line">
                    <?php if ($lead['email']): ?>
                    <div><i class="fas fa-envelope"></i> <?= htmlspecialchars($lead['email']) ?></div>
                    <?php endif; ?>
                    <?php if ($lead['phone']): ?>
                    <div><i class="fas fa-phone"></i> <?= htmlspecialchars($lead['phone']) ?></div>
                    <?php endif; ?>
                </div>
            </td>

            <!-- Interest + training for drivers -->
            <td>
                <span class="int-badge <?= $intClass ?>"><?= $intLabel ?></span>
                <?php if ($isDriver): ?>
                <div>
                <?php if ($driverId && $certified): ?>
                    <span class="train-badge train-certified"><i class="fas fa-graduation-cap"></i> <?= $t['certified'] ?></span>
                <?php elseif ($driverId && $stuck > 0): ?>
                    <span class="train-badge train-stuck"><i class="fas fa-exclamation-triangle"></i> <?= $t['stuck'] ?></span>
                <?php elseif ($driverId && $passed > 0): ?>
                    <span class="train-badge train-progress"><?= $passed ?>/7 modules</span>
                <?php elseif ($driverId): ?>
                    <span class="train-badge train-not-started"><?= $t['not_started'] ?></span>
                <?php else: ?>
                    <span class="train-pending"><?= $t['pending_approval'] ?></span>
                <?php endif; ?>
                </div>
                <?php endif; ?>
            </td>

            <!-- Source -->
            <td class="hide-tablet">
                <span class="src-chip"><?= ucwords(str_replace('_', ' ', $lead['source'])) ?></span>
            </td>

            <!-- Status (inline change) -->
            <td>
                <select class="status-select s-<?= $lead['status'] ?>"
                        onchange="updateLeadStatus(this, <?= (int)$lead['id'] ?>)"
                        title="Change status">
                    <?php foreach (['new','contacted','qualified','proposal','negotiation','won','lost'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $lead['status'] === $opt ? 'selected' : '' ?>>
                        <?= ucfirst($opt) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>

            <!-- Priority -->
            <td class="hide-mobile">
                <span class="pri pri-<?= $lead['priority'] ?>"><?= ucfirst($lead['priority']) ?></span>
            </td>

            <!-- Assigned to -->
            <td class="hide-tablet">
                <?php if ($lead['assigned_to_name'] && trim($lead['assigned_to_name']) !== ' '): ?>
                    <span style="font-size:12px;color:#374151;"><?= htmlspecialchars($lead['assigned_to_name']) ?></span>
                <?php else: ?>
                    <span style="font-size:12px;color:#d1d5db;"><?= $t['unassigned'] ?></span>
                <?php endif; ?>
            </td>

            <!-- Follow-up -->
            <td class="hide-tablet">
                <?php if ($followUp): ?>
                    <span class="<?= $fuClass ?>">
                        <?php if ($fuClass === 'fu-overdue'): ?><i class="fas fa-exclamation-circle" style="font-size:11px;"></i> <?php endif; ?>
                        <?= $followUp ?>
                    </span>
                <?php else: ?>
                    <span class="fu-none">-</span>
                <?php endif; ?>
            </td>

            <!-- Actions -->
            <td>
                <div style="display:flex;gap:4px;">
                    <a href="<?= url('admin/leads/view?id=' . $lead['id']) ?>" class="action-btn action-view" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="<?= url('admin/leads/edit?id=' . $lead['id']) ?>" class="action-btn action-edit" title="Edit">
                        <i class="fas fa-pen"></i>
                    </a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php if ($pageNum > 1): ?>
        <a href="<?= url('admin/leads?' . http_build_query(array_merge($filters, ['page' => $pageNum - 1]))) ?>">
            <i class="fas fa-chevron-left"></i>
        </a>
    <?php endif; ?>
    <?php for ($i = max(1, $pageNum - 2); $i <= min($totalPages, $pageNum + 2); $i++): ?>
        <?php if ($i === $pageNum): ?>
            <span class="pg-active"><?= $i ?></span>
        <?php else: ?>
            <a href="<?= url('admin/leads?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
    <?php if ($pageNum < $totalPages): ?>
        <a href="<?= url('admin/leads?' . http_build_query(array_merge($filters, ['page' => $pageNum + 1]))) ?>">
            <i class="fas fa-chevron-right"></i>
        </a>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<script>
function updateLeadStatus(sel, leadId) {
    const newStatus = sel.value;
    sel.disabled = true;

    const fd = new FormData();
    fd.append('lead_id', leadId);
    fd.append('status', newStatus);

    fetch('<?= url('admin/leads/update-status') ?>', { method: 'POST', body: fd })
        .then(function(r) {
            sel.disabled = false;
            if (r.ok || r.redirected) {
                sel.className = 'status-select s-' + newStatus;
            }
        })
        .catch(function() {
            sel.disabled = false;
        });
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
