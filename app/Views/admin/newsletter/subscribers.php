<?php
$pageTitle   = 'Newsletter Subscribers';
$currentPage = 'newsletter';
$lang = $_SESSION['language'] ?? 'en';
$fr = ($lang === 'fr');

$totalPages = max(1, (int) ceil($total / $perPage));
$qs = function (array $overrides = []) use ($listSlug, $status, $search) {
    $params = array_merge(['list' => $listSlug, 'status' => $status, 'search' => $search], $overrides);
    return http_build_query(array_filter($params, fn($v) => $v !== '' && $v !== null));
};

ob_start();
?>
<style>
  .page-header { margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
  .page-header h1 { font-size: 26px; font-weight: 700; color: var(--dark); margin-bottom: 4px; }
  .page-header p  { font-size: 14px; color: var(--gray-600); }
  .header-actions { display: flex; gap: 10px; }
  .btn-outline {
    display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px;
    border: 2px solid var(--border); border-radius: var(--radius-md); font-size: 13px;
    font-weight: 600; color: var(--gray-700); background: #fff; text-decoration: none; cursor: pointer;
  }
  .btn-outline:hover { background: var(--gray-50); }

  .filters { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
  .filters select, .filters input {
    padding: 9px 12px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 13.5px; font-family: inherit;
  }
  .filters input { min-width: 220px; }
  .filters button { padding: 9px 18px; border: none; border-radius: var(--radius-md); background: var(--primary); color: #fff; font-weight: 600; font-size: 13.5px; cursor: pointer; }

  .card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
  table { width: 100%; border-collapse: collapse; }
  th, td { padding: 11px 14px; text-align: left; font-size: 13.5px; border-bottom: 1px solid var(--border); }
  th { font-size: 11.5px; text-transform: uppercase; color: var(--gray-500); letter-spacing: .4px; }
  .badge { padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 600; }
  .badge-active { background: #dcfce7; color: #166534; }
  .badge-unsubscribed { background: #fee2e2; color: #991b1b; }
  .lists-cell { color: var(--gray-600); font-size: 12.5px; }
  .empty { text-align: center; color: var(--gray-500); padding: 30px; font-size: 14px; }
  .pager { display: flex; justify-content: space-between; align-items: center; padding: 14px; font-size: 13px; color: var(--gray-600); }
  .pager a { color: var(--primary); font-weight: 600; text-decoration: none; }
  .pager a.disabled { color: var(--gray-400); pointer-events: none; }
</style>

<div class="page-header">
  <div>
    <h1><?= $fr ? 'Abonnes aux infolettres' : 'Newsletter Subscribers' ?></h1>
    <p><?= number_format($total) ?> <?= $fr ? 'abonnes correspondant aux filtres' : 'subscribers matching filters' ?></p>
  </div>
  <div class="header-actions">
    <a href="<?= url('admin/newsletter') ?>" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> <?= $fr ? 'Retour' : 'Back' ?></a>
    <a href="<?= url('admin/newsletter/subscribers/export') ?>?<?= $qs() ?>" class="btn-outline"><i class="fa-solid fa-file-csv"></i> <?= $fr ? 'Exporter CSV' : 'Export CSV' ?></a>
  </div>
</div>

<form class="filters" method="GET" action="<?= url('admin/newsletter/subscribers') ?>">
  <select name="list">
    <option value=""><?= $fr ? 'Toutes les listes' : 'All lists' ?></option>
    <?php foreach ($allLists as $l): ?>
    <option value="<?= htmlspecialchars($l['slug']) ?>" <?= $listSlug === $l['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($l['name_en']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="status">
    <option value=""><?= $fr ? 'Tous les statuts' : 'All statuses' ?></option>
    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>><?= $fr ? 'Actif' : 'Active' ?></option>
    <option value="unsubscribed" <?= $status === 'unsubscribed' ? 'selected' : '' ?>><?= $fr ? 'Desabonne' : 'Unsubscribed' ?></option>
  </select>
  <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="<?= $fr ? 'Rechercher par courriel' : 'Search by email' ?>">
  <button type="submit"><?= $fr ? 'Filtrer' : 'Filter' ?></button>
</form>

<div class="card">
  <?php if (empty($subscribers)): ?>
    <div class="empty"><?= $fr ? 'Aucun abonne trouve.' : 'No subscribers found.' ?></div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th><?= $fr ? 'Courriel' : 'Email' ?></th>
        <th><?= $fr ? 'Statut' : 'Status' ?></th>
        <th><?= $fr ? 'Listes actives' : 'Active lists' ?></th>
        <th><?= $fr ? 'Abonne le' : 'Subscribed' ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($subscribers as $s): ?>
      <tr>
        <td><?= htmlspecialchars($s['email']) ?></td>
        <td><span class="badge badge-<?= htmlspecialchars($s['status']) ?>"><?= htmlspecialchars(ucfirst($s['status'])) ?></span></td>
        <td class="lists-cell"><?= htmlspecialchars($s['lists'] ?: '-') ?></td>
        <td><?= $s['subscribed_at'] ? htmlspecialchars(date('Y-m-d', strtotime($s['subscribed_at']))) : '-' ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="pager">
    <a class="<?= $page <= 1 ? 'disabled' : '' ?>" href="?<?= $qs(['page' => $page - 1]) ?>">&larr; <?= $fr ? 'Precedent' : 'Previous' ?></a>
    <span><?= $fr ? 'Page' : 'Page' ?> <?= $page ?> / <?= $totalPages ?></span>
    <a class="<?= $page >= $totalPages ? 'disabled' : '' ?>" href="?<?= $qs(['page' => $page + 1]) ?>"><?= $fr ? 'Suivant' : 'Next' ?> &rarr;</a>
  </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
