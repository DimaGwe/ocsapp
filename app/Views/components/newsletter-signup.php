<?php
/**
 * Shared newsletter signup band (bilingual, self-contained).
 *
 * Shows an email field plus per-list checkboxes (all visible up front) so the
 * visitor can pick which OCSAPP newsletters to receive, then a consent line and
 * the Subscribe button at the bottom. "OCSAPP Inc" (general) and the newsletter
 * matching the current portal are pre-checked.
 *
 * Included from components/footer.php so it appears on every public page.
 */

$nlFr = (($_SESSION['language'] ?? 'fr') === 'fr');

// Detect which portal this page belongs to, to pre-check the right list
$nlUri = $_SERVER['REQUEST_URI'] ?? '';
$nlPortal = 'buyer';
if (strpos($nlUri, '/seller') !== false)                                        { $nlPortal = 'seller'; }
elseif (strpos($nlUri, '/supplier') !== false)                                  { $nlPortal = 'supplier'; }
elseif (strpos($nlUri, '/distribution') !== false || strpos($nlUri, '/business') !== false) { $nlPortal = 'distribution'; }
elseif (strpos($nlUri, '/driver') !== false || strpos($nlUri, '/delivery') !== false)       { $nlPortal = 'driver'; }

// Load active newsletters
$nlLists = [];
try {
    $nlLists = \Database::getConnection()
        ->query("SELECT slug, name_en, name_fr FROM newsletter_lists WHERE is_active = 1 ORDER BY sort_order")
        ->fetchAll(\PDO::FETCH_ASSOC);
} catch (\Throwable $e) {
    $nlLists = []; // table not migrated yet — render nothing
}

if ($nlLists):
    $nlPrechecked = ['general', $nlPortal];
?>
<section class="nl-band">
  <div class="nl-inner">
    <div class="nl-head">
      <div class="nl-icon" aria-hidden="true">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
      </div>
      <div>
        <h3><?= $nlFr ? 'Restez informé' : 'Stay in the loop' ?></h3>
        <p><?= $nlFr ? 'Recevez les nouvelles et offres d\'OCSAPP. Choisissez ce qui vous intéresse.' : 'Get OCSAPP news and offers. Choose what matters to you.' ?></p>
      </div>
    </div>

    <form class="nl-form" id="nlForm" novalidate>
      <div class="nl-field">
        <input type="email" id="nlEmail" placeholder="<?= $nlFr ? 'Votre adresse courriel' : 'Your email address' ?>" required>
      </div>

      <div class="nl-options">
        <p class="nl-options-label"><?= $nlFr ? 'Je veux recevoir :' : 'I want to receive:' ?></p>
        <div class="nl-checks">
          <?php foreach ($nlLists as $list): ?>
          <label class="nl-check">
            <input type="checkbox" name="lists" value="<?= htmlspecialchars($list['slug']) ?>" <?= in_array($list['slug'], $nlPrechecked, true) ? 'checked' : '' ?>>
            <span><?= htmlspecialchars($nlFr ? $list['name_fr'] : $list['name_en']) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
        <label class="nl-consent">
          <input type="checkbox" id="nlConsent">
          <span><?= $nlFr
            ? 'J\'accepte de recevoir des courriels promotionnels d\'OCSAPP. Je peux me désabonner en tout temps.'
            : 'I agree to receive promotional emails from OCSAPP. I can unsubscribe at any time.' ?></span>
        </label>
      </div>

      <button type="submit" class="nl-btn"><?= $nlFr ? 'S\'abonner' : 'Subscribe' ?></button>

      <div class="nl-msg" id="nlMsg"></div>
    </form>
  </div>
</section>

<style>
  .nl-band { background: #0b3d2e; color: #fff; padding: 36px 30px; margin: 0 clamp(16px, 4vw, 48px); border-radius: 16px; }
  .nl-inner { max-width: 1100px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: flex-start; gap: 24px; justify-content: space-between; }
  .nl-head { display: flex; gap: 14px; align-items: flex-start; max-width: 460px; }
  .nl-icon { background: rgba(255,255,255,.12); border-radius: 12px; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .nl-head h3 { margin: 0 0 4px; font-size: 20px; font-weight: 700; }
  .nl-head p { margin: 0; font-size: 14px; opacity: .85; line-height: 1.5; }
  .nl-form { flex: 1; min-width: 300px; max-width: 520px; }
  .nl-field input { width: 100%; box-sizing: border-box; padding: 13px 15px; border: none; border-radius: 9px; font-size: 14px; font-family: inherit; }
  .nl-options { margin-top: 16px; }
  .nl-options-label { margin: 0 0 10px; font-size: 13px; font-weight: 600; opacity: .9; }
  .nl-checks { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px 18px; margin-bottom: 14px; }
  .nl-check, .nl-consent { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; cursor: pointer; line-height: 1.4; }
  .nl-check input, .nl-consent input { margin-top: 2px; width: 16px; height: 16px; accent-color: #00b207; flex-shrink: 0; }
  .nl-consent { opacity: .9; }
  .nl-btn { width: 100%; margin-top: 16px; padding: 13px 24px; background: #00b207; color: #fff; border: none; border-radius: 9px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background .2s; }
  .nl-btn:hover { background: #00d108; }
  .nl-msg { margin-top: 12px; font-size: 13.5px; min-height: 1px; }
  .nl-msg.ok { color: #7CFC9B; }
  .nl-msg.err { color: #ffb4b4; }
  @media (max-width: 640px) { .nl-checks { grid-template-columns: 1fr; } }
</style>

<script>
(function() {
  var form = document.getElementById('nlForm');
  if (!form) return;
  var email = document.getElementById('nlEmail');
  var consent = document.getElementById('nlConsent');
  var msg = document.getElementById('nlMsg');
  var ENDPOINT = <?= json_encode(url('api/newsletter/subscribe')) ?>;
  var L = {
    consent: <?= json_encode($nlFr ? 'Veuillez cocher la case de consentement pour vous abonner.' : 'Please check the consent box to subscribe.') ?>,
    pick:    <?= json_encode($nlFr ? 'Veuillez choisir au moins une infolettre.' : 'Please choose at least one newsletter.') ?>,
    email:   <?= json_encode($nlFr ? 'Veuillez entrer une adresse courriel valide.' : 'Please enter a valid email address.') ?>,
    err:     <?= json_encode($nlFr ? 'Une erreur est survenue. Réessayez plus tard.' : 'Something went wrong. Please try again later.') ?>
  };

  function setMsg(text, ok) { msg.textContent = text; msg.className = 'nl-msg ' + (ok ? 'ok' : 'err'); }

  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    var addr = (email.value || '').trim();
    if (!addr || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(addr)) { setMsg(L.email, false); return; }

    var lists = [].slice.call(form.querySelectorAll('input[name="lists"]:checked')).map(function(c) { return c.value; });
    if (!lists.length) { setMsg(L.pick, false); return; }
    if (!consent.checked) { setMsg(L.consent, false); return; }

    try {
      var res = await fetch(ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: addr, consent: true, lists: lists })
      });
      var json = await res.json();
      setMsg(json.message || (json.success ? 'OK' : L.err), !!json.success);
      if (json.success) { form.reset(); }
    } catch (err) {
      setMsg(L.err, false);
    }
  });
})();
</script>
<?php endif; ?>
