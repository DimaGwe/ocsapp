<?php
$currentPage = 'content-library';
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$pageTitle = $fr ? 'Bibliotheque de contenu' : 'Content Library';
$posts = $posts ?? [];

$counts = ['idea'=>0,'approved'=>0,'scheduled'=>0,'posted'=>0];
foreach ($posts as $p) { if (isset($counts[$p['status']])) $counts[$p['status']]++; }

ob_start();
?>
<style>
.lib-header { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:18px; flex-wrap:wrap; }
.lib-header h1 { font-size:22px; font-weight:700; color:#111827; margin:0 0 4px; }
.lib-header p { font-size:13px; color:#6b7280; margin:0; }
.btn-new { background:#00b207; color:#fff; text-decoration:none; padding:10px 16px; border-radius:8px; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:7px; }
.btn-new:hover { background:#009906; }

.lib-stats { display:flex; gap:12px; margin-bottom:18px; flex-wrap:wrap; }
.lib-stat { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:12px 18px; flex:1; min-width:110px; }
.lib-stat .n { font-size:24px; font-weight:800; color:#00b207; }
.lib-stat .l { font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; }

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
</style>

<div class="lib-header">
  <div>
    <h1><i class="fa-solid fa-photo-film" style="color:#00b207;margin-right:8px;"></i><?= $pageTitle ?></h1>
    <p><?= $fr ? 'Vos posts prets a publier. Copiez la caption, recuperez l\'image, marquez comme publie.' : 'Your ready-to-post content. Copy the caption, grab the image, mark as posted.' ?></p>
  </div>
  <a class="btn-new" href="<?= url('admin/content/create') ?>"><i class="fa-solid fa-plus"></i> <?= $fr ? 'Nouveau post' : 'New post' ?></a>
</div>

<div class="lib-stats">
  <div class="lib-stat"><div class="n"><?= count($posts) ?></div><div class="l"><?= $fr?'Total':'Total' ?></div></div>
  <div class="lib-stat"><div class="n"><?= $counts['idea'] ?></div><div class="l"><?= $fr?'Idees':'Ideas' ?></div></div>
  <div class="lib-stat"><div class="n"><?= $counts['approved'] ?></div><div class="l"><?= $fr?'Approuves':'Approved' ?></div></div>
  <div class="lib-stat"><div class="n"><?= $counts['scheduled'] ?></div><div class="l"><?= $fr?'En file':'In Queue' ?></div></div>
  <div class="lib-stat"><div class="n"><?= $counts['posted'] ?></div><div class="l"><?= $fr?'Publies':'Posted' ?></div></div>
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

<div class="lib-grid" id="grid"></div>
<div class="lib-toast" id="libToast"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const URL_STATUS = '<?= url('admin/content/update-status') ?>';
const URL_DELETE = '<?= url('admin/content/delete') ?>';
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

function post(url, body){ return fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(body)}).then(r=>r.json()); }
function toast(m){ const t=document.getElementById('libToast'); t.textContent=m; t.classList.add('show'); clearTimeout(t._x); t._x=setTimeout(()=>t.classList.remove('show'),2400); }
function esc(s){ return (s||'').replace(/[&<>]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c])); }
function imgUrl(p){ return p ? (ASSET_BASE+p) : ''; }

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
      + (p.image_path ? '<a href="'+imgUrl(p.image_path)+'" target="_blank"><i class="fa-solid fa-image"></i> '+(FR?'Image':'Image')+'</a>' : '')
      + '<button onclick="advance('+p.id+')" title="'+(FR?'Avancer':'Advance')+'"><i class="fa-solid fa-arrow-right"></i></button>'
      + '<button class="del" onclick="del('+p.id+')"><i class="fa-solid fa-trash"></i></button>'
      + '</div></div>';
  }).join('');
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
  post(URL_STATUS,{id,status:next}).then(d=>{ if(d.success){ p.status=next; render(); toast((FR?'Marque ':'Marked ')+next); } else toast(d.error||'Error'); });
}

function del(id){
  if(!confirm(FR?'Supprimer ce post ?':'Delete this post?')) return;
  post(URL_DELETE,{id}).then(d=>{ if(d.success){ posts=posts.filter(x=>x.id!==id); render(); toast(FR?'Supprime':'Deleted'); } else toast(d.error||'Error'); });
}

render();
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
