<?php require __DIR__ . '/layout-header.php'; ?>
<?php
$_frM = ['01'=>'jan','02'=>'fév','03'=>'mars','04'=>'avr','05'=>'mai','06'=>'juin',
         '07'=>'juil','08'=>'août','09'=>'sep','10'=>'oct','11'=>'nov','12'=>'déc'];
function _bizDateFr(string $d, bool $fr, array $m, bool $withTime = false): string {
    $ts = strtotime($d);
    if ($fr) {
        $base = date('j', $ts) . ' ' . ($m[date('m', $ts)] ?? date('M', $ts)) . ' ' . date('Y', $ts);
        return $withTime ? $base . ' à ' . date('G', $ts) . 'h' . date('i', $ts) : $base;
    }
    return $withTime ? date('M j, Y \a\t g:i A', $ts) : date('M j, Y', $ts);
}
$_emailStatusFr = ['sent' => 'Envoyé', 'failed' => 'Échoué', 'test_mode' => 'Mode test'];
$_emailStatusEn = ['sent' => 'Sent',   'failed' => 'Failed',  'test_mode' => 'Test mode'];
$fr = (($currentLang ?? 'fr') === 'fr');
?>

<style>
  .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px; }
  .stat-card { background: white; border-radius: 12px; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
  .stat-label { font-size: 13px; color: var(--gray-400); font-weight: 500; margin-bottom: 6px; }
  .stat-value { font-size: 24px; font-weight: 700; color: var(--gray-700); }

  .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 24px; }
  .card-title { font-size: 16px; font-weight: 700; color: var(--gray-700); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }

  .email-list { display: flex; flex-direction: column; gap: 8px; }
  .email-item {
    padding: 14px 16px; border: 1px solid var(--gray-100); border-radius: 10px;
    display: flex; justify-content: space-between; align-items: center;
    transition: background 0.15s;
  }
  .email-item:hover { background: var(--gray-50); }

  .email-subject { font-size: 14px; font-weight: 600; color: var(--gray-700); margin-bottom: 4px; }
  .email-meta { font-size: 12px; color: var(--gray-400); display: flex; gap: 12px; align-items: center; }

  .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; text-transform: uppercase; }
  .badge-sent { background: #d1fae5; color: #065f46; }
  .badge-failed { background: #fee2e2; color: #991b1b; }
  .badge-test_mode { background: #fef3c7; color: #92400e; }

  .type-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; background: var(--gray-100); color: var(--gray-600); text-transform: uppercase; }

  .empty-state { text-align: center; padding: 40px; color: var(--gray-400); }
  .empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }

  @media (max-width: 768px) { .stats-row { grid-template-columns: 1fr; } }
</style>

<div class="stats-row">
  <div class="stat-card">
    <div class="stat-label"><?= $fr ? 'Courriels total' : 'Total Emails' ?></div>
    <div class="stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label"><?= $fr ? 'Livrés' : 'Delivered' ?></div>
    <div class="stat-value" style="color:#059669;"><?= number_format($stats['sent_count'] ?? 0) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label"><?= $fr ? 'Dernier courriel' : 'Last Email' ?></div>
    <div class="stat-value" style="font-size:16px;">
      <?= ($stats['last_email'] ?? null) ? _bizDateFr($stats['last_email'], $fr, $_frM) : '—' ?>
    </div>
  </div>
</div>

<div class="card">
  <h3 class="card-title"><i class="fas fa-envelope-open-text" style="color:var(--primary);"></i> <?= $fr ? 'Historique des courriels' : 'Email History' ?></h3>

  <?php if (empty($emails)): ?>
    <div class="empty-state">
      <i class="fas fa-envelope-open-text"></i>
      <p><?= $fr ? 'Aucun courriel pour l\'instant.' : 'No emails yet.' ?></p>
      <p style="font-size:13px;"><?= $fr ? "L'historique de tous les courriels envoyés à votre compte s'affichera ici." : 'A history of all emails sent to your account will appear here.' ?></p>
    </div>
  <?php else: ?>
  <div class="email-list">
    <?php foreach ($emails as $em): ?>
    <div class="email-item">
      <div>
        <div class="email-subject"><?= htmlspecialchars($em['subject']) ?></div>
        <div class="email-meta">
          <span><?= _bizDateFr($em['created_at'], $fr, $_frM, true) ?></span>
          <?php if ($em['email_type']): ?>
            <span class="type-badge"><?= htmlspecialchars(str_replace('_', ' ', $em['email_type'])) ?></span>
          <?php endif; ?>
        </div>
      </div>
      <div>
        <span class="badge badge-<?= $em['status'] ?>"><?= ($fr ? $_emailStatusFr : $_emailStatusEn)[$em['status']] ?? ucfirst(str_replace('_', ' ', $em['status'])) ?></span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/layout-footer.php'; ?>
