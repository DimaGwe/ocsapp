<?php
/**
 * OCSAPP Admin - Integrations (API Keys)
 * File: app/Views/admin/settings/integrations.php
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$pageTitle = $fr ? 'Integrations' : 'Integrations';
$currentPage = 'integrations';
$defs   = $defs ?? [];
$stored = $stored ?? [];

ob_start();
?>
<style>
.int-header { margin-bottom: 22px; }
.int-header h1 { font-size: 22px; font-weight: 700; color:#111827; margin:0 0 4px; }
.int-header p { font-size: 13px; color:#6b7280; margin:0; }
.int-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(380px,1fr)); gap:18px; }
.int-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:22px; }
.int-card-head { display:flex; align-items:flex-start; gap:12px; margin-bottom:16px; }
.int-icon { width:42px; height:42px; border-radius:10px; background:#f0fdf4; color:#00b207; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.int-card-head h2 { font-size:15px; font-weight:700; color:#111827; margin:0 0 2px; }
.int-card-head p { font-size:12px; color:#6b7280; margin:0; }
.int-card-head a { font-size:11.5px; color:#00b207; text-decoration:none; }
.int-card-head a:hover { text-decoration:underline; }
.status-chip { font-size:11px; font-weight:700; padding:3px 9px; border-radius:20px; margin-left:auto; white-space:nowrap; }
.status-chip.set { background:#dcfce7; color:#15803d; }
.status-chip.unset { background:#f3f4f6; color:#9ca3af; }
.int-fg { margin-bottom:14px; }
.int-fg label { display:block; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:5px; }
.int-fg input { width:100%; padding:9px 11px; font-size:13px; border:1px solid #d1d5db; border-radius:8px; box-sizing:border-box; font-family:ui-monospace,monospace; }
.int-fg input:focus { outline:none; border-color:#00b207; box-shadow:0 0 0 3px rgba(0,178,7,.08); }
.int-actions { display:flex; gap:8px; align-items:center; }
.btn-test { background:#0a0e27; color:#fff; border:none; border-radius:8px; padding:9px 16px; font-size:13px; font-weight:600; cursor:pointer; }
.btn-test:hover { background:#1a1f3a; } .btn-test:disabled { background:#9ca3af; cursor:not-allowed; }
.test-result { font-size:12.5px; font-weight:600; }
.test-result.ok { color:#15803d; } .test-result.bad { color:#dc2626; }
.int-savebar { margin-top:22px; display:flex; gap:12px; align-items:center; }
.btn-save { background:#00b207; color:#fff; border:none; border-radius:8px; padding:11px 22px; font-size:14px; font-weight:600; cursor:pointer; }
.btn-save:hover { background:#009906; }
.int-note { font-size:12px; color:#9ca3af; }
.alert { padding:13px 18px; border-radius:9px; margin-bottom:18px; font-size:13.5px; font-weight:500; border-left:4px solid; }
.alert-success { background:#f0fdf4; border-color:#22c55e; color:#166534; }
.alert-error { background:#fef2f2; border-color:#ef4444; color:#991b1b; }
.spin { display:inline-block; width:13px; height:13px; border:2px solid rgba(255,255,255,.4); border-top-color:#fff; border-radius:50%; animation:isp .7s linear infinite; vertical-align:-2px; }
@keyframes isp { to { transform:rotate(360deg); } }
</style>

<div class="int-header">
  <h1><i class="fa-solid fa-plug" style="color:#00b207;margin-right:8px;"></i><?= $pageTitle ?></h1>
  <p><?= $fr ? 'Gerez les cles API des services tiers. Stockees de maniere securisee, masquees a l\'affichage.' : 'Manage third-party API keys. Stored securely, masked on display.' ?></p>
</div>

<?php if ($msg = getFlash('success')): ?><div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($msg = getFlash('error')): ?><div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<form method="POST" action="<?= url('admin/settings/integrations/save') ?>">
  <?= csrfField() ?>
  <div class="int-grid">
    <?php foreach ($defs as $pid => $group): ?>
      <div class="int-card">
        <div class="int-card-head">
          <div class="int-icon"><i class="fa-solid <?= $group['icon'] ?>"></i></div>
          <div>
            <h2><?= htmlspecialchars($group['title']) ?></h2>
            <p><?= htmlspecialchars($group['desc']) ?></p>
            <?php if (!empty($group['link'])): ?><a href="<?= $group['link'] ?>" target="_blank"><i class="fa-solid fa-up-right-from-square" style="font-size:10px;"></i> <?= $fr ? 'Obtenir une cle' : 'Get a key' ?></a><?php endif; ?>
          </div>
          <?php
            $anySet = false;
            foreach ($group['keys'] as $k) { if (!empty($stored[$k['key']]['is_set'])) { $anySet = true; break; } }
          ?>
          <span class="status-chip <?= $anySet ? 'set' : 'unset' ?>"><?= $anySet ? ($fr?'Configure':'Configured') : ($fr?'Non configure':'Not set') ?></span>
        </div>

        <?php foreach ($group['keys'] as $k):
          $info = $stored[$k['key']] ?? ['is_set'=>false,'masked'=>''];
          $ph = $info['is_set'] ? $info['masked'] : ($k['placeholder'] ?? '');
        ?>
        <div class="int-fg">
          <label><?= htmlspecialchars($k['label']) ?></label>
          <input type="text" name="<?= htmlspecialchars($k['key']) ?>" autocomplete="off"
                 placeholder="<?= htmlspecialchars($ph) ?>"
                 value="">
        </div>
        <?php endforeach; ?>

        <div class="int-actions">
          <button type="button" class="btn-test" data-provider="<?= htmlspecialchars($pid) ?>" onclick="testInt(this)"><i class="fa-solid fa-bolt"></i> <?= $fr ? 'Tester' : 'Test' ?></button>
          <span class="test-result" id="res-<?= htmlspecialchars($pid) ?>"></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="int-savebar">
    <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> <?= $fr ? 'Enregistrer' : 'Save keys' ?></button>
    <span class="int-note"><?= $fr ? 'Laissez un champ vide pour garder la cle actuelle. Saisissez une nouvelle valeur pour la remplacer.' : 'Leave a field blank to keep the current key. Enter a new value to replace it.' ?></span>
  </div>
</form>

<script>
const URL_TEST = '<?= url('admin/settings/integrations/test') ?>';
const FR = <?= $fr ? 'true':'false' ?>;
function testInt(btn){
  const provider = btn.dataset.provider;
  const res = document.getElementById('res-'+provider);
  btn.disabled = true; const orig = btn.innerHTML; btn.innerHTML = '<span class="spin"></span>';
  res.textContent = ''; res.className = 'test-result';
  // Save first reminder: test uses the SAVED key, not what's typed
  fetch(URL_TEST, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({provider}) })
    .then(r=>r.json()).then(d=>{
      btn.disabled=false; btn.innerHTML=orig;
      res.textContent = (d.ok ? '✓ ' : '✗ ') + d.message;
      res.className = 'test-result ' + (d.ok ? 'ok' : 'bad');
    }).catch(()=>{ btn.disabled=false; btn.innerHTML=orig; res.textContent='✗ '+(FR?'Echec':'Failed'); res.className='test-result bad'; });
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
