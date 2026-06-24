<?php
$pageTitle   = 'Newsletter';
$currentPage = 'newsletter';
$lang = $_SESSION['language'] ?? 'en';
$fr = ($lang === 'fr');

ob_start();
?>
<style>
  .page-header { margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
  .page-header h1 { font-size: 26px; font-weight: 700; color: var(--dark); margin-bottom: 4px; }
  .page-header p  { font-size: 14px; color: var(--gray-600); }
  .btn-outline {
    display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px;
    border: 2px solid var(--border); border-radius: var(--radius-md); font-size: 13px;
    font-weight: 600; color: var(--gray-700); background: #fff; text-decoration: none; cursor: pointer;
  }
  .btn-outline:hover { background: var(--gray-50); }

  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 26px; }
  .stat-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 18px; }
  .stat-card .num { font-size: 26px; font-weight: 700; color: var(--primary); }
  .stat-card .lbl { font-size: 12.5px; color: var(--gray-600); margin-top: 2px; }

  .card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 26px; }
  .card h2 { font-size: 17px; font-weight: 700; margin-bottom: 16px; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .form-group { margin-bottom: 16px; }
  .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--gray-700); }
  .form-group input, .form-group select, .form-group textarea {
    width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: var(--radius-md);
    font-size: 14px; font-family: inherit; box-sizing: border-box;
  }
  .form-group textarea { min-height: 150px; resize: vertical; }
  .hint { font-size: 12px; color: var(--gray-500); margin-top: 4px; }
  .form-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 8px; }
  .btn { padding: 11px 22px; border: none; border-radius: var(--radius-md); font-size: 14px; font-weight: 600; cursor: pointer; }
  .btn-primary { background: var(--primary); color: #fff; }
  .btn-primary:hover { opacity: .92; }
  .btn-test { background: #fff; color: var(--primary); border: 2px solid var(--primary); }

  table { width: 100%; border-collapse: collapse; }
  th, td { padding: 11px 12px; text-align: left; font-size: 13.5px; border-bottom: 1px solid var(--border); }
  th { font-size: 11.5px; text-transform: uppercase; color: var(--gray-500); letter-spacing: .4px; }
  .badge { padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 600; }
  .badge-sent { background: #dcfce7; color: #166534; }
  .badge-sending { background: #fef3c7; color: #92400e; }
  .badge-draft { background: #f3f4f6; color: #374151; }
  .badge-failed { background: #fee2e2; color: #991b1b; }
  .flash { padding: 12px 16px; border-radius: var(--radius-md); margin-bottom: 18px; font-size: 14px; }
  .flash-success { background: #e8f5e9; color: #2e7d32; }
  .flash-error { background: #fce4ec; color: #c62828; }
  .empty { text-align: center; color: var(--gray-500); padding: 24px; font-size: 14px; }
  @media (max-width: 700px) { .form-row { grid-template-columns: 1fr; } }
</style>

<div class="page-header">
  <div>
    <h1><?= $fr ? 'Infolettres' : 'Newsletter' ?></h1>
    <p><?= $fr ? 'Composez et envoyez des infolettres aux abonnes par liste.' : 'Compose and send newsletters to subscribers by list.' ?></p>
  </div>
  <a href="<?= url('admin/newsletter/subscribers') ?>" class="btn-outline">
    <i class="fa-solid fa-users"></i> <?= $fr ? 'Abonnes' : 'Subscribers' ?>
  </a>
</div>

<?php if ($msg = getFlash('success')): ?>
  <div class="flash flash-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if ($msg = getFlash('error')): ?>
  <div class="flash flash-error"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="stats-grid">
  <div class="stat-card">
    <div class="num"><?= number_format($totalSubscribers) ?></div>
    <div class="lbl"><?= $fr ? 'Abonnes actifs (total)' : 'Active subscribers (total)' ?></div>
  </div>
  <?php foreach ($lists as $list): ?>
  <div class="stat-card">
    <div class="num"><?= number_format($list['active_count']) ?></div>
    <div class="lbl"><?= htmlspecialchars($fr ? $list['name_fr'] : $list['name_en']) ?></div>
  </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <h2><?= $fr ? 'Composer une infolettre' : 'Compose a newsletter' ?></h2>
  <form method="POST" action="<?= url('admin/newsletter/send') ?>" id="composeForm">
    <?= csrfField() ?>
    <div class="form-group">
      <label><?= $fr ? 'Liste de destinataires' : 'Recipient list' ?></label>
      <select name="list_id" required>
        <option value=""><?= $fr ? '-- Choisir une liste --' : '-- Choose a list --' ?></option>
        <?php foreach ($lists as $list): ?>
        <option value="<?= (int) $list['id'] ?>">
          <?= htmlspecialchars($fr ? $list['name_fr'] : $list['name_en']) ?> (<?= number_format($list['active_count']) ?> <?= $fr ? 'abonnes' : 'subscribers' ?>)
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label><?= $fr ? 'Sujet (EN)' : 'Subject (EN)' ?></label>
        <input type="text" name="subject_en" required maxlength="255">
      </div>
      <div class="form-group">
        <label><?= $fr ? 'Sujet (FR)' : 'Subject (FR)' ?></label>
        <input type="text" name="subject_fr" required maxlength="255">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label><?= $fr ? 'Message (EN)' : 'Message (EN)' ?></label>
        <textarea name="body_en" required></textarea>
        <p class="hint"><?= $fr ? 'Le HTML simple est permis (p, a, strong, etc.).' : 'Basic HTML is allowed (p, a, strong, etc.).' ?></p>
      </div>
      <div class="form-group">
        <label><?= $fr ? 'Message (FR)' : 'Message (FR)' ?></label>
        <textarea name="body_fr" required></textarea>
        <p class="hint"><?= $fr ? 'Les deux langues sont envoyees dans le meme courriel.' : 'Both languages are sent in the same email.' ?></p>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary" name="action" value="send"
        onclick="return confirm('<?= $fr ? 'Envoyer cette infolettre a tous les abonnes actifs de la liste choisie ?' : 'Send this newsletter to all active subscribers of the selected list?' ?>');">
        <i class="fa-solid fa-paper-plane"></i> <?= $fr ? 'Envoyer' : 'Send' ?>
      </button>
      <button type="submit" class="btn btn-test" name="action" value="test">
        <i class="fa-solid fa-flask"></i> <?= $fr ? 'Envoyer un test (a moi)' : 'Send test (to me)' ?>
      </button>
    </div>
  </form>
</div>

<div class="card">
  <h2><?= $fr ? 'Historique des envois' : 'Campaign history' ?></h2>
  <?php if (empty($campaigns)): ?>
    <div class="empty"><?= $fr ? 'Aucune infolettre envoyee pour le moment.' : 'No newsletters sent yet.' ?></div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th><?= $fr ? 'Date' : 'Date' ?></th>
        <th><?= $fr ? 'Liste' : 'List' ?></th>
        <th><?= $fr ? 'Sujet' : 'Subject' ?></th>
        <th><?= $fr ? 'Statut' : 'Status' ?></th>
        <th><?= $fr ? 'Envoyes' : 'Sent' ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($campaigns as $c): ?>
      <tr>
        <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($c['created_at']))) ?></td>
        <td><?= htmlspecialchars($fr ? $c['list_name_fr'] : $c['list_name_en']) ?></td>
        <td><?= htmlspecialchars($fr ? $c['subject_fr'] : $c['subject_en']) ?></td>
        <td><span class="badge badge-<?= htmlspecialchars($c['status']) ?>"><?= htmlspecialchars(ucfirst($c['status'])) ?></span></td>
        <td><?= (int) $c['sent_count'] ?> / <?= (int) $c['recipient_count'] ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
