<?php
$outcomeConfig = [
    'resolved'           => ['label'=>'Resolved',           'color'=>'#10b981','bg'=>'#d1fae5','icon'=>'fa-check-circle'],
    'follow_up'          => ['label'=>'Follow-up needed',   'color'=>'#3b82f6','bg'=>'#dbeafe','icon'=>'fa-rotate-right'],
    'no_answer'          => ['label'=>'No answer',          'color'=>'#9ca3af','bg'=>'#f3f4f6','icon'=>'fa-phone-slash'],
    'voicemail'          => ['label'=>'Left voicemail',     'color'=>'#8b5cf6','bg'=>'#ede9fe','icon'=>'fa-voicemail'],
    'wrong_number'       => ['label'=>'Wrong number',       'color'=>'#ef4444','bg'=>'#fee2e2','icon'=>'fa-xmark'],
    'transferred'        => ['label'=>'Transferred',        'color'=>'#f59e0b','bg'=>'#fef3c7','icon'=>'fa-arrow-right-arrow-left'],
    'callback_scheduled' => ['label'=>'Callback scheduled', 'color'=>'#06b6d4','bg'=>'#cffafe','icon'=>'fa-calendar-check'],
    'other'              => ['label'=>'Other',              'color'=>'#6b7280','bg'=>'#f9fafb','icon'=>'fa-circle-dot'],
];
?>
<style>
.cl-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
.cl-stat  { background:white; border-radius:12px; padding:16px 20px; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center; }
.cl-stat-num { font-size:26px; font-weight:800; color:#111827; line-height:1; }
.cl-stat-lbl { font-size:11px; color:#6b7280; margin-top:5px; }
.cl-filters  { background:white; border-radius:12px; padding:16px 20px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; }
.cl-filter-group { display:flex; flex-direction:column; gap:4px; }
.cl-filter-label { font-size:11px; font-weight:600; color:#6b7280; }
.cl-filter-input { padding:8px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; font-family:inherit; }
.cl-table  { width:100%; border-collapse:collapse; }
.cl-table th { background:#f9fafb; padding:10px 14px; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; text-align:left; border-bottom:1px solid #e5e7eb; }
.cl-table td { padding:12px 14px; border-bottom:1px solid #f3f4f6; font-size:13px; color:#374151; vertical-align:middle; }
.cl-table tr:hover td { background:#fafafa; }
.cl-badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; padding:3px 9px; border-radius:20px; white-space:nowrap; }
.cl-dir-in  { background:#dbeafe; color:#1d4ed8; font-size:10px; font-weight:700; padding:2px 7px; border-radius:10px; }
.cl-dir-out { background:#f0fdf4; color:#166534; font-size:10px; font-weight:700; padding:2px 7px; border-radius:10px; }
.cl-contact-type { font-size:10px; font-weight:600; padding:2px 7px; border-radius:10px; background:#f3f4f6; color:#6b7280; text-transform:capitalize; }
@media (max-width:800px) { .cl-stats { grid-template-columns:repeat(2,1fr); } }
</style>

<!-- Page header -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
  <div>
    <h1 style="font-size:20px;font-weight:700;color:#111827;margin:0;">Call Log</h1>
    <p style="font-size:13px;color:#9ca3af;margin:4px 0 0;">History of all logged calls and dispositions</p>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="/admin/agent-dashboard" style="display:flex;align-items:center;gap:6px;padding:9px 16px;background:white;color:#374151;border:1px solid #e5e7eb;border-radius:9px;font-size:13px;font-weight:600;text-decoration:none;">
      <i class="fa-solid fa-gauge-high"></i> My Dashboard
    </a>
    <button onclick="openDispositionModal()" style="display:flex;align-items:center;gap:6px;padding:9px 16px;background:#00b207;color:white;border:none;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;">
      <i class="fa-solid fa-phone-volume"></i> Log a Call
    </button>
  </div>
</div>

<!-- Stats -->
<div class="cl-stats">
  <div class="cl-stat">
    <div class="cl-stat-num"><?= $todayStats['total'] ?></div>
    <div class="cl-stat-lbl">Calls Today</div>
  </div>
  <div class="cl-stat">
    <div class="cl-stat-num" style="color:#10b981;"><?= $todayStats['resolved'] ?></div>
    <div class="cl-stat-lbl">Resolved Today</div>
  </div>
  <div class="cl-stat">
    <div class="cl-stat-num" style="color:#06b6d4;"><?= $todayStats['callbacks'] ?></div>
    <div class="cl-stat-lbl">Upcoming Callbacks</div>
  </div>
  <div class="cl-stat">
    <div class="cl-stat-num" style="color:#3b82f6;"><?= $todayStats['tickets'] ?></div>
    <div class="cl-stat-lbl">Tickets Created Today</div>
  </div>
</div>

<!-- Filters -->
<form method="GET" action="/admin/call-log" class="cl-filters">
  <div class="cl-filter-group" style="flex:1;min-width:160px;">
    <label class="cl-filter-label">Search</label>
    <input type="text" name="q" class="cl-filter-input" placeholder="Name, phone, notes…" value="<?= htmlspecialchars($search) ?>">
  </div>
  <div class="cl-filter-group">
    <label class="cl-filter-label">Outcome</label>
    <select name="outcome" class="cl-filter-input">
      <option value="">All outcomes</option>
      <?php foreach ($outcomeConfig as $ov => $oc): ?>
        <option value="<?= $ov ?>" <?= $outcome === $ov ? 'selected' : '' ?>><?= $oc['label'] ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="cl-filter-group">
    <label class="cl-filter-label">Agent</label>
    <select name="agent" class="cl-filter-input">
      <option value="">All agents</option>
      <?php foreach ($agents as $ag): ?>
        <option value="<?= $ag['id'] ?>" <?= $agentId === (int)$ag['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($ag['first_name'] . ' ' . $ag['last_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <button type="submit" style="padding:8px 18px;background:#111827;color:white;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
  <?php if ($search || $outcome || $agentId): ?>
    <a href="/admin/call-log" style="padding:8px 14px;background:#f3f4f6;color:#374151;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">Clear</a>
  <?php endif; ?>
</form>

<!-- Table -->
<div style="background:white;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06);overflow:hidden;">
  <div style="padding:14px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;">
    <span style="font-size:13px;font-weight:600;color:#374151;"><?= number_format($total) ?> records</span>
  </div>

  <?php if (empty($logs)): ?>
    <div style="padding:48px;text-align:center;color:#9ca3af;">
      <i class="fa-solid fa-phone-slash" style="font-size:32px;display:block;margin-bottom:12px;"></i>
      No call logs found
    </div>
  <?php else: ?>
  <div style="overflow-x:auto;">
    <table class="cl-table">
      <thead>
        <tr>
          <th>Time</th>
          <th>Agent</th>
          <th>Direction</th>
          <th>Contact</th>
          <th>Outcome</th>
          <th>Notes</th>
          <th>Ticket</th>
          <th>Callback</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log):
          $oc = $outcomeConfig[$log['outcome']] ?? $outcomeConfig['other'];
        ?>
        <tr>
          <td style="white-space:nowrap;color:#9ca3af;font-size:12px;">
            <?= date('M j, g:ia', strtotime($log['created_at'])) ?>
          </td>
          <td style="white-space:nowrap;">
            <?= htmlspecialchars(trim($log['agent_first'] . ' ' . $log['agent_last'])) ?>
          </td>
          <td>
            <span class="<?= $log['direction'] === 'inbound' ? 'cl-dir-in' : 'cl-dir-out' ?>">
              <i class="fa-solid fa-<?= $log['direction'] === 'inbound' ? 'phone-arrow-down-left' : 'phone-arrow-up-right' ?>"></i>
              <?= ucfirst($log['direction']) ?>
            </span>
          </td>
          <td>
            <div style="font-weight:600;"><?= htmlspecialchars($log['contact_name'] ?: '—') ?></div>
            <?php if ($log['contact_phone']): ?>
              <div style="font-size:11px;color:#9ca3af;"><?= htmlspecialchars($log['contact_phone']) ?></div>
            <?php endif; ?>
            <span class="cl-contact-type"><?= $log['contact_type'] ?></span>
          </td>
          <td>
            <span class="cl-badge" style="background:<?= $oc['bg'] ?>;color:<?= $oc['color'] ?>;">
              <i class="fa-solid <?= $oc['icon'] ?>"></i>
              <?= $oc['label'] ?>
            </span>
          </td>
          <td style="max-width:200px;">
            <?php if ($log['notes']): ?>
              <span title="<?= htmlspecialchars($log['notes']) ?>" style="font-size:12px;color:#6b7280;">
                <?= htmlspecialchars(mb_strimwidth($log['notes'], 0, 60, '…')) ?>
              </span>
            <?php else: ?>
              <span style="color:#d1d5db;font-size:12px;">—</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($log['ticket_id']): ?>
              <a href="/admin/support/view?id=<?= $log['ticket_id'] ?>" style="font-size:12px;color:#3b82f6;font-weight:600;text-decoration:none;">
                View ticket →
              </a>
            <?php else: ?>
              <span style="color:#d1d5db;font-size:12px;">—</span>
            <?php endif; ?>
          </td>
          <td style="white-space:nowrap;">
            <?php if ($log['callback_at']): ?>
              <span style="font-size:12px;color:<?= strtotime($log['callback_at']) > time() ? '#06b6d4' : '#9ca3af' ?>;">
                <i class="fa-solid fa-calendar"></i>
                <?= date('M j, g:ia', strtotime($log['callback_at'])) ?>
              </span>
            <?php else: ?>
              <span style="color:#d1d5db;font-size:12px;">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if ($total > $perPage):
    $pages      = (int)ceil($total / $perPage);
    $baseUrl    = '/admin/call-log?';
    $queryParams = [];
    if ($search)  $queryParams['q']       = $search;
    if ($outcome) $queryParams['outcome'] = $outcome;
    if ($agentId) $queryParams['agent']   = $agentId;
    require __DIR__ . '/../../components/pagination.php';
  endif; ?>
  <?php endif; ?>
</div>
