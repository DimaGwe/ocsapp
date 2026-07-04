<?php
$currentPage = 'content-library';
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$pageTitle = $fr ? 'Bibliotheque de contenu' : 'Content Library';
$posts = $posts ?? [];

$counts = ['idea'=>0,'approved'=>0,'scheduled'=>0,'posted'=>0];
foreach ($posts as $p) { if (isset($counts[$p['status']])) $counts[$p['status']]++; }
$total = count($posts);

ob_start();
?>
<style>
:root {
  --clib-bg:#f3f4f6; --clib-header-bg:#0a0e27;
  --clib-green:#00b207; --clib-green-dim:rgba(0,178,7,.08); --clib-green-border:rgba(0,178,7,.22);
}

/* dark hero header */
.clib-header { background:var(--clib-header-bg); border-radius:14px; padding:26px 28px 22px; margin-bottom:14px; display:flex; align-items:flex-end; justify-content:space-between; gap:20px; flex-wrap:wrap; }
.clib-brand-line { display:flex; align-items:center; gap:7px; margin-bottom:6px; }
.clib-brand-dot { width:7px; height:7px; background:var(--clib-green); border-radius:50%; box-shadow:0 0 6px var(--clib-green); }
.clib-brand-name { font-size:11px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; color:var(--clib-green); }
.clib-header h1 { font-size:24px; font-weight:700; color:#fff; margin:0 0 5px; }
.clib-header-sub { font-size:12.5px; color:rgba(255,255,255,.45); max-width:480px; }
.clib-header-right { display:flex; flex-direction:column; align-items:flex-end; gap:12px; }
.clib-stats-row { display:flex; gap:2px; }
.clib-stat-box { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); padding:10px 16px; text-align:center; min-width:72px; }
.clib-stat-box:first-child { border-radius:8px 0 0 8px; }
.clib-stat-box:last-child { border-radius:0 8px 8px 0; }
.clib-stat-num { font-size:20px; font-weight:700; color:var(--clib-green); line-height:1; }
.clib-stat-label { font-size:9.5px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:.06em; margin-top:3px; }

.clib-progress-wrap { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:12px 18px; display:flex; align-items:center; gap:14px; margin-bottom:14px; }
.clib-progress-label { font-size:12px; color:#6b7280; white-space:nowrap; min-width:130px; }
.clib-progress-track { flex:1; height:4px; background:#e5e7eb; border-radius:4px; overflow:hidden; }
.clib-progress-fill { height:100%; background:var(--clib-green); border-radius:4px; width:0%; transition:width .3s ease; }
.clib-progress-pct { font-size:12px; font-weight:700; color:var(--clib-green); min-width:34px; text-align:right; }

.clib-view-toggle { display:flex; gap:8px; margin-bottom:14px; }
.clib-view-btn { font-size:12.5px; font-weight:600; padding:7px 16px; border-radius:8px; border:1px solid #e5e7eb; background:#fff; color:#6b7280; cursor:pointer; display:flex; align-items:center; gap:6px; }
.clib-view-btn.active { background:var(--clib-green-dim); border-color:var(--clib-green-border); color:#008f05; }
.clib-view-btn:hover:not(.active) { border-color:var(--clib-green-border); }

.lib-header { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:18px; flex-wrap:wrap; }
.btn-new { background:#00b207; color:#fff; text-decoration:none; padding:10px 16px; border-radius:8px; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:7px; white-space:nowrap; }
.btn-new:hover { background:#009906; }

.lib-bar { display:flex; gap:10px; align-items:center; margin-bottom:16px; flex-wrap:wrap; }
.seg { display:flex; background:#fff; border:1px solid #e5e7eb; border-radius:9px; overflow:hidden; }
.seg button { border:none; background:none; padding:8px 14px; font-size:12.5px; font-weight:600; color:#6b7280; cursor:pointer; }
.seg button.active { background:#00b207; color:#fff; }
.lib-bar select, .lib-bar input { border:1px solid #d1d5db; border-radius:8px; padding:8px 11px; font-size:13px; font-family:inherit; }

.lib-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(290px,1fr)); gap:16px; }
.pcard { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; display:flex; flex-direction:column; }
.pcard .imgwrap { position:relative; aspect-ratio:1/1; background:#f3f4f6; display:flex; align-items:center; justify-content:center; }
.pcard .imgwrap img { width:100%; height:100%; object-fit:cover; }
.pcard .imgwrap .noimg { color:#cbd5e1; font-size:13px; }
.pcard .badges { position:absolute; top:8px; left:8px; }
.pill { font-size:10px; font-weight:700; padding:3px 9px; border-radius:20px; text-transform:uppercase; letter-spacing:.4px; color:#fff; }
.p-idea{background:#94a3b8} .p-approved{background:#00b207} .p-scheduled{background:#2563eb} .p-posted{background:#475569}
.pcard .plats { position:absolute; top:8px; right:8px; display:flex; gap:4px; flex-wrap:wrap; justify-content:flex-end; max-width:60%; }
.pcard .plats span { background:rgba(0,0,0,.6); color:#fff; font-size:10px; font-weight:700; padding:3px 7px; border-radius:6px; }
.pcard .body { padding:13px; display:flex; flex-direction:column; gap:7px; flex:1; }
.pcard .ttl { font-weight:700; font-size:14px; color:#111827; }
.pcard .cap { font-size:12.5px; color:#374151; white-space:pre-wrap; max-height:88px; overflow:hidden; line-height:1.5; }
.pcard .tags { font-size:11.5px; color:#2563eb; word-break:break-word; }
.pcard .meta { font-size:11px; color:#9ca3af; margin-top:auto; }
.lang-toggle { display:inline-flex; gap:4px; }
.lang-toggle button { font-size:10px; font-weight:700; padding:2px 7px; border-radius:5px; border:1px solid #e5e7eb; background:#fff; color:#9ca3af; cursor:pointer; }
.lang-toggle button.on { background:#0a0e27; color:#fff; border-color:#0a0e27; }
.pcard .actions { display:flex; gap:5px; border-top:1px solid #f3f4f6; padding:9px 13px; flex-wrap:wrap; }
.pcard .actions button, .pcard .actions a { flex:1; min-width:0; border:1px solid #e5e7eb; background:#fff; color:#374151; border-radius:7px; padding:7px 4px; font-size:11.5px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:4px; text-decoration:none; }
.pcard .actions .copy { background:#00b207; color:#fff; border-color:#00b207; }
.pcard .actions .copy:hover { background:#009906; }
.pcard .actions button:hover { background:#f9fafb; }
.pcard .actions .del:hover { color:#ef4444; border-color:#fca5a5; }

.lib-empty { grid-column:1/-1; text-align:center; padding:60px 20px; color:#9ca3af; }
.lib-empty h3 { color:#374151; margin:0 0 6px; }
.lib-toast { position:fixed; bottom:26px; left:50%; transform:translateX(-50%) translateY(70px); background:#111827; color:#fff; padding:11px 22px; border-radius:30px; font-size:13px; font-weight:600; opacity:0; transition:.25s; z-index:1000; }
.lib-toast.show { transform:translateX(-50%) translateY(0); opacity:1; }

/* calendar view */
#calendar-view { display:none; }
#calendar-view.active { display:block; }
#list-view.hidden { display:none; }

.clib-cal-month-block { margin-bottom:28px; }
.clib-cal-month-title { font-size:13px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#6b7280; margin-bottom:10px; display:flex; align-items:center; gap:12px; }
.clib-cal-month-title::after { content:''; flex:1; height:1px; background:#e5e7eb; }
.clib-cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:1px; background:#e5e7eb; border-radius:10px; overflow:hidden; border:1px solid #e5e7eb; }
.clib-cal-day-header { background:var(--clib-bg); padding:8px 4px; text-align:center; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#9ca3af; }
.clib-cal-day { background:#fff; min-height:84px; padding:6px; position:relative; }
.clib-cal-day.empty { background:var(--clib-bg); }
.clib-cal-day.has-post { cursor:pointer; }
.clib-cal-day.has-post:hover { background:#f9fafb; }
.clib-cal-day.is-today { background:var(--clib-green-dim); }
.clib-cal-day-num { font-size:11px; font-weight:700; color:#9ca3af; margin-bottom:4px; }
.clib-cal-day.is-today .clib-cal-day-num { color:#008f05; }
.clib-cal-chip { border-radius:5px; padding:4px 6px; margin-top:2px; overflow:hidden; position:relative; }
.clib-cal-chip-bar { position:absolute; top:0; left:0; width:3px; height:100%; }
.clib-cal-chip-status { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.03em; }
.clib-cal-chip-title { font-size:10.5px; color:#111827; line-height:1.3; margin-top:1px; font-weight:600; }
.clib-cal-empty { text-align:center; padding:50px 20px; color:#9ca3af; }
.clib-cal-nodate { font-size:11.5px; color:#9ca3af; margin-top:8px; }

/* modal */
.clib-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:200; align-items:center; justify-content:center; padding:20px; }
.clib-modal-overlay.open { display:flex; }
.clib-modal { background:#fff; border-radius:12px; max-width:560px; width:100%; max-height:85vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.clib-modal-header { padding:18px 22px 14px; border-bottom:1px solid #e5e7eb; display:flex; align-items:flex-start; justify-content:space-between; gap:12px; position:sticky; top:0; background:#fff; border-radius:12px 12px 0 0; }
.clib-modal-date { font-size:12px; color:#6b7280; }
.clib-modal-title { font-size:16px; font-weight:700; color:#111827; line-height:1.3; margin-top:3px; }
.clib-modal-close { width:30px; height:30px; border-radius:50%; border:1px solid #e5e7eb; background:transparent; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#6b7280; flex-shrink:0; }
.clib-modal-close:hover { border-color:#9ca3af; }
.clib-modal-body { padding:18px 22px; }
.clib-modal-img { width:100%; border-radius:8px; margin-bottom:14px; max-height:260px; object-fit:cover; }
.clib-modal-copy { background:var(--clib-bg); border:1px solid #e5e7eb; border-radius:8px; padding:14px 16px; font-size:13px; line-height:1.8; color:#111827; white-space:pre-wrap; font-family:inherit; margin-bottom:12px; }
.clib-modal-hashtags { font-size:12px; color:#2563eb; margin-bottom:14px; word-break:break-word; }
.clib-modal-actions { display:flex; gap:8px; flex-wrap:wrap; }
.clib-modal-actions button, .clib-modal-actions a { font-size:12.5px; font-weight:600; padding:9px 16px; border-radius:7px; border:1px solid #e5e7eb; background:#fff; color:#374151; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.clib-modal-actions .copy { background:#00b207; color:#fff; border-color:#00b207; }
.clib-modal-actions .copy:hover { background:#009906; }

/* edit modal */
.edit-fg { margin-bottom:14px; }
.edit-fg label { display:block; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:5px; }
.edit-fg input, .edit-fg textarea, .edit-fg select { width:100%; padding:9px 11px; font-size:13px; border:1px solid #d1d5db; border-radius:8px; box-sizing:border-box; font-family:inherit; }
.edit-fg textarea { resize:vertical; min-height:70px; line-height:1.5; }
.edit-fg input:focus, .edit-fg textarea:focus, .edit-fg select:focus { outline:none; border-color:#00b207; box-shadow:0 0 0 3px rgba(0,178,7,.08); }
.edit-fg-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.edit-plat-chips { display:flex; gap:7px; flex-wrap:wrap; }
.edit-plat-chip { border:1px solid #e5e7eb; border-radius:20px; padding:6px 12px; font-size:12px; font-weight:600; cursor:pointer; color:#6b7280; user-select:none; }
.edit-plat-chip.on { background:#f0fdf4; border-color:#00b207; color:#15803d; }
.edit-imgbox { border:1px dashed #d1d5db; border-radius:10px; padding:14px; background:#fafafa; margin-bottom:14px; text-align:center; }
.edit-imgbox img { max-width:100%; max-height:200px; border-radius:8px; border:1px solid #e5e7eb; margin-bottom:10px; }
.edit-imgbox .noimg { color:#9ca3af; font-size:12.5px; margin-bottom:10px; }
.edit-img-tools { display:flex; gap:8px; justify-content:center; flex-wrap:wrap; }
.edit-img-tools button, .edit-img-tools label { font-size:12px; padding:7px 12px; border-radius:7px; border:1px solid #e5e7eb; background:#fff; color:#374151; cursor:pointer; }
.edit-img-tools input[type=file] { display:none; }
.pcard .actions .edit:hover { color:#00b207; border-color:#00b207; }
.pcard .actions .dl:hover { color:#00b207; border-color:#00b207; }

@media (max-width:700px) {
  .clib-header { padding:20px 18px 18px; }
  .clib-stats-row { flex-wrap:wrap; }
  .pcard .tags { display:none; }
}
</style>

<div class="clib-header">
  <div>
    <div class="clib-brand-line"><div class="clib-brand-dot"></div><div class="clib-brand-name">OCSAPP · ADMIN</div></div>
    <h1><?= $pageTitle ?></h1>
    <div class="clib-header-sub"><?= $fr ? 'Vos posts prets a publier. Copiez la caption, recuperez l\'image, marquez comme publie.' : 'Your ready-to-post content. Copy the caption, grab the image, mark as posted.' ?></div>
  </div>
  <div class="clib-header-right">
    <a class="btn-new" href="<?= url('admin/content/create') ?>"><i class="fa-solid fa-plus"></i> <?= $fr ? 'Nouveau post' : 'New post' ?></a>
    <div class="clib-stats-row">
      <div class="clib-stat-box"><div class="clib-stat-num" id="statTotal"><?= $total ?></div><div class="clib-stat-label"><?= $fr?'Total':'Total' ?></div></div>
      <div class="clib-stat-box"><div class="clib-stat-num" id="statIdea"><?= $counts['idea'] ?></div><div class="clib-stat-label"><?= $fr?'Idees':'Ideas' ?></div></div>
      <div class="clib-stat-box"><div class="clib-stat-num" id="statApproved"><?= $counts['approved'] ?></div><div class="clib-stat-label"><?= $fr?'Approuves':'Approved' ?></div></div>
      <div class="clib-stat-box"><div class="clib-stat-num" id="statScheduled"><?= $counts['scheduled'] ?></div><div class="clib-stat-label"><?= $fr?'En file':'Queue' ?></div></div>
      <div class="clib-stat-box"><div class="clib-stat-num" id="statPosted"><?= $counts['posted'] ?></div><div class="clib-stat-label"><?= $fr?'Publies':'Posted' ?></div></div>
    </div>
  </div>
</div>

<div class="clib-progress-wrap">
  <div class="clib-progress-label" id="progressText"></div>
  <div class="clib-progress-track"><div class="clib-progress-fill" id="progressFill"></div></div>
  <div class="clib-progress-pct" id="progressPct"></div>
</div>

<div class="lib-bar">
  <div class="seg" id="statusSeg">
    <button data-s="all" class="active"><?= $fr?'Tous':'All' ?></button>
    <button data-s="idea"><?= $fr?'Idees':'Ideas' ?></button>
    <button data-s="approved"><?= $fr?'Approuves':'Approved' ?></button>
    <button data-s="scheduled"><?= $fr?'File':'Queue' ?></button>
    <button data-s="posted"><?= $fr?'Publies':'Posted' ?></button>
  </div>
  <select id="platFilter">
    <option value=""><?= $fr?'Toutes plateformes':'All platforms' ?></option>
    <?php foreach (['Instagram','Facebook','LinkedIn','TikTok','X/Twitter'] as $p): ?>
      <option value="<?= $p ?>"><?= $p ?></option>
    <?php endforeach; ?>
  </select>
  <input id="search" placeholder="<?= $fr?'Rechercher...':'Search...' ?>" style="flex:1;max-width:240px;">
</div>

<div class="clib-view-toggle">
  <button class="clib-view-btn active" id="btnList" onclick="switchView('list')">☰ <?= $fr?'Liste':'List' ?></button>
  <button class="clib-view-btn" id="btnCal" onclick="switchView('calendar')">▦ <?= $fr?'Calendrier':'Calendar' ?></button>
</div>

<div id="list-view"><div class="lib-grid" id="grid"></div></div>
<div id="calendar-view"></div>

<div class="clib-modal-overlay" id="calModal" onclick="closeCalModal(event)">
  <div class="clib-modal" id="calModalBox">
    <div class="clib-modal-header">
      <div>
        <div class="clib-modal-date" id="modalDate"></div>
        <div class="clib-modal-title" id="modalTitle"></div>
      </div>
      <button class="clib-modal-close" onclick="closeCalModalDirect()">✕</button>
    </div>
    <div class="clib-modal-body">
      <img class="clib-modal-img" id="modalImg" style="display:none;">
      <div class="clib-modal-copy" id="modalBody"></div>
      <div class="clib-modal-hashtags" id="modalHashtags"></div>
      <div class="clib-modal-actions">
        <button class="copy" id="modalCopyBtn">⎘ <?= $fr?'Copier le texte':'Copy text' ?></button>
        <button id="modalEditBtn"><i class="fa-solid fa-pen"></i> <?= $fr?'Modifier':'Edit' ?></button>
        <button id="modalAdvanceBtn"></button>
        <a id="modalImgLink" href="#" target="_blank" style="display:none;"><i class="fa-solid fa-image"></i> <?= $fr?'Image':'Image' ?></a>
      </div>
    </div>
  </div>
</div>

<div class="clib-modal-overlay" id="editModal" onclick="closeEditModal(event)">
  <div class="clib-modal" id="editModalBox">
    <div class="clib-modal-header">
      <div class="clib-modal-title"><?= $fr?'Modifier le post':'Edit post' ?></div>
      <button class="clib-modal-close" onclick="closeEditModalDirect()">✕</button>
    </div>
    <div class="clib-modal-body">
      <input type="hidden" id="eId" value="">

      <div class="edit-imgbox" id="editImgBox">
        <img id="editImgPreview" style="display:none;">
        <div class="noimg" id="editNoImg"><?= $fr?'Pas d\'image':'No image' ?></div>
        <div class="edit-img-tools">
          <a id="editImgDownload" href="#" style="display:none;"><i class="fa-solid fa-download"></i> <?= $fr?'Telecharger':'Download' ?></a>
          <label for="editImgFile"><i class="fa-solid fa-upload"></i> <?= $fr?'Televerser une image':'Upload image' ?></label>
          <input type="file" id="editImgFile" accept="image/png,image/jpeg,image/webp,image/gif" onchange="onEditFileChosen(this)">
          <button type="button" onclick="removeEditImage()"><i class="fa-solid fa-xmark"></i> <?= $fr?'Retirer':'Remove' ?></button>
        </div>
      </div>
      <input type="hidden" id="eImagePath" value="">

      <div class="edit-fg">
        <label><?= $fr?'Titre interne':'Internal title' ?></label>
        <input id="eTitle">
      </div>

      <div class="edit-fg">
        <label><?= $fr?'Plateformes':'Platforms' ?></label>
        <div class="edit-plat-chips" id="editPlatChips">
          <?php foreach (['Instagram','Facebook','LinkedIn','TikTok','X/Twitter'] as $p): ?>
            <span class="edit-plat-chip" data-p="<?= $p ?>" onclick="this.classList.toggle('on')"><?= $p ?></span>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="edit-fg">
        <label>Caption (EN)</label>
        <textarea id="eCaptionEn"></textarea>
      </div>
      <div class="edit-fg">
        <label>Caption (FR)</label>
        <textarea id="eCaptionFr"></textarea>
      </div>
      <div class="edit-fg">
        <label>Hashtags</label>
        <input id="eHashtags">
      </div>

      <div class="edit-fg-row">
        <div class="edit-fg">
          <label><?= $fr?'Statut':'Status' ?></label>
          <select id="eStatus">
            <option value="idea"><?= $fr?'Idee':'Idea' ?></option>
            <option value="approved"><?= $fr?'Approuve':'Approved' ?></option>
            <option value="scheduled"><?= $fr?'En file':'Scheduled' ?></option>
            <option value="posted"><?= $fr?'Publie':'Posted' ?></option>
          </select>
        </div>
        <div class="edit-fg">
          <label><?= $fr?'Date de publication':'Post date' ?></label>
          <input type="date" id="ePostDate">
        </div>
      </div>

      <div class="clib-modal-actions">
        <button class="copy" id="eSaveBtn" onclick="saveEditPost()"><i class="fa-solid fa-floppy-disk"></i> <?= $fr?'Enregistrer':'Save' ?></button>
        <button onclick="closeEditModalDirect()"><?= $fr?'Annuler':'Cancel' ?></button>
      </div>
    </div>
  </div>
</div>

<div class="lib-toast" id="libToast"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const URL_STATUS = '<?= url('admin/content/update-status') ?>';
const URL_DELETE = '<?= url('admin/content/delete') ?>';
const URL_SAVE = '<?= url('admin/content/save') ?>';
const URL_UPLOAD = '<?= url('admin/content/upload-image') ?>';
const FR = <?= $fr ? 'true':'false' ?>;
let posts = <?= json_encode(array_map(function($p){
  return [
    'id'=>(int)$p['id'],'title'=>$p['title'],'caption_en'=>$p['caption_en'],'caption_fr'=>$p['caption_fr'],
    'hashtags'=>$p['hashtags'],'platforms'=>$p['platforms'],'status'=>$p['status'],
    'post_date'=>$p['post_date'],'image_path'=>$p['image_path']
  ];
}, $posts), JSON_UNESCAPED_UNICODE) ?>;
const ASSET_BASE = '<?= rtrim(url(''), '/') ?>/';
let fStatus='all', fPlat='', fSearch='';
let currentModalId = null;

const STATUS_COLORS = {idea:'#94a3b8', approved:'#00b207', scheduled:'#2563eb', posted:'#475569'};
const STATUS_LABEL_FR = {idea:'Idee', approved:'Approuve', scheduled:'Planifie', posted:'Publie'};
const STATUS_LABEL_EN = {idea:'Idea', approved:'Approved', scheduled:'Scheduled', posted:'Posted'};

function post(url, body){ return fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(body)}).then(r=>r.json()); }
function toast(m){ const t=document.getElementById('libToast'); t.textContent=m; t.classList.add('show'); clearTimeout(t._x); t._x=setTimeout(()=>t.classList.remove('show'),2400); }
function esc(s){ return (s||'').replace(/[&<>]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c])); }
function imgUrl(p){ return p ? (ASSET_BASE+p) : ''; }
function slugify(s){
  var combining = new RegExp('[' + String.fromCharCode(768) + '-' + String.fromCharCode(879) + ']', 'g');
  return (s||'post').toLowerCase().normalize('NFD').replace(combining,'').replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'') || 'post';
}
function downloadName(p){ const ext=(p.image_path||'').split('.').pop()||'png'; return 'ocsapp-'+slugify(p.title)+'.'+ext; }

document.querySelectorAll('#statusSeg button').forEach(b=>b.onclick=()=>{
  document.querySelectorAll('#statusSeg button').forEach(x=>x.classList.remove('active')); b.classList.add('active'); fStatus=b.dataset.s; render();
});
document.getElementById('platFilter').onchange=e=>{ fPlat=e.target.value; render(); };
document.getElementById('search').oninput=e=>{ fSearch=e.target.value.toLowerCase(); render(); };

function render(){
  let list = posts.filter(p=>{
    if(fStatus!=='all' && p.status!==fStatus) return false;
    if(fPlat && (p.platforms||'').indexOf(fPlat)===-1) return false;
    if(fSearch && ((p.title||'')+(p.caption_en||'')+(p.caption_fr||'')+(p.hashtags||'')).toLowerCase().indexOf(fSearch)===-1) return false;
    return true;
  });
  if(fStatus==='scheduled') list.sort((a,b)=>(a.post_date||'9999').localeCompare(b.post_date||'9999'));
  const g=document.getElementById('grid');
  if(!list.length){ g.innerHTML='<div class="lib-empty"><h3>'+(FR?'Aucun post ici':'Nothing here yet')+'</h3><p>'+(FR?'Creez un post depuis le Createur de contenu.':'Create one from the Content Creator.')+'</p></div>'; return; }
  g.innerHTML = list.map(p=>{
    const img = p.image_path ? '<img src="'+imgUrl(p.image_path)+'" alt="">' : '<div class="noimg">'+(FR?'Pas d\'image':'No image')+'</div>';
    const plats = (p.platforms||'').split(',').filter(Boolean).map(x=>'<span>'+esc(x.split('/')[0])+'</span>').join('');
    const hasEn = !!(p.caption_en||'').trim(), hasFr = !!(p.caption_fr||'').trim();
    const cap = hasEn ? p.caption_en : p.caption_fr;
    const tags = p.hashtags ? '<div class="tags">'+esc(p.hashtags)+'</div>' : '';
    const date = p.post_date ? '📅 '+p.post_date : (FR?'Pas de date':'No date');
    let langTog='';
    if(hasEn && hasFr){ langTog='<span class="lang-toggle"><button class="on" onclick="setLang('+p.id+',\'en\',this)">EN</button><button onclick="setLang('+p.id+',\'fr\',this)">FR</button></span>'; }
    return '<div class="pcard" data-id="'+p.id+'">'
      + '<div class="imgwrap">'+img+'<div class="badges"><span class="pill p-'+p.status+'">'+p.status+'</span></div><div class="plats">'+plats+'</div></div>'
      + '<div class="body"><div class="ttl">'+esc(p.title)+'</div>'
      + '<div class="cap" data-en="'+esc(p.caption_en||'')+'" data-fr="'+esc(p.caption_fr||'')+'">'+esc(cap)+'</div>'
      + tags
      + '<div class="meta">'+date+' '+langTog+'</div></div>'
      + '<div class="actions">'
      + '<button class="copy" onclick="copyCap('+p.id+')"><i class="fa-regular fa-copy"></i> '+(FR?'Copier':'Copy')+'</button>'
      + (p.image_path ? '<a class="dl" href="'+imgUrl(p.image_path)+'" download="'+downloadName(p)+'"><i class="fa-solid fa-download"></i> '+(FR?'Telecharger':'Download')+'</a>' : '')
      + '<button class="edit" onclick="openEditModal('+p.id+')"><i class="fa-solid fa-pen"></i> '+(FR?'Modifier':'Edit')+'</button>'
      + '<button onclick="advance('+p.id+')" title="'+(FR?'Avancer':'Advance')+'"><i class="fa-solid fa-arrow-right"></i></button>'
      + '<button class="del" onclick="del('+p.id+')"><i class="fa-solid fa-trash"></i></button>'
      + '</div></div>';
  }).join('');
}

function updateStats(){
  const c = {idea:0, approved:0, scheduled:0, posted:0};
  posts.forEach(p=>{ if(c[p.status]!==undefined) c[p.status]++; });
  document.getElementById('statTotal').textContent = posts.length;
  document.getElementById('statIdea').textContent = c.idea;
  document.getElementById('statApproved').textContent = c.approved;
  document.getElementById('statScheduled').textContent = c.scheduled;
  document.getElementById('statPosted').textContent = c.posted;
  const pct = posts.length ? Math.round((c.posted/posts.length)*100) : 0;
  document.getElementById('progressFill').style.width = pct+'%';
  document.getElementById('progressPct').textContent = pct+'%';
  document.getElementById('progressText').textContent = c.posted+' / '+posts.length+' '+(FR?'publies':'posted');
}

function setLang(id, lang, btn){
  const card = document.querySelector('.pcard[data-id="'+id+'"]'); if(!card) return;
  const cap = card.querySelector('.cap');
  cap.textContent = lang==='fr' ? cap.dataset.fr : cap.dataset.en;
  btn.parentNode.querySelectorAll('button').forEach(b=>b.classList.remove('on')); btn.classList.add('on');
}

function copyCap(id){
  const p = posts.find(x=>x.id===id);
  const card = document.querySelector('.pcard[data-id="'+id+'"]');
  // copy whichever language is currently shown, fall back to EN then FR
  let base = card ? card.querySelector('.cap').textContent : (p.caption_en||p.caption_fr);
  const txt = base + (p.hashtags ? '\n\n'+p.hashtags : '');
  navigator.clipboard.writeText(txt).then(()=>toast(FR?'Caption copiee':'Caption copied'));
}

function advance(id){
  const order=['idea','approved','scheduled','posted'];
  const p=posts.find(x=>x.id===id);
  const next=order[(order.indexOf(p.status)+1)%order.length];
  post(URL_STATUS,{id,status:next}).then(d=>{
    if(d.success){
      p.status=next; render(); buildCalendar(); updateStats(); toast((FR?'Marque ':'Marked ')+next);
      if(currentModalId===id) openCalModal(id);
    } else toast(d.error||'Error');
  });
}

function del(id){
  if(!confirm(FR?'Supprimer ce post ?':'Delete this post?')) return;
  post(URL_DELETE,{id}).then(d=>{
    if(d.success){
      posts=posts.filter(x=>x.id!==id); render(); buildCalendar(); updateStats(); toast(FR?'Supprime':'Deleted');
    } else toast(d.error||'Error');
  });
}

function switchView(v){
  const lv=document.getElementById('list-view');
  const cv=document.getElementById('calendar-view');
  const bl=document.getElementById('btnList');
  const bc=document.getElementById('btnCal');
  if(v==='calendar'){
    lv.classList.add('hidden'); cv.classList.add('active');
    bl.classList.remove('active'); bc.classList.add('active');
  } else {
    lv.classList.remove('hidden'); cv.classList.remove('active');
    bl.classList.add('active'); bc.classList.remove('active');
  }
}

function buildCalendar(){
  const cal = document.getElementById('calendar-view');
  const dayNames = FR ? ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'] : ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
  const monthNamesFr = ['','Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'];
  const monthNamesEn = ['','January','February','March','April','May','June','July','August','September','October','November','December'];

  const dated = posts.filter(p => /^\d{4}-\d{2}-\d{2}$/.test(p.post_date||''));
  const noDateCount = posts.length - dated.length;

  if(!dated.length){
    cal.innerHTML = '<div class="clib-cal-empty"><h3>'+(FR?'Aucun post avec une date':'No dated posts yet')+'</h3><p>'+(FR?'Ajoutez une date de publication pour voir vos posts ici.':'Add a post date to see your posts here.')+'</p></div>';
    return;
  }

  const byMonth = {};
  dated.forEach(p=>{
    const [y,m,d] = p.post_date.split('-').map(Number);
    const key = y+'-'+m;
    if(!byMonth[key]) byMonth[key] = {y, m, items:{}};
    byMonth[key].items[d] = p;
  });

  const now = new Date();
  let html = '';
  Object.keys(byMonth).sort().forEach(key=>{
    const {y, m, items} = byMonth[key];
    const monthLabel = (FR?monthNamesFr[m]:monthNamesEn[m]) + ' ' + y;
    const daysInMonth = new Date(y, m, 0).getDate();
    const firstDow = new Date(y, m-1, 1).getDay();

    html += '<div class="clib-cal-month-block"><div class="clib-cal-month-title">'+monthLabel+'</div><div class="clib-cal-grid">';
    dayNames.forEach(d=>{ html += '<div class="clib-cal-day-header">'+d+'</div>'; });
    for(let e=0;e<firstDow;e++) html += '<div class="clib-cal-day empty"></div>';
    for(let d=1; d<=daysInMonth; d++){
      const p = items[d];
      const isToday = (y===now.getFullYear() && m===now.getMonth()+1 && d===now.getDate());
      let cls = 'clib-cal-day' + (isToday?' is-today':'') + (p?' has-post':'');
      html += '<div class="'+cls+'"'+(p?' onclick="openCalModal('+p.id+')"':'')+'>';
      html += '<div class="clib-cal-day-num">'+d+'</div>';
      if(p){
        const color = STATUS_COLORS[p.status];
        const label = FR ? STATUS_LABEL_FR[p.status] : STATUS_LABEL_EN[p.status];
        const shortTitle = (p.title||'').length>34 ? p.title.substring(0,32)+'...' : p.title;
        html += '<div class="clib-cal-chip" style="background:'+color+'1a;border:1px solid '+color+'40;">'
          + '<div class="clib-cal-chip-bar" style="background:'+color+'"></div>'
          + '<div style="padding-left:6px;"><div class="clib-cal-chip-status" style="color:'+color+';">'+label+'</div>'
          + '<div class="clib-cal-chip-title">'+esc(shortTitle)+'</div></div></div>';
      }
      html += '</div>';
    }
    const totalCells = firstDow + daysInMonth;
    const remainder = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for(let e=0;e<remainder;e++) html += '<div class="clib-cal-day empty"></div>';
    html += '</div></div>';
  });

  if(noDateCount > 0){
    html += '<div class="clib-cal-nodate">'+noDateCount+' '+(FR?'post(s) sans date de publication ne sont pas affiches ici.':'post(s) without a publish date are not shown here.')+'</div>';
  }
  cal.innerHTML = html;
}

function openCalModal(id){
  currentModalId = id;
  const p = posts.find(x=>x.id===id);
  if(!p) return;
  const hasEn = !!(p.caption_en||'').trim(), hasFr = !!(p.caption_fr||'').trim();
  const cap = hasEn ? p.caption_en : p.caption_fr;

  document.getElementById('modalDate').textContent = (p.post_date||(FR?'Pas de date':'No date')) + ' · ' + (FR?STATUS_LABEL_FR[p.status]:STATUS_LABEL_EN[p.status]);
  document.getElementById('modalTitle').textContent = p.title;
  document.getElementById('modalBody').textContent = cap;
  document.getElementById('modalHashtags').textContent = p.hashtags || '';

  const img = document.getElementById('modalImg');
  const imgLink = document.getElementById('modalImgLink');
  if(p.image_path){
    img.src = imgUrl(p.image_path); img.style.display = 'block';
    imgLink.href = imgUrl(p.image_path); imgLink.setAttribute('download', downloadName(p));
    imgLink.innerHTML = '<i class="fa-solid fa-download"></i> ' + (FR?'Telecharger l\'image':'Download image');
    imgLink.style.display = 'inline-flex';
  } else {
    img.style.display = 'none'; imgLink.style.display = 'none'; imgLink.removeAttribute('download');
  }

  document.getElementById('modalCopyBtn').onclick = ()=>copyCap(id);
  const advBtn = document.getElementById('modalAdvanceBtn');
  advBtn.textContent = (FR?'Avancer le statut':'Advance status');
  advBtn.onclick = ()=>advance(id);
  document.getElementById('modalEditBtn').onclick = ()=>{ closeCalModalDirect(); openEditModal(id); };

  document.getElementById('calModal').classList.add('open');
}

function closeCalModal(e){ if(e.target===document.getElementById('calModal')) closeCalModalDirect(); }
function closeCalModalDirect(){ document.getElementById('calModal').classList.remove('open'); currentModalId=null; }

/* ---- edit modal ---- */
let editPendingFile = null;
let editRemovedImage = false;

function openEditModal(id){
  const p = posts.find(x=>x.id===id);
  if(!p) return;
  editPendingFile = null;
  editRemovedImage = false;

  document.getElementById('eId').value = p.id;
  document.getElementById('eTitle').value = p.title || '';
  document.getElementById('eCaptionEn').value = p.caption_en || '';
  document.getElementById('eCaptionFr').value = p.caption_fr || '';
  document.getElementById('eHashtags').value = p.hashtags || '';
  document.getElementById('eStatus').value = p.status || 'idea';
  document.getElementById('ePostDate').value = p.post_date || '';
  document.getElementById('eImagePath').value = p.image_path || '';
  document.getElementById('editImgFile').value = '';

  const platSet = new Set((p.platforms||'').split(',').map(x=>x.trim()).filter(Boolean));
  document.querySelectorAll('#editPlatChips .edit-plat-chip').forEach(c=>{
    c.classList.toggle('on', platSet.has(c.dataset.p));
  });

  refreshEditImagePreview(p.image_path ? imgUrl(p.image_path) : '');
  setEditDownloadLink(p.image_path ? p : null);

  document.getElementById('editModal').classList.add('open');
}

function setEditDownloadLink(p){
  const dl = document.getElementById('editImgDownload');
  if(p && p.image_path){ dl.href = imgUrl(p.image_path); dl.setAttribute('download', downloadName(p)); dl.style.display = 'inline-flex'; }
  else { dl.style.display = 'none'; dl.removeAttribute('download'); }
}

function refreshEditImagePreview(url){
  const img = document.getElementById('editImgPreview');
  const noimg = document.getElementById('editNoImg');
  if(url){ img.src = url; img.style.display = 'block'; noimg.style.display = 'none'; }
  else { img.style.display = 'none'; img.src=''; noimg.style.display = 'block'; }
}

function onEditFileChosen(input){
  const file = input.files && input.files[0];
  if(!file) return;
  editPendingFile = file;
  editRemovedImage = false;
  setEditDownloadLink(null);
  const reader = new FileReader();
  reader.onload = e => refreshEditImagePreview(e.target.result);
  reader.readAsDataURL(file);
}

function removeEditImage(){
  editPendingFile = null;
  editRemovedImage = true;
  document.getElementById('editImgFile').value = '';
  document.getElementById('eImagePath').value = '';
  refreshEditImagePreview('');
  setEditDownloadLink(null);
}

function closeEditModal(e){ if(e.target===document.getElementById('editModal')) closeEditModalDirect(); }
function closeEditModalDirect(){ document.getElementById('editModal').classList.remove('open'); editPendingFile=null; editRemovedImage=false; }

function saveEditPost(){
  const id = parseInt(document.getElementById('eId').value, 10);
  const captionEn = document.getElementById('eCaptionEn').value;
  const captionFr = document.getElementById('eCaptionFr').value;
  if(!captionEn.trim() && !captionFr.trim()){ toast(FR?'Ajoutez une caption (EN ou FR)':'Add a caption (EN or FR)'); return; }

  const btn = document.getElementById('eSaveBtn');
  btn.disabled = true;

  const finishSave = (imagePath) => {
    const platforms = [...document.querySelectorAll('#editPlatChips .edit-plat-chip.on')].map(c=>c.dataset.p);
    const data = {
      id, title: document.getElementById('eTitle').value,
      caption_en: captionEn, caption_fr: captionFr,
      hashtags: document.getElementById('eHashtags').value,
      platforms, status: document.getElementById('eStatus').value,
      post_date: document.getElementById('ePostDate').value,
      image_path: imagePath, generated_by: 'manual'
    };
    post(URL_SAVE, data).then(d=>{
      btn.disabled = false;
      if(!d.success){ toast(d.error||'Error'); return; }
      const p = posts.find(x=>x.id===id);
      if(p){ Object.assign(p, data, {platforms: platforms.join(',')}); }
      render(); buildCalendar(); updateStats();
      closeEditModalDirect();
      toast(FR?'Post mis a jour':'Post updated');
    }).catch(()=>{ btn.disabled = false; toast(FR?'Echec de l\'enregistrement':'Save failed'); });
  };

  if(editPendingFile){
    const fd = new FormData();
    fd.append('image', editPendingFile);
    fetch(URL_UPLOAD, {method:'POST', headers:{'X-CSRF-TOKEN':CSRF}, body:fd})
      .then(r=>r.json())
      .then(d=>{
        if(!d.success){ btn.disabled=false; toast(d.error||'Upload failed'); return; }
        finishSave(d.path);
      }).catch(()=>{ btn.disabled=false; toast(FR?'Echec du televersement':'Upload failed'); });
  } else {
    finishSave(editRemovedImage ? '' : document.getElementById('eImagePath').value);
  }
}

render();
buildCalendar();
updateStats();
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
