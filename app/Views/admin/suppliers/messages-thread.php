<?php
/**
 * Reusable messages thread partial
 * Required variables: $supplierId (int), $threadMessages (array)
 * Used in: admin/suppliers/edit.php, admin/leads/view.php
 */
$threadId = 'msgThread_' . $supplierId; // unique ID if multiple partials on page
$formId   = 'adminMsgForm_' . $supplierId;
?>

<style>
.msg-thread-card { background: white; border-radius: 8px; border: 1px solid var(--border); margin-top: 24px; }
.msg-thread-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.msg-thread-header h3 { margin: 0; font-size: 15px; font-weight: 600; color: var(--dark); display: flex; align-items: center; gap: 8px; }

.msg-thread-body {
  height: 340px; overflow-y: auto; padding: 20px;
  display: flex; flex-direction: column; gap: 14px;
  background: #fafafa;
}

.at-row { display: flex; align-items: flex-end; gap: 8px; }
.at-row.from-admin { justify-content: flex-start; }
.at-row.from-supplier { justify-content: flex-end; }

.at-avatar {
  width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;
}
.at-avatar.av-admin { background: #dbeafe; color: #1d4ed8; }
.at-avatar.av-supplier { background: #d1fae5; color: #065f46; }

.at-wrap { max-width: 65%; display: flex; flex-direction: column; }
.from-admin .at-wrap { align-items: flex-start; }
.from-supplier .at-wrap { align-items: flex-end; }

.at-name { font-size: 10px; color: var(--gray-500); margin-bottom: 3px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }

.at-bubble {
  padding: 10px 14px; border-radius: 14px; font-size: 13px; line-height: 1.5;
  word-break: break-word; white-space: pre-wrap;
}
.from-admin .at-bubble { background: #e5e7eb; color: #1f2937; border-bottom-left-radius: 3px; }
.from-supplier .at-bubble { background: #00b207; color: #fff; border-bottom-right-radius: 3px; }

.at-time { font-size: 10px; color: var(--gray-400); margin-top: 3px; }

.at-empty { text-align: center; padding: 40px 20px; color: var(--gray-400); flex: 1; }
.at-empty i { font-size: 32px; margin-bottom: 10px; color: var(--gray-200); display: block; }

.msg-send-area { padding: 14px 20px; border-top: 1px solid var(--border); display: flex; gap: 10px; align-items: flex-end; }
.msg-send-area textarea {
  flex: 1; border: 1px solid var(--border); border-radius: 8px;
  padding: 9px 12px; font-size: 13px; resize: none; font-family: inherit;
  color: var(--dark); outline: none; transition: border-color 0.2s;
  min-height: 38px; max-height: 100px; line-height: 1.4;
}
.msg-send-area textarea:focus { border-color: var(--primary); }
.msg-send-btn {
  padding: 9px 18px; background: var(--primary); color: white; border: none;
  border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;
  display: flex; align-items: center; gap: 6px; height: 38px; flex-shrink: 0;
  transition: background 0.15s;
}
.msg-send-btn:hover { background: var(--primary-dark, #008505); }
.msg-send-btn:disabled { background: var(--gray-300); cursor: not-allowed; }

.at-sending-indicator { display: none; font-size: 12px; color: var(--gray-400); padding: 4px 20px; }
</style>

<div class="msg-thread-card">
  <div class="msg-thread-header">
    <h3><i class="fas fa-comments" style="color:#7c3aed;"></i> Messages</h3>
    <a href="<?= url('supplier/messages') ?>" target="_blank"
       style="font-size:12px;color:var(--primary);text-decoration:none;">
      <i class="fas fa-external-link-alt"></i> Supplier view
    </a>
  </div>

  <div class="msg-thread-body" id="<?= $threadId ?>">
    <?php if (empty($threadMessages)): ?>
      <div class="at-empty">
        <i class="fas fa-comments"></i>
        <p>No messages yet. Start the conversation below.</p>
      </div>
    <?php else: ?>
      <?php foreach ($threadMessages as $m): ?>
        <?php
          $isAdminMsg  = $m['sender_type'] === 'admin';
          $rowCls      = $isAdminMsg ? 'from-admin' : 'from-supplier';
          $adminName   = trim(($m['admin_first_name'] ?? '') . ' ' . ($m['admin_last_name'] ?? ''));
          $senderLabel = $isAdminMsg ? ($adminName ?: 'Admin') : 'Supplier';
          $ts          = date('M j, g:i a', strtotime($m['created_at']));
          $initials    = $isAdminMsg
            ? strtoupper(substr($adminName ?: 'A', 0, 1))
            : strtoupper(substr($supplierName ?? 'S', 0, 1));
        ?>
        <div class="at-row <?= $rowCls ?>">
          <?php if ($isAdminMsg): ?>
            <div class="at-avatar av-admin"><?= htmlspecialchars($initials) ?></div>
          <?php endif; ?>
          <div class="at-wrap">
            <div class="at-name"><?= htmlspecialchars($senderLabel) ?></div>
            <div class="at-bubble"><?= htmlspecialchars($m['message']) ?></div>
            <div class="at-time"><?= $ts ?></div>
          </div>
          <?php if (!$isAdminMsg): ?>
            <div class="at-avatar av-supplier"><?= htmlspecialchars($initials) ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="at-sending-indicator" id="sendingInd_<?= $supplierId ?>">
    <i class="fas fa-spinner fa-spin"></i> Sending…
  </div>

  <div class="msg-send-area">
    <textarea
      id="adminMsgInput_<?= $supplierId ?>"
      placeholder="Type a message to this supplier… (Ctrl+Enter to send)"
      maxlength="2000"
      rows="1"
      onInput="atResize(this)"
    ></textarea>
    <button class="msg-send-btn" id="adminSendBtn_<?= $supplierId ?>"
            onclick="sendAdminMessage(<?= $supplierId ?>)">
      <i class="fas fa-paper-plane"></i> Send
    </button>
  </div>
</div>

<script>
(function() {
  const SUPPLIER_ID   = <?= $supplierId ?>;
  const POLL_INTERVAL = 15000; // 15 seconds
  const THREAD_ID     = '<?= $threadId ?>';

  // Store last-message timestamp on the thread element (accessible globally)
  const box = document.getElementById(THREAD_ID);
  if (box) {
    box.dataset.lastTs = <?php
      $lastTs = '';
      if (!empty($threadMessages)) {
          $lastTs = end($threadMessages)['created_at'];
      }
      echo json_encode($lastTs);
    ?>;
    box.scrollTop = box.scrollHeight;
  }

  // Auto-resize textarea
  window.atResize = function(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
  };

  // Ctrl+Enter shortcut
  const inp = document.getElementById('adminMsgInput_' + SUPPLIER_ID);
  if (inp) {
    inp.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
        sendAdminMessage(SUPPLIER_ID);
      }
    });
  }

  // ── Polling for new incoming supplier messages ──
  function pollNewMessages() {
    const thread = document.getElementById(THREAD_ID);
    if (!thread) return;

    const lastTs = thread.dataset.lastTs || '';
    const url = '<?= url('api/admin/supplier-messages') ?>?supplier_id=' + SUPPLIER_ID
      + (lastTs ? '&after=' + encodeURIComponent(lastTs) : '');

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        if (data.success && data.messages && data.messages.length > 0) {
          data.messages.forEach(m => {
            if (m.sender_type === 'supplier') {
              // Remove empty state if present
              const empty = thread.querySelector('.at-empty');
              if (empty) empty.remove();

              const label   = 'Supplier';
              const initial = label.charAt(0).toUpperCase();
              const ts      = new Date(m.created_at).toLocaleString('en-CA', {month:'short', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true});
              const escaped = (m.message || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');

              thread.insertAdjacentHTML('beforeend', `<div class="at-row from-supplier">
                <div class="at-wrap">
                  <div class="at-name">${label}</div>
                  <div class="at-bubble">${escaped}</div>
                  <div class="at-time">${ts}</div>
                </div>
                <div class="at-avatar av-supplier">${initial}</div>
              </div>`);
              thread.scrollTop = thread.scrollHeight;
            }
            // Always advance the timestamp cursor
            thread.dataset.lastTs = m.created_at;
          });
        }
      })
      .catch(() => {}); // silently ignore poll errors
  }

  setInterval(pollNewMessages, POLL_INTERVAL);
})();

function sendAdminMessage(supplierId) {
  const inp    = document.getElementById('adminMsgInput_' + supplierId);
  const btn    = document.getElementById('adminSendBtn_' + supplierId);
  const ind    = document.getElementById('sendingInd_' + supplierId);
  const thread = document.getElementById('msgThread_' + supplierId);
  const msg    = (inp.value || '').trim();

  if (!msg) { inp.focus(); return; }

  btn.disabled = true;
  inp.disabled = true;
  if (ind) ind.style.display = 'block';

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  fetch('<?= url('admin/suppliers/messages/send') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
    },
    body: JSON.stringify({ supplier_id: supplierId, message: msg }),
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Remove empty state if present
      const empty = thread.querySelector('.at-empty');
      if (empty) empty.remove();

      // Append new bubble
      const adminName  = <?= json_encode(trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin') ?>;
      const initial    = adminName.charAt(0).toUpperCase();
      const now        = new Date();
      const ts         = now.toLocaleString('en-CA', {month:'short', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true});
      const escapedMsg = msg.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');

      const html = `<div class="at-row from-admin">
        <div class="at-avatar av-admin">${initial}</div>
        <div class="at-wrap">
          <div class="at-name">${adminName}</div>
          <div class="at-bubble">${escapedMsg}</div>
          <div class="at-time">${ts}</div>
        </div>
      </div>`;
      thread.insertAdjacentHTML('beforeend', html);
      thread.scrollTop = thread.scrollHeight;

      // Advance timestamp so the poller won't re-fetch this message
      if (data.created_at) thread.dataset.lastTs = data.created_at;

      inp.value = '';
      inp.style.height = 'auto';
    } else {
      alert(data.error || 'Failed to send message. Please try again.');
    }
  })
  .catch(() => alert('Network error. Please try again.'))
  .finally(() => {
    btn.disabled = false;
    inp.disabled = false;
    if (ind) ind.style.display = 'none';
    inp.focus();
  });
}
</script>
