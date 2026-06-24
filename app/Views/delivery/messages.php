<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');

$t = [
    'en' => [
        'page_title'    => 'Messages',
        'subtitle'      => 'Direct communication with the OCSAPP team',
        'team_name'     => 'OCSAPP Delivery Team',
        'response_time' => 'We typically respond within 1 business day',
        'no_messages'   => 'No messages yet',
        'no_msg_desc'   => 'When the OCSAPP team sends you a message, it will appear here.',
        'you'           => 'You',
        'placeholder'   => 'Type a reply…',
        'send'          => 'Send',
        'sending'       => 'Sending…',
        'no_app'        => 'No application on file. Messages will be available once your driver application has been submitted.',
    ],
    'fr' => [
        'page_title'    => 'Messages',
        'subtitle'      => 'Communication directe avec l\'équipe OCSAPP',
        'team_name'     => 'Équipe de livraison OCSAPP',
        'response_time' => 'Nous répondons généralement dans un délai d\'un jour ouvrable.',
        'no_messages'   => 'Aucun message pour l\'instant',
        'no_msg_desc'   => 'Lorsque l\'équipe OCSAPP vous envoie un message, il apparaîtra ici.',
        'you'           => 'Vous',
        'placeholder'   => 'Écrire une réponse…',
        'send'          => 'Envoyer',
        'sending'       => 'Envoi…',
        'no_app'        => 'Aucune candidature enregistrée. Les messages seront disponibles une fois votre candidature de livreur soumise.',
    ],
][$currentLang] ?? [];

$_msgT = $t;
require __DIR__ . '/layout-header.php';
$t = $_msgT; unset($_msgT);
?>

<style>
  .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 8px; }
  .page-title { font-size: 22px; font-weight: 700; color: var(--gray-700); display: flex; align-items: center; gap: 10px; }

  .chat-card {
    background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    display: flex; flex-direction: column; height: calc(100vh - 190px); min-height: 400px;
  }
  .chat-header {
    padding: 18px 24px; border-bottom: 1px solid var(--gray-100);
    display: flex; align-items: center; gap: 12px;
  }
  .chat-header-icon {
    width: 40px; height: 40px; border-radius: 50%; background: #d1fae5;
    display: flex; align-items: center; justify-content: center; color: #00b207; font-size: 16px; flex-shrink: 0;
  }
  .chat-header-info h3 { margin: 0; font-size: 15px; font-weight: 600; color: var(--gray-700); }
  .chat-header-info p  { margin: 0; font-size: 12px; color: var(--gray-400); }

  .chat-messages {
    flex: 1; overflow-y: auto; padding: 24px;
    display: flex; flex-direction: column; gap: 16px;
  }
  .msg-row { display: flex; align-items: flex-end; gap: 8px; }
  .msg-row.from-admin  { justify-content: flex-start; }
  .msg-row.from-driver { justify-content: flex-end; }

  .msg-avatar {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;
  }
  .msg-avatar.admin-avatar  { background: #e0e7ff; color: #4338ca; }
  .msg-avatar.driver-avatar { background: #d1fae5; color: #065f46; }

  .msg-bubble-wrap { max-width: 65%; display: flex; flex-direction: column; }
  .from-admin  .msg-bubble-wrap { align-items: flex-start; }
  .from-driver .msg-bubble-wrap { align-items: flex-end; }

  .msg-sender { font-size: 11px; color: var(--gray-400); margin-bottom: 4px; font-weight: 500; }
  .msg-bubble {
    padding: 12px 16px; border-radius: 16px; font-size: 14px; line-height: 1.55;
    word-break: break-word; white-space: pre-wrap;
  }
  .from-admin  .msg-bubble { background: #f3f4f6; color: #1f2937; border-bottom-left-radius: 4px; }
  .from-driver .msg-bubble { background: #00b207; color: #fff;    border-bottom-right-radius: 4px; }
  .msg-time { font-size: 10px; color: var(--gray-400); margin-top: 4px; }

  .chat-input-area {
    padding: 16px 24px; border-top: 1px solid var(--gray-100);
    display: flex; gap: 12px; align-items: flex-end;
  }
  .chat-input-area textarea {
    flex: 1; border: 1px solid var(--gray-200); border-radius: 10px;
    padding: 10px 14px; font-size: 14px; resize: none; line-height: 1.5;
    font-family: inherit; color: var(--gray-700); outline: none;
    transition: border-color 0.2s; min-height: 44px; max-height: 120px;
  }
  .chat-input-area textarea:focus { border-color: #00b207; }
  .send-btn {
    padding: 10px 20px; background: #00b207; color: white; border: none;
    border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 6px; white-space: nowrap;
    transition: background 0.2s; flex-shrink: 0; height: 44px;
  }
  .send-btn:hover { background: #008505; }
  .send-btn:disabled { background: var(--gray-300); cursor: not-allowed; }

  .empty-state {
    text-align: center; padding: 60px 20px; color: var(--gray-400);
    flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
  }
  .empty-state i { font-size: 48px; margin-bottom: 16px; color: var(--gray-200); }
  .empty-state h3 { margin: 0 0 8px; font-size: 16px; font-weight: 600; color: var(--gray-500); }
  .empty-state p  { margin: 0; font-size: 14px; }

  @media (max-width: 768px) {
    .chat-card { height: calc(100vh - 150px); min-height: 300px; }
    .msg-bubble-wrap { max-width: 85%; }
    .page-title { font-size: 18px; }
    .chat-input-area { padding: 10px 12px; gap: 8px; }
  }
</style>

<div class="page-header">
  <div class="page-title">
    <i class="fas fa-comments" style="color:#00b207;"></i>
    <?= $t['page_title'] ?>
  </div>
  <p style="margin:0;font-size:13px;color:var(--gray-400);"><?= $t['subtitle'] ?></p>
</div>

<?php if (!$appId): ?>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:24px;text-align:center;color:var(--gray-500);font-size:14px;">
    <i class="fas fa-comment-slash" style="font-size:32px;color:var(--gray-200);display:block;margin-bottom:12px;"></i>
    <?= $t['no_app'] ?>
  </div>
<?php else: ?>
<?php
$driverInitials = strtoupper(substr($_SESSION['user']['first_name'] ?? 'D', 0, 1) . substr($_SESSION['user']['last_name'] ?? '', 0, 1));
?>
<div class="chat-card">
  <div class="chat-header">
    <div class="chat-header-icon"><i class="fas fa-headset"></i></div>
    <div class="chat-header-info">
      <h3><?= $t['team_name'] ?></h3>
      <p><?= $t['response_time'] ?></p>
    </div>
  </div>

  <div class="chat-messages" id="chatMessages">
    <?php if (empty($messages)): ?>
      <div class="empty-state">
        <i class="fas fa-comments"></i>
        <h3><?= $t['no_messages'] ?></h3>
        <p><?= $t['no_msg_desc'] ?></p>
      </div>
    <?php else: ?>
      <?php foreach ($messages as $msg): ?>
        <?php
          $isAdmin  = $msg['sender_type'] === 'admin';
          $rowClass = $isAdmin ? 'from-admin' : 'from-driver';
          $label    = $isAdmin ? ($fr ? 'Équipe OCSAPP' : 'OCSAPP Team') : $t['you'];
          $text     = $fr && !empty($msg['message_fr']) ? $msg['message_fr'] : $msg['message'];
          $ts       = date('M j, g:i a', strtotime($msg['created_at']));
        ?>
        <div class="msg-row <?= $rowClass ?>">
          <?php if ($isAdmin): ?>
            <div class="msg-avatar admin-avatar">OC</div>
          <?php endif; ?>
          <div class="msg-bubble-wrap">
            <div class="msg-sender"><?= htmlspecialchars($label) ?></div>
            <div class="msg-bubble"><?= htmlspecialchars($text) ?></div>
            <div class="msg-time"><?= $ts ?></div>
          </div>
          <?php if (!$isAdmin): ?>
            <div class="msg-avatar driver-avatar"><?= htmlspecialchars($driverInitials) ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="chat-input-area">
    <textarea id="msgInput" placeholder="<?= htmlspecialchars($t['placeholder']) ?>"
              maxlength="2000" rows="1" onInput="autoResize(this)"></textarea>
    <button class="send-btn" id="sendBtn" onclick="sendMsg()">
      <i class="fas fa-paper-plane"></i> <?= $t['send'] ?>
    </button>
  </div>
</div>

<script>
const chatBox  = document.getElementById('chatMessages');
const msgInput = document.getElementById('msgInput');
const sendBtn  = document.getElementById('sendBtn');
if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;

function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

msgInput.addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) sendMsg();
});

async function sendMsg() {
  const msg = msgInput.value.trim();
  if (!msg) return;
  sendBtn.disabled = true;
  sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= $t['sending'] ?>';

  const fd = new FormData();
  fd.append('application_id', '<?= (int)$appId ?>');
  fd.append('message', msg);
  fd.append('_csrf_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

  try {
    const res  = await fetch('<?= url('delivery/send-application-message') ?>', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
      msgInput.value = '';
      msgInput.style.height = '';
      location.reload();
    } else {
      alert(data.error || 'Failed to send.');
      sendBtn.disabled = false;
      sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <?= $t['send'] ?>';
    }
  } catch(e) {
    alert('Network error.');
    sendBtn.disabled = false;
    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <?= $t['send'] ?>';
  }
}
</script>
<?php endif; ?>

<?php require __DIR__ . '/layout-footer.php'; ?>
