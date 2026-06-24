<?php
/**
 * Agent Dashboard — personal contact-center workspace
 */
$statusConfig = [
    'available' => ['label' => 'Available',  'color' => '#10b981', 'bg' => '#d1fae5', 'dot' => '#10b981'],
    'busy'      => ['label' => 'Busy',       'color' => '#ef4444', 'bg' => '#fee2e2', 'dot' => '#ef4444'],
    'break'     => ['label' => 'On Break',   'color' => '#f59e0b', 'bg' => '#fef3c7', 'dot' => '#f59e0b'],
    'offline'   => ['label' => 'Offline',    'color' => '#9ca3af', 'bg' => '#f3f4f6', 'dot' => '#9ca3af'],
];
$priorityColor = ['urgent'=>'#ef4444','high'=>'#f59e0b','medium'=>'#3b82f6','low'=>'#9ca3af'];
$priorityBg    = ['urgent'=>'#fee2e2','high'=>'#fef3c7','medium'=>'#dbeafe','low'=>'#f3f4f6'];
$statusColor   = ['open'=>'#3b82f6','in_progress'=>'#8b5cf6','pending_contact'=>'#f59e0b','resolved'=>'#10b981','closed'=>'#9ca3af'];
$statusBg      = ['open'=>'#dbeafe','in_progress'=>'#ede9fe','pending_contact'=>'#fef3c7','resolved'=>'#d1fae5','closed'=>'#f3f4f6'];

$sc   = $statusConfig[$agentStatus] ?? $statusConfig['offline'];
$myName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? ''));
$myInitials = $myName ? strtoupper(implode('', array_slice(array_map(fn($w)=>$w[0], explode(' ', $myName)), 0, 2))) : '?';
$csrfName = env('CSRF_TOKEN_NAME', '_csrf_token');
$csrfVal  = generateCsrfToken();

function ageLabel(string $dt): string {
    $secs = time() - strtotime($dt);
    if ($secs < 60) return 'just now';
    if ($secs < 3600) return (int)($secs/60) . 'm ago';
    if ($secs < 86400) return (int)($secs/3600) . 'h ago';
    return (int)($secs/86400) . 'd ago';
}
?>
<style>
/* ---- Layout ---- */
.agd-page { display:flex; flex-direction:column; gap:20px; }
.agd-top  { display:flex; align-items:center; gap:16px; flex-wrap:wrap; }

/* ---- Agent identity card ---- */
.agd-identity { display:flex; align-items:center; gap:14px; background:white; border-radius:14px; padding:18px 22px; box-shadow:0 2px 8px rgba(0,0,0,.06); flex:1; min-width:280px; }
.agd-avatar   { width:52px; height:52px; border-radius:50%; background:#00b207; color:white; font-size:18px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.agd-name     { font-size:16px; font-weight:700; color:#111827; line-height:1.2; }
.agd-role     { font-size:12px; color:#9ca3af; margin-top:2px; text-transform:capitalize; }

/* ---- Status selector ---- */
.agd-status-wrap { display:flex; align-items:center; gap:10px; margin-left:auto; }
.agd-status-dot  { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.agd-status-label{ font-size:13px; font-weight:600; }
.agd-status-select { padding:6px 12px; border-radius:8px; border:1px solid #e5e7eb; font-size:13px; font-weight:600; cursor:pointer; background:white; }

/* ---- Stats row ---- */
.agd-stats   { display:grid; grid-template-columns:repeat(5,1fr); gap:14px; }
.agd-stat    { background:white; border-radius:12px; padding:16px 20px; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center; }
.agd-stat-num{ font-size:28px; font-weight:800; color:#111827; line-height:1; }
.agd-stat-lbl{ font-size:11px; color:#6b7280; margin-top:6px; font-weight:500; }
.agd-stat.accent-green .agd-stat-num { color:#10b981; }
.agd-stat.accent-blue  .agd-stat-num { color:#3b82f6; }
.agd-stat.accent-amber .agd-stat-num { color:#f59e0b; }
.agd-stat.accent-red   .agd-stat-num { color:#ef4444; }

/* ---- Main columns ---- */
.agd-cols  { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
.agd-panel { background:white; border-radius:14px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden; }
.agd-panel-hdr { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid #f3f4f6; }
.agd-panel-title { font-size:13px; font-weight:700; color:#374151; display:flex; align-items:center; gap:8px; }
.agd-panel-count { font-size:11px; background:#f3f4f6; color:#6b7280; border-radius:20px; padding:2px 8px; font-weight:600; }
.agd-panel-body  { overflow-y:auto; max-height:420px; }

/* ---- Ticket rows ---- */
.agd-ticket-row { display:flex; align-items:flex-start; gap:12px; padding:12px 20px; border-bottom:1px solid #f9fafb; text-decoration:none; transition:background .15s; }
.agd-ticket-row:hover { background:#f9fafb; }
.agd-ticket-row:last-child { border-bottom:none; }
.agd-t-body  { flex:1; min-width:0; }
.agd-t-top   { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:4px; }
.agd-t-num   { font-size:10px; color:#9ca3af; font-weight:600; }
.agd-t-subject { font-size:13px; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.agd-t-contact { font-size:11px; color:#6b7280; }
.agd-t-age   { font-size:10px; color:#9ca3af; margin-left:auto; flex-shrink:0; }
.t-badge     { font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px; }

/* ---- Follow-up rows ---- */
.agd-followup-row { display:flex; align-items:center; gap:10px; padding:10px 20px; border-bottom:1px solid #f9fafb; }
.agd-followup-row:last-child { border-bottom:none; }
.agd-f-dot  { width:8px; height:8px; border-radius:50%; background:#f59e0b; flex-shrink:0; }
.agd-f-body { flex:1; min-width:0; }
.agd-f-sub  { font-size:13px; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.agd-f-meta { font-size:11px; color:#9ca3af; margin-top:2px; }
.agd-f-link { font-size:11px; font-weight:600; color:#00b207; text-decoration:none; flex-shrink:0; }

/* ---- Bottom row ---- */
.agd-bottom { display:grid; grid-template-columns:1fr 1fr; gap:20px; }

/* ---- Activity feed ---- */
.agd-activity-row { display:flex; align-items:flex-start; gap:10px; padding:10px 20px; border-bottom:1px solid #f9fafb; }
.agd-activity-row:last-child { border-bottom:none; }
.agd-act-icon { width:28px; height:28px; border-radius:50%; background:#eff6ff; color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:11px; flex-shrink:0; margin-top:1px; }
.agd-act-body  { flex:1; min-width:0; }
.agd-act-text  { font-size:12px; color:#374151; }
.agd-act-meta  { font-size:10px; color:#9ca3af; margin-top:2px; }

/* ---- Team panel ---- */
.agd-team-row { display:flex; align-items:center; gap:10px; padding:10px 20px; border-bottom:1px solid #f9fafb; }
.agd-team-row:last-child { border-bottom:none; }
.agd-team-av  { width:30px; height:30px; border-radius:50%; background:#e5e7eb; font-size:11px; font-weight:700; color:#374151; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.agd-team-name{ font-size:13px; font-weight:600; color:#111827; flex:1; }
.agd-team-count{ font-size:11px; color:#9ca3af; }
.agd-team-status{ display:flex; align-items:center; gap:5px; }
.agd-team-dot  { width:8px; height:8px; border-radius:50%; }

/* Responsive */
@media (max-width:900px) {
    .agd-stats { grid-template-columns:repeat(3,1fr); }
    .agd-cols  { grid-template-columns:1fr; }
    .agd-bottom{ grid-template-columns:1fr; }
}
</style>

<div class="agd-page">

  <!-- Top: Identity + Status -->
  <div class="agd-top">
    <div class="agd-identity">
      <div class="agd-avatar"><?= htmlspecialchars($myInitials) ?></div>
      <div>
        <div class="agd-name"><?= htmlspecialchars($myName) ?></div>
        <div class="agd-role"><?= str_replace('_', ' ', $_SESSION['user']['role'] ?? 'agent') ?></div>
      </div>
      <div class="agd-status-wrap" style="margin-left:auto;">
        <div class="agd-status-dot" id="statusDot" style="background:<?= $sc['dot'] ?>;"></div>
        <span class="agd-status-label" id="statusLabel" style="color:<?= $sc['color'] ?>;"><?= $sc['label'] ?></span>
        <select class="agd-status-select" id="agentStatusSelect" onchange="updateAgentStatus(this.value)">
          <?php foreach ($statusConfig as $sv => $scfg): ?>
            <option value="<?= $sv ?>" <?= $agentStatus === $sv ? 'selected' : '' ?>><?= $scfg['label'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div style="display:flex; gap:10px; flex-shrink:0;">
      <button onclick="openDispositionModal()" style="display:flex;align-items:center;gap:6px;padding:10px 16px;background:#00b207;color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;">
        <i class="fa-solid fa-phone-volume"></i> Log Call
      </button>
      <a href="/admin/support/create" style="display:flex;align-items:center;gap:6px;padding:10px 16px;background:white;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;">
        <i class="fa-solid fa-plus"></i> New Ticket
      </a>
      <a href="/admin/support" style="display:flex;align-items:center;gap:6px;padding:10px 16px;background:white;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;">
        <i class="fa-solid fa-inbox"></i> Full Inbox
      </a>
      <a href="/admin/call-log" style="display:flex;align-items:center;gap:6px;padding:10px 16px;background:white;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;">
        <i class="fa-solid fa-clock-rotate-left"></i> Call History
      </a>
    </div>
  </div>

  <!-- Stats Row -->
  <div class="agd-stats">
    <div class="agd-stat accent-blue">
      <div class="agd-stat-num"><?= $stats['my_open'] ?></div>
      <div class="agd-stat-lbl">My Open Tickets</div>
    </div>
    <div class="agd-stat accent-green">
      <div class="agd-stat-num"><?= $stats['my_resolved_today'] ?></div>
      <div class="agd-stat-lbl">Resolved Today</div>
    </div>
    <div class="agd-stat accent-blue">
      <div class="agd-stat-num"><?= $stats['replies_today'] ?></div>
      <div class="agd-stat-lbl">Replies Sent Today</div>
    </div>
    <div class="agd-stat accent-red">
      <div class="agd-stat-num"><?= $stats['urgent_open'] ?></div>
      <div class="agd-stat-lbl">Urgent Open (All)</div>
    </div>
    <div class="agd-stat accent-amber">
      <div class="agd-stat-num"><?= $stats['unassigned'] ?></div>
      <div class="agd-stat-lbl"><a href="/admin/support?filter=unassigned" style="color:inherit;text-decoration:none;">Unassigned</a></div>
    </div>
  </div>

  <!-- Main 2-column area -->
  <div class="agd-cols">

    <!-- My Queue -->
    <div class="agd-panel">
      <div class="agd-panel-hdr">
        <div class="agd-panel-title">
          <i class="fa-solid fa-list-check" style="color:#3b82f6;"></i>
          My Open Queue
        </div>
        <span class="agd-panel-count"><?= count($myQueue) ?></span>
      </div>
      <div class="agd-panel-body">
        <?php if (empty($myQueue)): ?>
          <div style="padding:32px 20px;text-align:center;color:#9ca3af;font-size:13px;">
            <i class="fa-solid fa-check-circle" style="font-size:24px;color:#10b981;display:block;margin-bottom:8px;"></i>
            All clear — no open tickets assigned to you
          </div>
        <?php else: ?>
          <?php foreach ($myQueue as $t):
            $pColor = $priorityColor[$t['priority']] ?? '#9ca3af';
            $pBg    = $priorityBg[$t['priority']] ?? '#f3f4f6';
            $sColor = $statusColor[$t['status']] ?? '#9ca3af';
            $sBg    = $statusBg[$t['status']] ?? '#f3f4f6';
            $lastAt = $t['last_reply_at'] ?? $t['updated_at'];
          ?>
          <a href="/admin/support/view?id=<?= $t['id'] ?>" class="agd-ticket-row">
            <div class="agd-t-body">
              <div class="agd-t-top">
                <span class="agd-t-num"><?= htmlspecialchars($t['ticket_number']) ?></span>
                <span class="t-badge" style="background:<?= $pBg ?>;color:<?= $pColor ?>;"><?= $t['priority'] ?></span>
                <span class="t-badge" style="background:<?= $sBg ?>;color:<?= $sColor ?>;"><?= str_replace('_',' ',$t['status']) ?></span>
              </div>
              <div class="agd-t-subject"><?= htmlspecialchars($t['subject']) ?></div>
              <?php if ($t['contact_name']): ?>
                <div class="agd-t-contact"><?= htmlspecialchars($t['contact_name']) ?></div>
              <?php endif; ?>
            </div>
            <div class="agd-t-age"><?= ageLabel($lastAt) ?></div>
          </a>
          <?php endforeach; ?>
          <?php if ($stats['my_open'] > count($myQueue)): ?>
            <div style="padding:10px 20px;text-align:center;">
              <a href="/admin/support?filter=mine" style="font-size:12px;color:#3b82f6;text-decoration:none;font-weight:600;">View all <?= $stats['my_open'] ?> →</a>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Follow-ups / Pending Contact -->
    <div class="agd-panel">
      <div class="agd-panel-hdr">
        <div class="agd-panel-title">
          <i class="fa-solid fa-clock-rotate-left" style="color:#f59e0b;"></i>
          Pending Contact
        </div>
        <span class="agd-panel-count"><?= count($followUps) ?></span>
      </div>
      <div class="agd-panel-body">
        <?php if (empty($followUps)): ?>
          <div style="padding:32px 20px;text-align:center;color:#9ca3af;font-size:13px;">
            <i class="fa-solid fa-thumbs-up" style="font-size:24px;color:#10b981;display:block;margin-bottom:8px;"></i>
            No tickets waiting on contact
          </div>
        <?php else: ?>
          <?php foreach ($followUps as $t):
            $lastAt = $t['updated_at'] ?? $t['created_at'];
            $agentName = $t['agent_first'] ? trim($t['agent_first'] . ' ' . $t['agent_last']) : null;
          ?>
          <div class="agd-followup-row">
            <div class="agd-f-dot"></div>
            <div class="agd-f-body">
              <div class="agd-f-sub"><?= htmlspecialchars($t['subject']) ?></div>
              <div class="agd-f-meta">
                <?= htmlspecialchars($t['ticket_number']) ?>
                <?= $t['contact_name'] ? ' · ' . htmlspecialchars($t['contact_name']) : '' ?>
                <?= $agentName ? ' · ' . htmlspecialchars($agentName) : ' · Unassigned' ?>
                · <?= ageLabel($lastAt) ?>
              </div>
            </div>
            <a href="/admin/support/view?id=<?= $t['id'] ?>" class="agd-f-link">Open →</a>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

  </div><!-- /agd-cols -->

  <!-- Bottom row: Activity + Team -->
  <div class="agd-bottom">

    <!-- Recent Activity -->
    <div class="agd-panel">
      <div class="agd-panel-hdr">
        <div class="agd-panel-title">
          <i class="fa-solid fa-bolt" style="color:#8b5cf6;"></i>
          My Activity Today
        </div>
        <span class="agd-panel-count"><?= count($recentActivity) ?></span>
      </div>
      <div class="agd-panel-body">
        <?php if (empty($recentActivity)): ?>
          <div style="padding:32px 20px;text-align:center;color:#9ca3af;font-size:13px;">No activity logged yet today</div>
        <?php else: ?>
          <?php foreach ($recentActivity as $act): ?>
          <div class="agd-activity-row">
            <div class="agd-act-icon">
              <?php if ((int)$act['is_internal']): ?>
                <i class="fa-solid fa-lock"></i>
              <?php else: ?>
                <i class="fa-solid fa-reply"></i>
              <?php endif; ?>
            </div>
            <div class="agd-act-body">
              <div class="agd-act-text">
                <?= (int)$act['is_internal'] ? '<span style="color:#f59e0b;font-weight:600;font-size:10px;">NOTE</span> ' : '' ?>
                <a href="/admin/support/view?id=<?= $act['ticket_id'] ?>" style="color:#374151;text-decoration:none;">
                  <?= htmlspecialchars(mb_strimwidth($act['message'], 0, 80, '…')) ?>
                </a>
              </div>
              <div class="agd-act-meta">
                <?= htmlspecialchars($act['ticket_number']) ?> — <?= htmlspecialchars(mb_strimwidth($act['subject'],0,40,'…')) ?>
                · <?= date('g:ia', strtotime($act['created_at'])) ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Team Status -->
    <div class="agd-panel">
      <div class="agd-panel-hdr">
        <div class="agd-panel-title">
          <i class="fa-solid fa-users" style="color:#00b207;"></i>
          Team Status
        </div>
        <span class="agd-panel-count"><?= count($team) ?></span>
      </div>
      <div class="agd-panel-body">
        <?php foreach ($team as $member):
          $msc = $statusConfig[$member['agent_status']] ?? $statusConfig['offline'];
          $mInit = strtoupper(substr($member['first_name'],0,1) . substr($member['last_name'],0,1));
          $isMe = (int)$member['id'] === (int)($_SESSION['user']['id'] ?? 0);
        ?>
        <div class="agd-team-row">
          <div class="agd-team-av" style="background:<?= $isMe ? '#00b207' : '#e5e7eb' ?>;color:<?= $isMe ? 'white' : '#374151' ?>;">
            <?= htmlspecialchars($mInit) ?>
          </div>
          <div>
            <div class="agd-team-name">
              <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
              <?= $isMe ? ' <span style="font-size:10px;color:#9ca3af;">(you)</span>' : '' ?>
            </div>
            <div class="agd-team-count"><?= $member['open_count'] ?> open tickets</div>
          </div>
          <div class="agd-team-status" style="margin-left:auto;">
            <div class="agd-team-dot" style="background:<?= $msc['dot'] ?>;"></div>
            <span style="font-size:12px;font-weight:600;color:<?= $msc['color'] ?>;"><?= $msc['label'] ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div><!-- /agd-bottom -->

</div><!-- /agd-page -->

<script>
const agentStatusColors = {
  available: { color:'#10b981', dot:'#10b981', label:'Available' },
  busy:      { color:'#ef4444', dot:'#ef4444', label:'Busy' },
  break:     { color:'#f59e0b', dot:'#f59e0b', label:'On Break' },
  offline:   { color:'#9ca3af', dot:'#9ca3af', label:'Offline' },
};

function updateAgentStatus(newStatus) {
  const dot   = document.getElementById('statusDot');
  const label = document.getElementById('statusLabel');
  const cfg   = agentStatusColors[newStatus] || agentStatusColors.offline;

  fetch('/admin/agent-dashboard/status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      status: newStatus,
      '<?= htmlspecialchars($csrfName) ?>': '<?= htmlspecialchars($csrfVal) ?>',
    }),
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      dot.style.background   = cfg.dot;
      label.style.color      = cfg.color;
      label.textContent      = cfg.label;
    }
  })
  .catch(() => {});
}
</script>
