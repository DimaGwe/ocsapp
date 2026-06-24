<?php
$currentLang = $currentLang ?? $_SESSION['language'] ?? 'fr';
$t = [
    'en' => [
        'page_title'        => 'Messages',
        'subtitle'          => 'Direct communication with OCS Team',
        'support_team'      => 'OCSAPP Support Team',
        'response_time'     => 'We typically respond within 1 business day',
        'no_messages'       => 'No messages yet',
        'no_messages_desc'  => "Send us a message below and we'll get back to you shortly.",
        'you'               => 'You',
        'placeholder'       => 'Type your message…',
        'send'              => 'Send',
    ],
    'fr' => [
        'page_title'        => 'Messages',
        'subtitle'          => 'Communication directe avec l\'équipe OCS',
        'support_team'      => 'Équipe de soutien OCSAPP',
        'response_time'     => 'Nous répondons généralement dans un délai d\'un jour ouvrable.',
        'no_messages'       => 'Aucun message pour l\'instant',
        'no_messages_desc'  => 'Envoyez-nous un message ci-dessous et nous vous répondrons sous peu.',
        'you'               => 'Vous',
        'placeholder'       => 'Écrivez votre message…',
        'send'              => 'Envoyer',
    ],
][$currentLang] ?? [];

$_msgT = $t;
require __DIR__ . '/layout-header.php';
$t = $_msgT; unset($_msgT);
?>

<style>
  .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
  .page-title { font-size: 22px; font-weight: 700; color: var(--gray-700); display: flex; align-items: center; gap: 10px; }

  .chat-card {
    background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    display: flex; flex-direction: column; height: calc(100vh - 180px); min-height: 400px;
  }
  @media (max-width: 768px) {
    .chat-card { height: calc(100vh - 140px); min-height: 300px; }
    .msg-bubble-wrap { max-width: 85%; }
    .page-header { flex-direction: column; align-items: flex-start; gap: 6px; }
    .page-title { font-size: 18px; }
    .chat-input-area { padding: 10px 12px; gap: 8px; }
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
  .chat-header-info p { margin: 0; font-size: 12px; color: var(--gray-400); }

  .chat-messages {
    flex: 1; overflow-y: auto; padding: 24px;
    display: flex; flex-direction: column; gap: 16px;
  }

  .msg-row { display: flex; align-items: flex-end; gap: 8px; }
  .msg-row.from-admin { justify-content: flex-start; }
  .msg-row.from-business { justify-content: flex-end; }

  .msg-avatar {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600;
  }
  .msg-avatar.admin-avatar { background: #e0e7ff; color: #4338ca; }
  .msg-avatar.business-avatar { background: #d1fae5; color: #065f46; }

  .msg-bubble-wrap { max-width: 65%; display: flex; flex-direction: column; }
  .from-admin .msg-bubble-wrap { align-items: flex-start; }
  .from-business .msg-bubble-wrap { align-items: flex-end; }

  .msg-sender { font-size: 11px; color: var(--gray-400); margin-bottom: 4px; font-weight: 500; }

  .msg-bubble {
    padding: 12px 16px; border-radius: 16px; font-size: 14px; line-height: 1.55;
    word-break: break-word; white-space: pre-wrap;
  }
  .from-admin .msg-bubble {
    background: #f3f4f6; color: #1f2937;
    border-bottom-left-radius: 4px;
  }
  .from-business .msg-bubble {
    background: #00b207; color: #ffffff;
    border-bottom-right-radius: 4px;
  }

  .msg-time { font-size: 10px; color: var(--gray-400); margin-top: 4px; }

  .chat-input-area {
    padding: 16px 24px; border-top: 1px solid var(--gray-100);
    display: flex; gap: 12px; align-items: flex-end;
  }
  .chat-input-area textarea {
    flex: 1; border: 1px solid var(--gray-200); border-radius: 10px;
    padding: 10px 14px; font-size: 14px; resize: none; line-height: 1.5;
    font-family: inherit; color: var(--gray-700); outline: none;
    transition: border-color 0.2s;
    min-height: 44px; max-height: 120px;
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

  .empty-state { text-align: center; padding: 60px 20px; color: var(--gray-400); flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; }
  .empty-state i { font-size: 48px; margin-bottom: 16px; color: var(--gray-200); }
  .empty-state h3 { margin: 0 0 8px; font-size: 16px; font-weight: 600; color: var(--gray-500); }
  .empty-state p { margin: 0; font-size: 14px; }

  .flash-msg { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 8px; }
  .flash-msg.success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
  .flash-msg.error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
</style>

<div class="page-header">
  <div class="page-title">
    <i class="fas fa-comments" style="color:#00b207;"></i>
    <?= $t['page_title'] ?>
  </div>
  <p style="margin:0;font-size:13px;color:var(--gray-400);"><?= $t['subtitle'] ?></p>
</div>

<?php if (!empty($flash)):
  $flashType = isset($flash['error']) ? 'error' : 'success';
  $flashMessage = $flash[$flashType] ?? '';
?>
  <div class="flash-msg <?= $flashType ?>">
    <i class="fas fa-<?= $flashType === 'error' ? 'exclamation-circle' : 'check-circle' ?>"></i>
    <?= htmlspecialchars($flashMessage) ?>
  </div>
<?php endif; ?>

<div class="chat-card">
  <!-- Header -->
  <div class="chat-header">
    <div class="chat-header-icon"><i class="fas fa-headset"></i></div>
    <div class="chat-header-info">
      <h3><?= $t['support_team'] ?></h3>
      <p><?= $t['response_time'] ?></p>
    </div>
  </div>

  <!-- Messages -->
  <div class="chat-messages" id="chatMessages">
    <?php if (empty($messages)): ?>
      <div class="empty-state">
        <i class="fas fa-comments"></i>
        <h3><?= $t['no_messages'] ?></h3>
        <p><?= $t['no_messages_desc'] ?></p>
      </div>
    <?php else: ?>
      <?php foreach ($messages as $msg): ?>
        <?php
          $isAdmin     = $msg['sender_type'] === 'admin';
          $adminName   = trim(($msg['admin_first_name'] ?? '') . ' ' . ($msg['admin_last_name'] ?? ''));
          $senderLabel = $isAdmin ? ($adminName ?: 'OCSAPP Team') : $t['you'];
          $rowClass    = $isAdmin ? 'from-admin' : 'from-business';
          $ts          = date('M j, g:i a', strtotime($msg['created_at']));
        ?>
        <div class="msg-row <?= $rowClass ?>">
          <?php if ($isAdmin): ?>
            <div class="msg-avatar admin-avatar">OC</div>
          <?php endif; ?>
          <div class="msg-bubble-wrap">
            <div class="msg-sender"><?= htmlspecialchars($senderLabel) ?></div>
            <div class="msg-bubble"><?= htmlspecialchars($msg['message']) ?></div>
            <div class="msg-time"><?= $ts ?></div>
          </div>
          <?php if (!$isAdmin): ?>
            <div class="msg-avatar business-avatar">
              <?= htmlspecialchars(strtoupper(substr($_SESSION['business']['company_name'] ?? 'B', 0, 1))) ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Send form -->
  <div class="chat-input-area">
    <form method="POST" action="<?= url('distribution/messages/send') ?>" id="msgForm" style="display:contents;">
      <?= csrfField() ?>
      <textarea
        name="message"
        id="msgInput"
        placeholder="<?= htmlspecialchars($t['placeholder']) ?>"
        maxlength="2000"
        rows="1"
        required
        onInput="autoResize(this)"
      ></textarea>
      <button type="submit" class="send-btn" id="sendBtn">
        <i class="fas fa-paper-plane"></i> <?= $t['send'] ?>
      </button>
    </form>
  </div>
</div>

<script>
  const chatBox = document.getElementById('chatMessages');
  if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;

  function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
  }

  document.getElementById('msgForm').addEventListener('submit', function() {
    document.getElementById('sendBtn').disabled = true;
  });

  document.getElementById('msgInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
      document.getElementById('msgForm').requestSubmit();
    }
  });
</script>

<?php require __DIR__ . '/layout-footer.php'; ?>
