<?php
$priorityColor = ['urgent'=>'#ef4444','high'=>'#f59e0b','medium'=>'#3b82f6','low'=>'#9ca3af'];
$priorityBg    = ['urgent'=>'#fee2e2','high'=>'#fef3c7','medium'=>'#dbeafe','low'=>'#f3f4f6'];
$statusColor   = ['open'=>'#3b82f6','in_progress'=>'#8b5cf6','pending_contact'=>'#f59e0b','resolved'=>'#10b981','closed'=>'#9ca3af'];
$statusBg      = ['open'=>'#dbeafe','in_progress'=>'#ede9fe','pending_contact'=>'#fef3c7','resolved'=>'#d1fae5','closed'=>'#f3f4f6'];
$ctColors      = ['buyer'=>'#3b82f6','seller'=>'#8b5cf6','driver'=>'#f59e0b','supplier'=>'#10b981','lead'=>'#6b7280'];
$pColor = $priorityColor[$ticket['priority']] ?? '#9ca3af';
$pBg    = $priorityBg[$ticket['priority']]    ?? '#f3f4f6';
$sColor = $statusColor[$ticket['status']]     ?? '#9ca3af';
$sBg    = $statusBg[$ticket['status']]        ?? '#f3f4f6';
$ctColor = $ctColors[$ticket['contact_type']] ?? '#9ca3af';
$initials = $ticket['contact_name'] ? strtoupper(implode('', array_slice(array_map(fn($w)=>$w[0], explode(' ', $ticket['contact_name'])), 0, 2))) : '?';
$tid = $ticket['id'];
$csrfName = env('CSRF_TOKEN_NAME', '_csrf_token');
$csrfVal  = generateCsrfToken();
?>

<!-- Header -->
<div class="detail-header">
  <div class="detail-header-left">
    <div class="detail-ticket-num">
      <?= htmlspecialchars($ticket['ticket_number']) ?>
      &nbsp;·&nbsp; <?= ucfirst($ticket['category']) ?>
      &nbsp;·&nbsp; <i class="fa-solid fa-<?= ['phone'=>'phone','email'=>'envelope','web_form'=>'globe','walk_in'=>'person-walking','chat'=>'comments'][$ticket['channel']] ?? 'circle' ?>"></i> <?= ucfirst(str_replace('_',' ',$ticket['channel'])) ?>
    </div>
    <div class="detail-subject"><?= htmlspecialchars($ticket['subject']) ?></div>
    <div class="detail-badges">
      <span class="t-badge" style="background:<?= $sBg ?>;color:<?= $sColor ?>;"><?= str_replace('_',' ',$ticket['status']) ?></span>
      <span class="t-badge" style="background:<?= $pBg ?>;color:<?= $pColor ?>;"><?= $ticket['priority'] ?></span>
      <?php if ($ticket['contact_type'] !== 'unknown'): ?>
        <span class="t-badge" style="background:<?= $ctColor ?>18;color:<?= $ctColor ?>;"><?= $ticket['contact_type'] ?></span>
      <?php endif; ?>
      <span style="font-size:11px;color:#9ca3af;margin-left:4px;">Opened <?= date('M j, Y g:ia', strtotime($ticket['created_at'])) ?></span>
    </div>
  </div>
  <div class="detail-header-right">
    <a href="/admin/support/view?id=<?= $tid ?>" class="icon-btn" title="Open full page" target="_blank"><i class="fa-solid fa-expand"></i></a>
  </div>
</div>

<!-- Contact bar -->
<?php if ($ticket['contact_name'] || $ticket['contact_email'] || $ticket['contact_phone']): ?>
<div class="detail-contact-bar">
  <div class="contact-avatar" style="background:<?= $ctColor ?>;"><?= htmlspecialchars($initials) ?></div>
  <div class="contact-info">
    <div class="contact-name">
      <?= htmlspecialchars($ticket['contact_name'] ?: 'Unknown Contact') ?>
      <?php if ($ticket['contact_id'] && $ticket['contact_type'] !== 'unknown'): ?>
        <a href="/admin/<?= $ticket['contact_type'] === 'buyer' || $ticket['contact_type'] === 'seller' || $ticket['contact_type'] === 'driver' ? 'users' : $ticket['contact_type'] . 's' ?>/view?id=<?= $ticket['contact_id'] ?>" style="font-size:10px;color:#00b207;margin-left:6px;">view profile →</a>
      <?php endif; ?>
    </div>
    <div class="contact-sub">
      <?= htmlspecialchars($ticket['contact_email'] ?? '') ?>
      <?= $ticket['contact_phone'] ? ' · ' . htmlspecialchars($ticket['contact_phone']) : '' ?>
      <?= $ticket['order_id'] ? ' · Order #' . $ticket['order_id'] : '' ?>
    </div>
  </div>
  <div class="contact-actions">
    <?php if ($ticket['contact_phone']): ?>
      <a href="tel:<?= htmlspecialchars($ticket['contact_phone']) ?>" class="contact-action-btn" style="color:#00b207;border-color:#bbf7d0;background:#f0fdf4;"><i class="fa-solid fa-phone"></i> Call</a>
    <?php endif; ?>
    <?php if ($ticket['contact_email']): ?>
      <a href="mailto:<?= htmlspecialchars($ticket['contact_email']) ?>" class="contact-action-btn" style="color:#3b82f6;border-color:#bfdbfe;background:#eff6ff;"><i class="fa-solid fa-envelope"></i> Email</a>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- Controls bar -->
<div class="detail-controls">
  <!-- Status -->
  <select class="control-select" onchange="quickStatus(<?= $tid ?>, this.value)" title="Status">
    <?php foreach (['open','in_progress','pending_contact','resolved','closed'] as $s): ?>
      <option value="<?= $s ?>" <?= $ticket['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
    <?php endforeach; ?>
  </select>

  <!-- Priority -->
  <form method="POST" action="/admin/support/update-priority" style="margin:0;" onsubmit="return false;">
    <input type="hidden" name="ticket_id" value="<?= $tid ?>">
    <select class="control-select" name="priority" onchange="submitPriority(<?= $tid ?>, this.value)" title="Priority">
      <?php foreach (['low','medium','high','urgent'] as $p): ?>
        <option value="<?= $p ?>" <?= $ticket['priority']===$p?'selected':'' ?>><?= ucfirst($p) ?></option>
      <?php endforeach; ?>
    </select>
  </form>

  <!-- Assign -->
  <select class="control-select" onchange="quickAssign(<?= $tid ?>, this.value)" title="Assign to">
    <option value="">Unassigned</option>
    <?php foreach ($agents as $ag): ?>
      <option value="<?= $ag['id'] ?>" <?= $ticket['assigned_to']==$ag['id']?'selected':'' ?>>
        <?= htmlspecialchars($ag['first_name'] . ' ' . $ag['last_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <div style="margin-left:auto;display:flex;gap:6px;">
    <?php if ($ticket['status'] !== 'resolved' && $ticket['status'] !== 'closed'): ?>
      <button onclick="quickStatus(<?= $tid ?>,'resolved')" style="padding:5px 12px;background:#d1fae5;color:#065f46;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;">
        <i class="fa-solid fa-check"></i> Resolve
      </button>
    <?php else: ?>
      <button onclick="quickStatus(<?= $tid ?>,'open')" style="padding:5px 12px;background:#dbeafe;color:#1e40af;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;">
        <i class="fa-solid fa-rotate-left"></i> Reopen
      </button>
    <?php endif; ?>
  </div>
</div>

<!-- Message thread -->
<div class="thread-body" id="threadBody">
  <?php if ($ticket['description']): ?>
    <div class="msg-bubble contact">
      <div class="msg-inner"><?= nl2br(htmlspecialchars($ticket['description'])) ?></div>
      <div class="msg-meta"><?= htmlspecialchars($ticket['contact_name'] ?: 'Contact') ?> &middot; <?= date('M j g:ia', strtotime($ticket['created_at'])) ?></div>
    </div>
  <?php endif; ?>

  <?php foreach ($messages as $msg): ?>
    <?php
    $isSystem   = $msg['sender_type'] === 'system';
    $isInternal = (int)$msg['is_internal'] === 1;
    $senderName = trim(($msg['first_name'] ?? '') . ' ' . ($msg['last_name'] ?? '')) ?: $msg['sender_name'] ?: 'Agent';
    $bubbleClass = $isSystem ? 'system' : ($msg['sender_type'] === 'contact' ? 'contact' : 'agent');
    if ($isInternal) $bubbleClass .= ' internal';
    ?>
    <div class="msg-bubble <?= $bubbleClass ?>">
      <div class="msg-inner">
        <?= nl2br(htmlspecialchars($msg['message'])) ?>
        <?php if ($isInternal && !$isSystem): ?>
          <span class="internal-badge">NOTE</span>
        <?php endif; ?>
      </div>
      <?php if (!$isSystem): ?>
        <div class="msg-meta">
          <?= htmlspecialchars($senderName) ?> &middot; <?= date('M j g:ia', strtotime($msg['created_at'])) ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  <div id="thread-end"></div>
</div>

<!-- Reply box -->
<?php if (!in_array($ticket['status'], ['closed'])): ?>
<div class="reply-box">
  <div class="reply-toggle">
    <button class="reply-toggle-btn active" data-mode="reply">Reply</button>
    <button class="reply-toggle-btn internal" data-mode="internal">Internal Note</button>
  </div>
  <form method="POST" action="/admin/support/reply">
    <input type="hidden" name="<?= $csrfName ?>" value="<?= $csrfVal ?>">
    <input type="hidden" name="ticket_id" value="<?= $tid ?>">
    <input type="hidden" name="is_internal" id="isInternalInput" value="0">
    <textarea class="reply-textarea" id="replyTextarea" name="message" placeholder="Reply to contact..." required></textarea>
    <div class="reply-footer">
      <span style="font-size:11px;color:#9ca3af;"><i class="fa-solid fa-lock" style="margin-right:3px;"></i>Agent only: internal notes are not visible to contact</span>
      <button type="submit" class="send-btn" id="sendBtn">Send Reply</button>
    </div>
  </form>
</div>
<?php else: ?>
<div style="padding:12px 20px;background:white;border-top:1px solid #e5e7eb;text-align:center;font-size:12px;color:#9ca3af;">
  Ticket is closed. <button onclick="quickStatus(<?= $tid ?>,'open')" style="color:#3b82f6;background:none;border:none;cursor:pointer;font-size:12px;font-weight:600;">Reopen to reply</button>
</div>
<?php endif; ?>

<script>
function submitPriority(ticketId, priority) {
  fetch('/admin/support/update-priority', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ ticket_id: ticketId, priority, _csrf_token: document.querySelector('meta[name="csrf-token"]')?.content || '' })
  }).then(() => loadTicket(ticketId));
}
</script>
