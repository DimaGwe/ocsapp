<?php
$priorityColor = ['urgent'=>'#ef4444','high'=>'#f59e0b','medium'=>'#3b82f6','low'=>'#9ca3af'];
$priorityBg    = ['urgent'=>'#fee2e2','high'=>'#fef3c7','medium'=>'#dbeafe','low'=>'#f3f4f6'];
$statusColor   = ['open'=>'#3b82f6','in_progress'=>'#8b5cf6','pending_contact'=>'#f59e0b','resolved'=>'#10b981','closed'=>'#9ca3af'];
$statusBg      = ['open'=>'#dbeafe','in_progress'=>'#ede9fe','pending_contact'=>'#fef3c7','resolved'=>'#d1fae5','closed'=>'#f3f4f6'];
$ctColors      = ['buyer'=>'#3b82f6','seller'=>'#8b5cf6','driver'=>'#f59e0b','supplier'=>'#10b981','lead'=>'#6b7280'];

$pColor  = $priorityColor[$ticket['priority']] ?? '#9ca3af';
$pBg     = $priorityBg[$ticket['priority']]    ?? '#f3f4f6';
$sColor  = $statusColor[$ticket['status']]     ?? '#9ca3af';
$sBg     = $statusBg[$ticket['status']]        ?? '#f3f4f6';
$ctColor = $ctColors[$ticket['contact_type']]  ?? '#9ca3af';
$initials = $ticket['contact_name']
    ? strtoupper(implode('', array_slice(array_map(fn($w)=>$w[0], explode(' ', $ticket['contact_name'])), 0, 2)))
    : '?';

$csrfName = env('CSRF_TOKEN_NAME', '_csrf_token');
$csrfVal  = generateCsrfToken();
?>
<style>
.sv-layout  { display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; }
.sv-main    { display:flex; flex-direction:column; gap:16px; }
.sv-sidebar { display:flex; flex-direction:column; gap:14px; }
.sv-card    { background:white; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden; }
.sv-card-hdr{ padding:13px 18px; border-bottom:1px solid #f3f4f6; font-size:12px; font-weight:700; color:#374151; display:flex; align-items:center; gap:7px; }
.sv-card-body{ padding:16px 18px; }

/* Thread */
.sv-thread  { display:flex; flex-direction:column; gap:12px; padding:16px 18px; max-height:600px; overflow-y:auto; }
.msg-bubble.contact  { align-self:flex-start; max-width:72%; }
.msg-bubble.agent    { align-self:flex-end; max-width:72%; }
.msg-bubble.system   { align-self:center; }
.msg-bubble.internal { align-self:flex-end; max-width:72%; }
.msg-inner  { padding:11px 14px; border-radius:12px; font-size:13px; line-height:1.55; }
.msg-bubble.contact  .msg-inner  { background:#f3f4f6; color:#111827; border-bottom-left-radius:3px; }
.msg-bubble.agent    .msg-inner  { background:#00b207; color:white; border-bottom-right-radius:3px; }
.msg-bubble.system   .msg-inner  { background:#f9fafb; color:#9ca3af; font-size:11px; padding:5px 12px; border-radius:20px; border:1px solid #e5e7eb; }
.msg-bubble.internal .msg-inner  { background:#fffbeb; color:#92400e; border:1px dashed #fcd34d; border-bottom-right-radius:3px; }
.msg-meta   { font-size:10px; color:#9ca3af; margin-top:4px; padding:0 2px; }
.msg-bubble.agent    .msg-meta   { text-align:right; }
.internal-badge { font-size:9px; font-weight:700; background:#fcd34d; color:#92400e; padding:1px 6px; border-radius:10px; margin-left:6px; vertical-align:middle; }

/* Reply box */
.reply-toggle     { display:flex; border-bottom:1px solid #f3f4f6; }
.reply-toggle-btn { padding:10px 18px; font-size:12px; font-weight:600; background:none; border:none; cursor:pointer; color:#9ca3af; border-bottom:2px solid transparent; margin-bottom:-1px; }
.reply-toggle-btn.active   { color:#00b207; border-bottom-color:#00b207; }
.reply-toggle-btn.internal.active { color:#f59e0b; border-bottom-color:#f59e0b; }
.reply-textarea   { width:100%; min-height:100px; padding:12px 16px; border:none; font-size:13px; font-family:inherit; resize:vertical; outline:none; }
.reply-textarea.internal-mode { background:#fffbeb; }
.reply-footer     { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; border-top:1px solid #f3f4f6; }
.send-btn         { padding:8px 20px; background:#00b207; color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
.send-btn.internal-mode { background:#f59e0b; }

/* Controls */
.sv-control-row { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.sv-control-select { padding:7px 10px; border:1px solid #e5e7eb; border-radius:8px; font-size:12px; background:white; cursor:pointer; }
.sv-ctrl-btn { padding:7px 14px; border:none; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; }

@media(max-width:900px){ .sv-layout { grid-template-columns:1fr; } }
</style>

<!-- Breadcrumb -->
<div style="display:flex;align-items:center;gap:8px;margin-bottom:18px;font-size:12px;color:#9ca3af;">
  <a href="/admin/support" style="color:#9ca3af;text-decoration:none;"><i class="fa-solid fa-inbox"></i> Support Inbox</a>
  <span>/</span>
  <span style="color:#374151;font-weight:600;"><?= htmlspecialchars($ticket['ticket_number']) ?></span>
</div>

<!-- Subject + badges -->
<div style="margin-bottom:18px;">
  <h1 style="font-size:18px;font-weight:700;color:#111827;margin:0 0 8px;"><?= htmlspecialchars($ticket['subject']) ?></h1>
  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
    <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:<?= $sBg ?>;color:<?= $sColor ?>;"><?= str_replace('_',' ',$ticket['status']) ?></span>
    <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:<?= $pBg ?>;color:<?= $pColor ?>;"><?= $ticket['priority'] ?></span>
    <span style="font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;background:#f3f4f6;color:#6b7280;"><?= ucfirst(str_replace('_',' ',$ticket['channel'])) ?></span>
    <span style="font-size:11px;color:#9ca3af;">Opened <?= date('M j, Y g:ia', strtotime($ticket['created_at'])) ?></span>
  </div>
</div>

<div class="sv-layout">

  <!-- Main column -->
  <div class="sv-main">

    <!-- Controls bar -->
    <div class="sv-card">
      <div class="sv-card-body sv-control-row">
        <select class="sv-control-select" onchange="svQuickStatus(<?= $ticket['id'] ?>, this.value)">
          <?php foreach (['open','in_progress','pending_contact','resolved','closed'] as $s): ?>
            <option value="<?= $s ?>" <?= $ticket['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
          <?php endforeach; ?>
        </select>

        <select class="sv-control-select" onchange="svQuickPriority(<?= $ticket['id'] ?>, this.value)">
          <?php foreach (['low','medium','high','urgent'] as $p): ?>
            <option value="<?= $p ?>" <?= $ticket['priority']===$p?'selected':'' ?>><?= ucfirst($p) ?></option>
          <?php endforeach; ?>
        </select>

        <select class="sv-control-select" onchange="svQuickAssign(<?= $ticket['id'] ?>, this.value)">
          <option value="">— Unassigned —</option>
          <?php foreach ($agents as $ag): ?>
            <option value="<?= $ag['id'] ?>" <?= $ticket['assigned_to']==$ag['id']?'selected':'' ?>>
              <?= htmlspecialchars($ag['first_name'] . ' ' . $ag['last_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <div style="margin-left:auto;display:flex;gap:6px;">
          <?php if (!in_array($ticket['status'], ['resolved','closed'])): ?>
            <button class="sv-ctrl-btn" onclick="svQuickStatus(<?= $ticket['id'] ?>,'resolved')" style="background:#d1fae5;color:#065f46;">
              <i class="fa-solid fa-check"></i> Resolve
            </button>
          <?php else: ?>
            <button class="sv-ctrl-btn" onclick="svQuickStatus(<?= $ticket['id'] ?>,'open')" style="background:#dbeafe;color:#1e40af;">
              <i class="fa-solid fa-rotate-left"></i> Reopen
            </button>
          <?php endif; ?>

          <form method="POST" action="/admin/support/delete" style="margin:0;" onsubmit="return confirm('Delete this ticket?')">
            <?= csrfField() ?>
            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
            <button type="submit" class="sv-ctrl-btn" style="background:#fee2e2;color:#991b1b;">
              <i class="fa-solid fa-trash"></i>
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Thread -->
    <div class="sv-card">
      <div class="sv-card-hdr"><i class="fa-solid fa-comments" style="color:#6b7280;"></i> Conversation</div>
      <div class="sv-thread" id="threadBody">
        <?php if ($ticket['description']): ?>
          <div class="msg-bubble contact">
            <div class="msg-inner"><?= nl2br(htmlspecialchars($ticket['description'])) ?></div>
            <div class="msg-meta"><?= htmlspecialchars($ticket['contact_name'] ?: 'Contact') ?> · <?= date('M j g:ia', strtotime($ticket['created_at'])) ?></div>
          </div>
        <?php endif; ?>

        <?php foreach ($messages as $msg):
          $isSystem   = $msg['sender_type'] === 'system';
          $isInternal = (int)$msg['is_internal'] === 1;
          $senderName = trim(($msg['first_name'] ?? '') . ' ' . ($msg['last_name'] ?? '')) ?: $msg['sender_name'] ?: 'Agent';
          $bubbleClass = $isSystem ? 'system' : ($msg['sender_type'] === 'contact' ? 'contact' : 'agent');
          if ($isInternal && !$isSystem) $bubbleClass .= ' internal';
        ?>
          <div class="msg-bubble <?= $bubbleClass ?>">
            <div class="msg-inner">
              <?= nl2br(htmlspecialchars($msg['message'])) ?>
              <?php if ($isInternal && !$isSystem): ?>
                <span class="internal-badge">NOTE</span>
              <?php endif; ?>
            </div>
            <?php if (!$isSystem): ?>
              <div class="msg-meta"><?= htmlspecialchars($senderName) ?> · <?= date('M j g:ia', strtotime($msg['created_at'])) ?></div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        <div id="thread-end"></div>
      </div>

      <!-- Reply box -->
      <?php if ($ticket['status'] !== 'closed'): ?>
      <div style="border-top:1px solid #f3f4f6;">
        <div class="reply-toggle">
          <button class="reply-toggle-btn active" data-mode="reply" onclick="svSetMode('reply')">Reply</button>
          <button class="reply-toggle-btn internal" data-mode="internal" onclick="svSetMode('internal')">Internal Note</button>
        </div>
        <form method="POST" action="/admin/support/reply">
          <input type="hidden" name="<?= $csrfName ?>" value="<?= $csrfVal ?>">
          <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
          <input type="hidden" name="is_internal" id="svIsInternal" value="0">
          <textarea class="reply-textarea" id="svReplyTextarea" name="message" placeholder="Reply to contact…" required></textarea>
          <div class="reply-footer">
            <span style="font-size:11px;color:#9ca3af;"><i class="fa-solid fa-lock" style="margin-right:3px;"></i>Internal notes are not visible to the contact</span>
            <button type="submit" class="send-btn" id="svSendBtn">Send Reply</button>
          </div>
        </form>
      </div>
      <?php else: ?>
      <div style="padding:12px 18px;text-align:center;font-size:12px;color:#9ca3af;">
        Ticket is closed. <button onclick="svQuickStatus(<?= $ticket['id'] ?>,'open')" style="color:#3b82f6;background:none;border:none;cursor:pointer;font-size:12px;font-weight:600;">Reopen to reply</button>
      </div>
      <?php endif; ?>
    </div>

  </div><!-- /sv-main -->

  <!-- Sidebar -->
  <div class="sv-sidebar">

    <!-- Contact -->
    <?php if ($ticket['contact_name'] || $ticket['contact_email'] || $ticket['contact_phone']): ?>
    <div class="sv-card">
      <div class="sv-card-hdr"><i class="fa-solid fa-user" style="color:#3b82f6;"></i> Contact</div>
      <div class="sv-card-body" style="display:flex;flex-direction:column;gap:10px;">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:38px;height:38px;border-radius:50%;background:<?= $ctColor ?>;color:white;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= htmlspecialchars($initials) ?></div>
          <div>
            <div style="font-size:13px;font-weight:700;color:#111827;"><?= htmlspecialchars($ticket['contact_name'] ?: 'Unknown') ?></div>
            <div style="font-size:11px;color:#9ca3af;text-transform:capitalize;"><?= $ticket['contact_type'] ?></div>
          </div>
        </div>
        <?php if ($ticket['contact_email']): ?>
          <div style="font-size:12px;color:#374151;">
            <i class="fa-solid fa-envelope" style="color:#9ca3af;width:14px;"></i>
            <a href="mailto:<?= htmlspecialchars($ticket['contact_email']) ?>" style="color:#374151;text-decoration:none;"><?= htmlspecialchars($ticket['contact_email']) ?></a>
          </div>
        <?php endif; ?>
        <?php if ($ticket['contact_phone']): ?>
          <div style="font-size:12px;color:#374151;">
            <i class="fa-solid fa-phone" style="color:#9ca3af;width:14px;"></i>
            <a href="tel:<?= htmlspecialchars($ticket['contact_phone']) ?>" style="color:#374151;text-decoration:none;"><?= htmlspecialchars($ticket['contact_phone']) ?></a>
          </div>
        <?php endif; ?>
        <?php if ($ticket['order_id']): ?>
          <div style="font-size:12px;color:#374151;">
            <i class="fa-solid fa-box" style="color:#9ca3af;width:14px;"></i>
            Order #<?= $ticket['order_id'] ?>
          </div>
        <?php endif; ?>
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px;">
          <?php if ($ticket['contact_phone']): ?>
            <a href="tel:<?= htmlspecialchars($ticket['contact_phone']) ?>" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:#f0fdf4;color:#00b207;border-radius:7px;font-size:11px;font-weight:600;text-decoration:none;border:1px solid #bbf7d0;"><i class="fa-solid fa-phone"></i> Call</a>
          <?php endif; ?>
          <?php if ($ticket['contact_email']): ?>
            <a href="mailto:<?= htmlspecialchars($ticket['contact_email']) ?>" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:#eff6ff;color:#3b82f6;border-radius:7px;font-size:11px;font-weight:600;text-decoration:none;border:1px solid #bfdbfe;"><i class="fa-solid fa-envelope"></i> Email</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Ticket details -->
    <div class="sv-card">
      <div class="sv-card-hdr"><i class="fa-solid fa-circle-info" style="color:#6b7280;"></i> Details</div>
      <div class="sv-card-body" style="display:flex;flex-direction:column;gap:9px;font-size:12px;color:#374151;">
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#9ca3af;">Ticket #</span>
          <span style="font-weight:600;"><?= htmlspecialchars($ticket['ticket_number']) ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#9ca3af;">Category</span>
          <span><?= ucfirst(str_replace('_',' ',$ticket['category'])) ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#9ca3af;">Assigned to</span>
          <span><?= $ticket['agent_first'] ? htmlspecialchars($ticket['agent_first'] . ' ' . $ticket['agent_last']) : '—' ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#9ca3af;">Created by</span>
          <span><?= $ticket['creator_first'] ? htmlspecialchars($ticket['creator_first'] . ' ' . $ticket['creator_last']) : '—' ?></span>
        </div>
        <?php if ($ticket['first_response_at']): ?>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#9ca3af;">First response</span>
          <span><?= date('M j g:ia', strtotime($ticket['first_response_at'])) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($ticket['resolved_at']): ?>
        <div style="display:flex;justify-content:space-between;">
          <span style="color:#9ca3af;">Resolved</span>
          <span><?= date('M j g:ia', strtotime($ticket['resolved_at'])) ?></span>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </div><!-- /sv-sidebar -->

</div><!-- /sv-layout -->

<script>
function svQuickStatus(id, status) {
  const csrfVal = document.querySelector('meta[name="csrf-token"]')?.content || '';
  fetch('/admin/support/update-status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ ticket_id: id, status, _csrf_token: csrfVal }),
  }).then(() => location.reload());
}

function svQuickPriority(id, priority) {
  const csrfVal = document.querySelector('meta[name="csrf-token"]')?.content || '';
  fetch('/admin/support/update-priority', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ ticket_id: id, priority, _csrf_token: csrfVal }),
  }).then(() => location.reload());
}

function svQuickAssign(id, agentId) {
  const csrfVal = document.querySelector('meta[name="csrf-token"]')?.content || '';
  fetch('/admin/support/assign', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ ticket_id: id, assigned_to: agentId, _csrf_token: csrfVal }),
  }).then(() => location.reload());
}

function svSetMode(mode) {
  const isInternal = mode === 'internal';
  document.getElementById('svIsInternal').value = isInternal ? '1' : '0';
  const ta  = document.getElementById('svReplyTextarea');
  const btn = document.getElementById('svSendBtn');
  ta.placeholder  = isInternal ? 'Internal note (not visible to contact)…' : 'Reply to contact…';
  ta.className    = 'reply-textarea' + (isInternal ? ' internal-mode' : '');
  btn.className   = 'send-btn' + (isInternal ? ' internal-mode' : '');
  btn.textContent = isInternal ? 'Add Note' : 'Send Reply';

  document.querySelectorAll('.reply-toggle-btn').forEach(b => {
    b.classList.toggle('active', b.dataset.mode === mode);
  });
}

// Scroll thread to bottom
document.getElementById('thread-end')?.scrollIntoView({ behavior: 'smooth' });
</script>
