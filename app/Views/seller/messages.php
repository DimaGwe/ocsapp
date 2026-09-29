<?php
/**
 * Seller messages with the OCSAPP team (/seller/messages, SellerMessagesController::index)
 * Updated 2026-09-28: seller-portal.css kit (chat styles moved there), bilingual EN/FR, fr-CA times.
 * Same form (POST seller/messages/send, field "message", max 2000).
 */
$messages = $messages ?? [];
$pageTitle = 'Messages';
$user = user();
require __DIR__ . '/layout-header.php';
$me = mb_strtoupper(mb_substr(html_entity_decode((string) ($user['first_name'] ?? 'V'), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 0, 1));
?>
<section class="sp-card sp-chat">
  <div class="sp-chat-head">
    <div class="sp-avatar"><i class="fa-solid fa-headset"></i></div>
    <div><h2><?= $fr ? "Équipe OCSAPP" : 'OCSAPP team' ?></h2>
      <div class="sp-dim"><?= $fr ? 'Nous répondons habituellement en un jour ouvrable.' : 'We usually reply within one business day.' ?></div></div>
  </div>
  <div class="sp-chat-list" id="chatMessages">
    <?php if (empty($messages)): ?>
      <div class="sp-empty" style="margin:auto"><i class="fa-solid fa-comments"></i>
        <p><?= $fr ? 'Aucun message pour le moment. Écrivez-nous ci-dessous, nous vous répondrons rapidement.' : "No messages yet. Write to us below and we'll get back to you shortly." ?></p></div>
    <?php else: foreach ($messages as $msg):
      $isAdmin = ($msg['sender_type'] ?? '') === 'admin';
      $adminName = trim(html_entity_decode(($msg['admin_first_name'] ?? '') . ' ' . ($msg['admin_last_name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
      $ts = strtotime($msg['created_at']); ?>
      <div class="sp-msg <?= $isAdmin ? 'them' : 'me' ?>">
        <div class="sp-avatar"><?= $isAdmin ? 'OC' : htmlspecialchars($me) ?></div>
        <div>
          <div class="sp-bubble"><?= htmlspecialchars(html_entity_decode((string) $msg['message'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></div>
          <div class="sp-msg-meta"><?= htmlspecialchars($isAdmin ? ($adminName !== '' ? $adminName : ($fr ? 'Équipe OCSAPP' : 'OCSAPP team')) : ($fr ? 'Vous' : 'You')) ?>
            · <?= acct_date($msg['created_at'], $fr) ?>, <?= $fr ? date('G', $ts) . ' h ' . date('i', $ts) : date('g:i A', $ts) ?></div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
  <form class="sp-chat-form" method="POST" action="<?= url('seller/messages/send') ?>" id="msgForm">
    <?= csrfField() ?>
    <textarea name="message" id="msgInput" rows="1" maxlength="2000" required placeholder="<?= $fr ? 'Écrivez votre message…' : 'Type your message…' ?>" aria-label="<?= $fr ? 'Message' : 'Message' ?>"></textarea>
    <button type="submit" class="sp-btn sp-btn-primary" id="sendBtn"><i class="fa-solid fa-paper-plane"></i> <?= $fr ? 'Envoyer' : 'Send' ?></button>
  </form>
</section>
<p class="sp-dim" style="margin-top:8px"><?= $fr ? 'Ctrl + Entrée pour envoyer.' : 'Ctrl + Enter to send.' ?></p>
<script>
(function () {
  var box = document.getElementById('chatMessages'), input = document.getElementById('msgInput'), form = document.getElementById('msgForm');
  if (box) box.scrollTop = box.scrollHeight;
  input.addEventListener('input', function () { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 140) + 'px'; });
  input.addEventListener('keydown', function (e) { if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) form.requestSubmit(); });
  form.addEventListener('submit', function () { document.getElementById('sendBtn').disabled = true; });
})();
</script>
<?php require __DIR__ . '/layout-footer.php'; ?>
