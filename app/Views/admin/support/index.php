<?php
// Priority colors
$priorityColor = ['urgent'=>'#ef4444','high'=>'#f59e0b','medium'=>'#3b82f6','low'=>'#9ca3af'];
$priorityBg    = ['urgent'=>'#fee2e2','high'=>'#fef3c7','medium'=>'#dbeafe','low'=>'#f3f4f6'];
$statusColor   = ['open'=>'#3b82f6','in_progress'=>'#8b5cf6','pending_contact'=>'#f59e0b','resolved'=>'#10b981','closed'=>'#9ca3af'];
$statusBg      = ['open'=>'#dbeafe','in_progress'=>'#ede9fe','pending_contact'=>'#fef3c7','resolved'=>'#d1fae5','closed'=>'#f3f4f6'];
$channelIcon   = ['phone'=>'fa-phone','email'=>'fa-envelope','web_form'=>'fa-globe','walk_in'=>'fa-person-walking','chat'=>'fa-comments'];

function ticketAge(string $date): string {
    $diff = time() - strtotime($date);
    if ($diff < 3600)  return floor($diff/60)  . 'm';
    if ($diff < 86400) return floor($diff/3600) . 'h';
    return floor($diff/86400) . 'd';
}
?>
<style>
.support-layout { display:flex; gap:0; height:calc(100vh - 64px); overflow:hidden; margin:-32px; }

/* Left sidebar */
.support-sidebar { width:220px; flex-shrink:0; background:white; border-right:1px solid #e5e7eb; display:flex; flex-direction:column; overflow:hidden; }
.support-sidebar-top { padding:16px; border-bottom:1px solid #f3f4f6; }
.support-sidebar-top h2 { font-size:16px; font-weight:700; color:#111827; }
.new-ticket-btn { display:flex; align-items:center; gap:6px; width:100%; margin-top:10px; padding:8px 14px; background:#00b207; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; transition:background 0.2s; }
.new-ticket-btn:hover { background:#009206; color:white; }
.filter-nav { padding:8px; flex:1; overflow-y:auto; }
.filter-link { display:flex; align-items:center; justify-content:space-between; padding:9px 12px; border-radius:8px; font-size:13px; color:#374151; text-decoration:none; transition:background 0.15s; cursor:pointer; }
.filter-link:hover { background:#f3f4f6; }
.filter-link.active { background:#f0fdf4; color:#00b207; font-weight:600; }
.filter-link i { width:16px; text-align:center; margin-right:8px; color:#9ca3af; }
.filter-link.active i { color:#00b207; }
.filter-count { font-size:11px; font-weight:700; padding:2px 7px; border-radius:10px; background:#e5e7eb; color:#6b7280; }
.filter-link.active .filter-count { background:#bbf7d0; color:#065f46; }
.filter-count.urgent { background:#fee2e2; color:#dc2626; }

/* Ticket list */
.ticket-list-col { width:360px; flex-shrink:0; border-right:1px solid #e5e7eb; display:flex; flex-direction:column; background:white; overflow:hidden; }
.ticket-list-header { padding:12px 16px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:8px; }
.ticket-search { flex:1; position:relative; }
.ticket-search input { width:100%; padding:7px 12px 7px 32px; border:1px solid #e5e7eb; border-radius:8px; font-size:12px; font-family:inherit; }
.ticket-search input:focus { outline:none; border-color:#00b207; }
.ticket-search i { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:12px; }
.ticket-list-body { flex:1; overflow-y:auto; }
.ticket-item { padding:12px 16px; border-bottom:1px solid #f9fafb; cursor:pointer; transition:background 0.1s; position:relative; }
.ticket-item:hover { background:#f9fafb; }
.ticket-item.active { background:#f0fdf4; border-left:3px solid #00b207; }
.ticket-item-top { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:4px; }
.ticket-num { font-size:11px; font-weight:700; color:#6b7280; }
.ticket-age { font-size:11px; color:#9ca3af; flex-shrink:0; }
.ticket-subject { font-size:13px; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:4px; }
.ticket-meta { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.t-badge { font-size:10px; font-weight:600; padding:2px 7px; border-radius:8px; }
.ticket-contact { font-size:12px; color:#6b7280; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:4px; }
.ticket-agent { font-size:11px; color:#9ca3af; }
.empty-list { padding:40px 20px; text-align:center; color:#9ca3af; }
.empty-list i { font-size:32px; margin-bottom:10px; display:block; }

/* Ticket detail */
.ticket-detail-col { flex:1; display:flex; flex-direction:column; background:#f9fafb; overflow:hidden; }
.detail-placeholder { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#d1d5db; }
.detail-placeholder i { font-size:48px; margin-bottom:12px; }
.detail-placeholder p { font-size:14px; }

/* Detail panel */
.detail-header { padding:14px 20px; background:white; border-bottom:1px solid #e5e7eb; display:flex; align-items:flex-start; gap:12px; }
.detail-header-left { flex:1; min-width:0; }
.detail-ticket-num { font-size:11px; font-weight:700; color:#9ca3af; margin-bottom:2px; }
.detail-subject { font-size:15px; font-weight:700; color:#111827; }
.detail-badges { display:flex; gap:6px; margin-top:6px; flex-wrap:wrap; }
.detail-header-right { display:flex; gap:6px; flex-shrink:0; }
.icon-btn { background:#f3f4f6; border:none; width:32px; height:32px; border-radius:8px; cursor:pointer; color:#6b7280; font-size:13px; display:flex; align-items:center; justify-content:center; transition:all 0.15s; }
.icon-btn:hover { background:#e5e7eb; color:#111827; }

.detail-contact-bar { padding:10px 20px; background:white; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:16px; }
.contact-avatar { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px; color:white; flex-shrink:0; }
.contact-info { flex:1; min-width:0; }
.contact-name { font-size:13px; font-weight:600; color:#111827; }
.contact-sub { font-size:11px; color:#9ca3af; }
.contact-actions { display:flex; gap:6px; }
.contact-action-btn { display:inline-flex; align-items:center; gap:4px; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none; border:1px solid; transition:all 0.15s; }

.detail-controls { padding:8px 20px; background:white; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.control-select { padding:5px 10px; border:1px solid #e5e7eb; border-radius:7px; font-size:12px; font-family:inherit; background:white; cursor:pointer; }
.control-select:focus { outline:none; border-color:#00b207; }

/* Thread */
.thread-body { flex:1; overflow-y:auto; padding:16px 20px; display:flex; flex-direction:column; gap:10px; }
.msg-bubble { max-width:75%; }
.msg-bubble.agent { align-self:flex-end; }
.msg-bubble.contact { align-self:flex-start; }
.msg-bubble.system { align-self:center; }
.msg-inner { padding:10px 14px; border-radius:12px; font-size:13px; line-height:1.5; }
.msg-bubble.agent .msg-inner { background:#00b207; color:white; border-bottom-right-radius:3px; }
.msg-bubble.agent.internal .msg-inner { background:#fef3c7; color:#92400e; border:1px dashed #d97706; }
.msg-bubble.contact .msg-inner { background:white; color:#111827; border:1px solid #e5e7eb; border-bottom-left-radius:3px; box-shadow:0 1px 3px rgba(0,0,0,0.05); }
.msg-bubble.system .msg-inner { background:#f3f4f6; color:#9ca3af; font-size:11px; padding:4px 12px; border-radius:20px; }
.msg-meta { font-size:10px; margin-top:3px; color:#9ca3af; }
.msg-bubble.agent .msg-meta { text-align:right; }
.internal-badge { font-size:9px; background:#fef3c7; color:#92400e; padding:1px 6px; border-radius:6px; font-weight:700; margin-left:4px; }

/* Reply box */
.reply-box { padding:12px 20px; background:white; border-top:1px solid #e5e7eb; }
.reply-toggle { display:flex; gap:4px; margin-bottom:8px; }
.reply-toggle-btn { padding:4px 12px; border-radius:20px; border:1px solid #e5e7eb; font-size:12px; font-weight:600; cursor:pointer; background:white; color:#6b7280; transition:all 0.15s; }
.reply-toggle-btn.active { background:#111827; color:white; border-color:#111827; }
.reply-toggle-btn.internal.active { background:#f59e0b; color:white; border-color:#f59e0b; }
.reply-textarea { width:100%; padding:10px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; font-family:inherit; resize:none; min-height:72px; transition:border 0.15s; }
.reply-textarea:focus { outline:none; border-color:#00b207; }
.reply-footer { display:flex; justify-content:space-between; align-items:center; margin-top:8px; }
.send-btn { padding:7px 20px; background:#00b207; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:background 0.15s; }
.send-btn:hover { background:#009206; }
.send-btn.internal-mode { background:#f59e0b; }
.send-btn.internal-mode:hover { background:#d97706; }

@media (max-width:1200px) { .ticket-list-col { width:300px; } }
@media (max-width:900px) { .support-sidebar { display:none; } .ticket-list-col { width:260px; } }
</style>

<div class="support-layout">

  <!-- LEFT SIDEBAR -->
  <div class="support-sidebar">
    <div class="support-sidebar-top">
      <h2><i class="fa-solid fa-headset" style="color:#00b207;margin-right:6px;"></i>Support</h2>
      <a href="/admin/support/create" class="new-ticket-btn">
        <i class="fa-solid fa-plus"></i> New Ticket
      </a>
    </div>
    <nav class="filter-nav">
      <a href="/admin/support?filter=all" class="filter-link <?= $filter==='all'?'active':'' ?>">
        <span><i class="fa-solid fa-inbox"></i>All Open</span>
        <span class="filter-count"><?= $stats['open'] ?></span>
      </a>
      <a href="/admin/support?filter=mine" class="filter-link <?= $filter==='mine'?'active':'' ?>">
        <span><i class="fa-solid fa-user-check"></i>My Queue</span>
        <?php if($stats['mine']): ?><span class="filter-count"><?= $stats['mine'] ?></span><?php endif; ?>
      </a>
      <a href="/admin/support?filter=unassigned" class="filter-link <?= $filter==='unassigned'?'active':'' ?>">
        <span><i class="fa-solid fa-circle-question"></i>Unassigned</span>
        <?php if($stats['unassigned']): ?><span class="filter-count"><?= $stats['unassigned'] ?></span><?php endif; ?>
      </a>
      <a href="/admin/support?filter=urgent" class="filter-link <?= $filter==='urgent'?'active':'' ?>">
        <span><i class="fa-solid fa-fire"></i>Urgent</span>
        <?php if($stats['urgent']): ?><span class="filter-count urgent"><?= $stats['urgent'] ?></span><?php endif; ?>
      </a>
      <a href="/admin/support?filter=resolved" class="filter-link <?= $filter==='resolved'?'active':'' ?>">
        <span><i class="fa-solid fa-circle-check"></i>Resolved</span>
        <?php if($stats['resolved_today']): ?><span class="filter-count"><?= $stats['resolved_today'] ?> today</span><?php endif; ?>
      </a>
    </nav>
  </div>

  <!-- TICKET LIST -->
  <div class="ticket-list-col">
    <div class="ticket-list-header">
      <form class="ticket-search" method="GET" action="/admin/support">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="search" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search tickets..." autocomplete="off">
      </form>
    </div>

    <div class="ticket-list-body" id="ticketListBody">
      <?php if (empty($tickets)): ?>
        <div class="empty-list">
          <i class="fa-solid fa-ticket"></i>
          <p>No tickets found</p>
        </div>
      <?php else: ?>
        <?php foreach ($tickets as $t): ?>
          <?php
            $pColor = $priorityColor[$t['priority']] ?? '#9ca3af';
            $pBg    = $priorityBg[$t['priority']] ?? '#f3f4f6';
            $sColor = $statusColor[$t['status']] ?? '#9ca3af';
            $sBg    = $statusBg[$t['status']] ?? '#f3f4f6';
            $icon   = $channelIcon[$t['channel']] ?? 'fa-circle';
            $ctColor = match($t['contact_type']) { 'buyer'=>'#3b82f6','seller'=>'#8b5cf6','driver'=>'#f59e0b','supplier'=>'#10b981','lead'=>'#6b7280', default=>'#9ca3af' };
          ?>
          <div class="ticket-item" onclick="loadTicket(<?= $t['id'] ?>)" id="ticket-item-<?= $t['id'] ?>">
            <div class="ticket-item-top">
              <span class="ticket-num"><i class="fa-solid <?= $icon ?>" style="margin-right:3px;"></i><?= htmlspecialchars($t['ticket_number']) ?></span>
              <span class="ticket-age"><?= ticketAge($t['last_reply_at'] ?: $t['created_at']) ?></span>
            </div>
            <div class="ticket-subject"><?= htmlspecialchars($t['subject']) ?></div>
            <?php if ($t['contact_name']): ?>
              <div class="ticket-contact"><?= htmlspecialchars($t['contact_name']) ?><?= $t['contact_email'] ? ' · ' . htmlspecialchars($t['contact_email']) : '' ?></div>
            <?php endif; ?>
            <div class="ticket-meta">
              <span class="t-badge" style="background:<?= $sBg ?>;color:<?= $sColor ?>;"><?= str_replace('_',' ',$t['status']) ?></span>
              <span class="t-badge" style="background:<?= $pBg ?>;color:<?= $pColor ?>;"><?= $t['priority'] ?></span>
              <?php if ($t['contact_type'] !== 'unknown'): ?>
                <span class="t-badge" style="background:<?= $ctColor ?>18;color:<?= $ctColor ?>;"><?= $t['contact_type'] ?></span>
              <?php endif; ?>
              <?php if ($t['reply_count'] > 0): ?>
                <span style="font-size:10px;color:#9ca3af;"><i class="fa-solid fa-message" style="margin-right:2px;"></i><?= $t['reply_count'] ?></span>
              <?php endif; ?>
            </div>
            <?php if ($t['agent_first']): ?>
              <div class="ticket-agent"><i class="fa-solid fa-user-gear" style="margin-right:3px;"></i><?= htmlspecialchars($t['agent_first'] . ' ' . $t['agent_last']) ?></div>
            <?php else: ?>
              <div class="ticket-agent" style="color:#f59e0b;"><i class="fa-solid fa-circle-question" style="margin-right:3px;"></i>Unassigned</div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>

        <?php if ($total > $perPage): ?>
          <div style="padding:12px;text-align:center;">
            <?php if ($page > 1): ?><a href="?filter=<?= urlencode($filter) ?>&page=<?= $page-1 ?>&q=<?= urlencode($search) ?>" style="margin-right:8px;font-size:12px;color:#00b207;">← Prev</a><?php endif; ?>
            <span style="font-size:11px;color:#9ca3af;"><?= $offset+1 ?>–<?= min($offset+$perPage,$total) ?> of <?= $total ?></span>
            <?php if ($offset+$perPage < $total): ?><a href="?filter=<?= urlencode($filter) ?>&page=<?= $page+1 ?>&q=<?= urlencode($search) ?>" style="margin-left:8px;font-size:12px;color:#00b207;">Next →</a><?php endif; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- TICKET DETAIL (AJAX panel) -->
  <div class="ticket-detail-col" id="ticketDetailCol">
    <div class="detail-placeholder" id="detailPlaceholder">
      <i class="fa-regular fa-envelope-open"></i>
      <p>Select a ticket to view the conversation</p>
    </div>
    <div id="ticketDetailPanel" style="display:none;flex:1;display:none;flex-direction:column;overflow:hidden;"></div>
  </div>

</div>

<script>
let currentTicketId = null;

function loadTicket(id) {
  // Highlight selected
  document.querySelectorAll('.ticket-item').forEach(el => el.classList.remove('active'));
  const item = document.getElementById('ticket-item-' + id);
  if (item) item.classList.add('active');

  currentTicketId = id;

  const panel = document.getElementById('ticketDetailPanel');
  const placeholder = document.getElementById('detailPlaceholder');
  placeholder.style.display = 'none';
  panel.style.display = 'flex';
  panel.innerHTML = '<div style="flex:1;display:flex;align-items:center;justify-content:center;color:#d1d5db;"><i class="fa-solid fa-spinner fa-spin fa-2x"></i></div>';

  fetch('/admin/api/support/ticket?id=' + id)
    .then(r => r.json())
    .then(data => {
      if (data.html) {
        panel.innerHTML = data.html;
        scrollThread();
        initReplyToggle();
      }
    })
    .catch(() => {
      panel.innerHTML = '<div style="padding:40px;text-align:center;color:#ef4444;">Failed to load ticket.</div>';
    });
}

function scrollThread() {
  const thread = document.getElementById('threadBody');
  if (thread) thread.scrollTop = thread.scrollHeight;
}

function initReplyToggle() {
  const btns = document.querySelectorAll('.reply-toggle-btn');
  const textarea = document.getElementById('replyTextarea');
  const hiddenInput = document.getElementById('isInternalInput');
  const sendBtn = document.getElementById('sendBtn');

  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      btns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const isInternal = btn.dataset.mode === 'internal';
      hiddenInput.value = isInternal ? '1' : '0';
      if (isInternal) {
        textarea.placeholder = 'Internal note — only visible to agents...';
        textarea.style.background = '#fffbeb';
        sendBtn.classList.add('internal-mode');
        sendBtn.textContent = 'Save Note';
      } else {
        textarea.placeholder = 'Reply to contact...';
        textarea.style.background = '';
        sendBtn.classList.remove('internal-mode');
        sendBtn.textContent = 'Send Reply';
      }
    });
  });
}

// Quick status update from detail panel
function quickStatus(ticketId, status) {
  fetch('/admin/support/update-status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ ticket_id: ticketId, status, _csrf_token: document.querySelector('meta[name="csrf-token"]')?.content || '' })
  }).then(() => loadTicket(ticketId));
}

// Quick assign from detail panel
function quickAssign(ticketId, agentId) {
  fetch('/admin/support/assign', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ ticket_id: ticketId, assigned_to: agentId, _csrf_token: document.querySelector('meta[name="csrf-token"]')?.content || '' })
  }).then(() => loadTicket(ticketId));
}

// Auto-load first ticket if list not empty
document.addEventListener('DOMContentLoaded', () => {
  const first = document.querySelector('.ticket-item');
  if (first) {
    const id = first.id.replace('ticket-item-','');
    loadTicket(parseInt(id));
  }
});
</script>
