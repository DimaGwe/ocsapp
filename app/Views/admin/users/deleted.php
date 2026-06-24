<?php
$currentPage    = 'deleted-users';
$activeSection  = 'users_accounts';
$currentLang    = $_SESSION['language'] ?? 'fr';
ob_start();
?>

<style>
.du-stats { display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.du-stat-card {
  background: #fff; border-radius: 10px; padding: 18px 24px;
  border: 1px solid #e5e7eb; flex: 1; min-width: 140px;
}
.du-stat-card .num { font-size: 28px; font-weight: 700; color: #1f2937; }
.du-stat-card .lbl { font-size: 12px; color: #6b7280; margin-top: 2px; }
.du-stat-card.red .num  { color: #dc2626; }
.du-stat-card.green .num { color: #16a34a; }

.du-filters { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; align-items: center; }
.du-filters select, .du-filters input[type=text] {
  padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px;
  font-size: 13px; color: #1f2937; background: #fff;
}
.du-filters button {
  padding: 8px 16px; border-radius: 8px; border: none;
  background: #1f2937; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;
}

table.du-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; border: 1px solid #e5e7eb; }
table.du-table thead { background: #f9fafb; }
table.du-table th { padding: 11px 14px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; text-align: left; border-bottom: 1px solid #e5e7eb; white-space: nowrap; }
table.du-table td { padding: 12px 14px; font-size: 13px; color: #374151; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
table.du-table tr:last-child td { border-bottom: none; }
table.du-table tr:hover td { background: #fafafa; }

.badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge.banned   { background: #fee2e2; color: #dc2626; }
.badge.allowed  { background: #dcfce7; color: #16a34a; }
.badge.reason   { background: #f3f4f6; color: #374151; }
.badge.role     { background: #eff6ff; color: #1d4ed8; }

.btn-ban {
  padding: 5px 12px; border-radius: 6px; border: 1px solid; font-size: 12px;
  font-weight: 600; cursor: pointer; transition: all .15s;
}
.btn-ban.ban    { border-color: #dc2626; background: #fef2f2; color: #dc2626; }
.btn-ban.unban  { border-color: #16a34a; background: #f0fdf4; color: #16a34a; }

.notes-cell { max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #6b7280; font-style: italic; }

@media (max-width: 900px) {
  table.du-table th.hide-sm, table.du-table td.hide-sm { display: none; }
}
</style>

<div class="admin-content-header" style="margin-bottom:24px;">
  <div>
    <h1 class="admin-page-title">
      <i class="fas fa-user-slash" style="color:#dc2626;margin-right:10px;"></i>
      <?= $currentLang === 'fr' ? 'Utilisateurs supprimés' : 'Deleted Users' ?>
    </h1>
    <p style="color:#6b7280;font-size:14px;margin-top:4px;">
      <?= $currentLang === 'fr'
          ? 'Archive CRM de tous les comptes supprimés. Gérez les interdictions de réinscription.'
          : 'CRM archive of all deleted accounts. Manage re-registration bans.' ?>
    </p>
  </div>
</div>

<!-- Stats -->
<div class="du-stats">
  <div class="du-stat-card">
    <div class="num"><?= (int)($stats['total'] ?? 0) ?></div>
    <div class="lbl"><?= $currentLang === 'fr' ? 'Total supprimés' : 'Total deleted' ?></div>
  </div>
  <div class="du-stat-card red">
    <div class="num"><?= (int)($stats['banned'] ?? 0) ?></div>
    <div class="lbl"><?= $currentLang === 'fr' ? 'Bannis' : 'Banned' ?></div>
  </div>
  <div class="du-stat-card green">
    <div class="num"><?= (int)($stats['can_rejoin'] ?? 0) ?></div>
    <div class="lbl"><?= $currentLang === 'fr' ? 'Peuvent réinscrire' : 'Can re-register' ?></div>
  </div>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('admin/deleted-users') ?>">
  <div class="du-filters">
    <input type="text" name="search" placeholder="<?= $currentLang === 'fr' ? 'Nom ou courriel...' : 'Name or email...' ?>" value="<?= htmlspecialchars($search ?? '') ?>" style="min-width:200px;">

    <select name="reason">
      <option value=""><?= $currentLang === 'fr' ? 'Toutes les raisons' : 'All reasons' ?></option>
      <option value="voluntary"       <?= ($reasonFilter??'') === 'voluntary'        ? 'selected' : '' ?>>Voluntary / Départ volontaire</option>
      <option value="inactive"        <?= ($reasonFilter??'') === 'inactive'         ? 'selected' : '' ?>>Inactive / Inactivité</option>
      <option value="terms_violation" <?= ($reasonFilter??'') === 'terms_violation'  ? 'selected' : '' ?>>Terms violation / Conditions</option>
      <option value="business_conduct"<?= ($reasonFilter??'') === 'business_conduct' ? 'selected' : '' ?>>Business conduct / Conduite</option>
      <option value="test"            <?= ($reasonFilter??'') === 'test'             ? 'selected' : '' ?>>Test account / Compte test</option>
      <option value="other"           <?= ($reasonFilter??'') === 'other'            ? 'selected' : '' ?>>Other / Autre</option>
    </select>

    <select name="role">
      <option value=""><?= $currentLang === 'fr' ? 'Tous les rôles' : 'All roles' ?></option>
      <?php foreach (['buyer','seller','driver','supplier','business','delivery','admin'] as $r): ?>
      <option value="<?= $r ?>" <?= ($roleFilter??'') === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
      <?php endforeach; ?>
    </select>

    <select name="banned">
      <option value=""><?= $currentLang === 'fr' ? 'Statut réinscription' : 'Re-reg status' ?></option>
      <option value="1" <?= ($banFilter??'') === '1' ? 'selected' : '' ?>><?= $currentLang === 'fr' ? 'Bannis seulement' : 'Banned only' ?></option>
      <option value="0" <?= ($banFilter??'') === '0' ? 'selected' : '' ?>><?= $currentLang === 'fr' ? 'Peuvent réinscrire' : 'Can re-register' ?></option>
    </select>

    <button type="submit"><i class="fas fa-search"></i> <?= $currentLang === 'fr' ? 'Filtrer' : 'Filter' ?></button>
    <?php if ($search || $reasonFilter || $roleFilter || $banFilter): ?>
      <a href="<?= url('admin/deleted-users') ?>" style="padding:8px 14px;border-radius:8px;border:1px solid #d1d5db;background:#fff;color:#6b7280;font-size:13px;text-decoration:none;">
        × <?= $currentLang === 'fr' ? 'Effacer' : 'Clear' ?>
      </a>
    <?php endif; ?>
  </div>
</form>

<?php if (empty($deletedUsers)): ?>
  <div style="text-align:center;padding:64px 24px;background:#fff;border-radius:10px;border:1px solid #e5e7eb;">
    <i class="fas fa-user-slash" style="font-size:48px;color:#d1d5db;margin-bottom:16px;display:block;"></i>
    <p style="color:#6b7280;font-size:15px;"><?= $currentLang === 'fr' ? 'Aucun utilisateur supprimé trouvé.' : 'No deleted users found.' ?></p>
  </div>
<?php else: ?>
<div style="overflow-x:auto;">
<table class="du-table">
  <thead>
    <tr>
      <th><?= $currentLang === 'fr' ? 'Utilisateur' : 'User' ?></th>
      <th>Role</th>
      <th><?= $currentLang === 'fr' ? 'Raison' : 'Reason' ?></th>
      <th class="hide-sm"><?= $currentLang === 'fr' ? 'Notes' : 'Notes' ?></th>
      <th class="hide-sm"><?= $currentLang === 'fr' ? 'Supprimé par' : 'Deleted by' ?></th>
      <th><?= $currentLang === 'fr' ? 'Date' : 'Date' ?></th>
      <th><?= $currentLang === 'fr' ? 'Réinscription' : 'Re-register' ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($deletedUsers as $du): ?>
    <tr>
      <td>
        <div style="font-weight:600;color:#1f2937;"><?= htmlspecialchars(trim($du['first_name'] . ' ' . $du['last_name'])) ?: '<em style="color:#9ca3af">—</em>' ?></div>
        <div style="font-size:12px;color:#6b7280;"><?= htmlspecialchars($du['email']) ?></div>
      </td>
      <td><span class="badge role"><?= htmlspecialchars($du['role'] ?? '—') ?></span></td>
      <td>
        <?php
        $reasonLabels = [
          'voluntary'        => 'Voluntary',
          'inactive'         => 'Inactive',
          'terms_violation'  => 'Terms violation',
          'business_conduct' => 'Business conduct',
          'test'             => 'Test account',
          'other'            => 'Other',
        ];
        ?>
        <span class="badge reason"><?= $reasonLabels[$du['reason']] ?? htmlspecialchars($du['reason']) ?></span>
      </td>
      <td class="hide-sm">
        <div class="notes-cell" title="<?= htmlspecialchars($du['notes'] ?? '') ?>">
          <?= $du['notes'] ? htmlspecialchars($du['notes']) : '<span style="color:#d1d5db;">—</span>' ?>
        </div>
      </td>
      <td class="hide-sm" style="font-size:12px;color:#6b7280;">
        <?= htmlspecialchars(trim($du['deleted_by_name'])) ?: '—' ?>
      </td>
      <td style="font-size:12px;white-space:nowrap;">
        <?= date('M j, Y', strtotime($du['deleted_at'])) ?><br>
        <span style="color:#9ca3af;"><?= date('g:ia', strtotime($du['deleted_at'])) ?></span>
      </td>
      <td>
        <?php if ($du['can_rejoin']): ?>
          <span class="badge allowed"><i class="fas fa-check"></i> <?= $currentLang === 'fr' ? 'Autorisé' : 'Allowed' ?></span>
        <?php else: ?>
          <span class="badge banned"><i class="fas fa-ban"></i> <?= $currentLang === 'fr' ? 'Banni' : 'Banned' ?></span>
        <?php endif; ?>
      </td>
      <td>
        <form method="POST" action="<?= url('admin/deleted-users/toggle-ban') ?>">
          <?= csrfField() ?>
          <input type="hidden" name="id" value="<?= $du['id'] ?>">
          <input type="hidden" name="current_value" value="<?= $du['can_rejoin'] ?>">
          <?php if ($du['can_rejoin']): ?>
            <button type="submit" class="btn-ban ban" title="Ban from re-registering">
              <i class="fas fa-ban"></i> <?= $currentLang === 'fr' ? 'Bannir' : 'Ban' ?>
            </button>
          <?php else: ?>
            <button type="submit" class="btn-ban unban" title="Allow re-registration">
              <i class="fas fa-check"></i> <?= $currentLang === 'fr' ? 'Débannir' : 'Unban' ?>
            </button>
          <?php endif; ?>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<p style="font-size:12px;color:#9ca3af;margin-top:10px;text-align:right;">
  <?= count($deletedUsers) ?> <?= $currentLang === 'fr' ? 'enregistrement(s)' : 'record(s)' ?>
</p>
<?php endif; ?>

<?php
$content = ob_get_clean();
$pageTitle = $currentLang === 'fr' ? 'Utilisateurs supprimés' : 'Deleted Users';
require BASE_PATH . '/app/Views/admin/layout.php';
?>
