<?php
$currentPage = 'content-creator';
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$pageTitle = $fr ? 'Createur de contenu' : 'Content Creator';

ob_start();
?>
<style>
.cc-header { margin-bottom: 20px; }
.cc-header h1 { font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 4px; }
.cc-header p  { font-size: 13px; color: #6b7280; margin: 0; }

.cc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: stretch; }
@media (max-width: 1000px) { .cc-grid { grid-template-columns: 1fr; } }

.cc-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; display:flex; flex-direction:column; overflow:hidden; }
.cc-card-head { padding:14px 18px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between; gap:8px; }
.cc-card-head h2 { font-size:14px; font-weight:600; color:#374151; margin:0; display:flex; align-items:center; gap:8px; }

/* ---- Chat ---- */
.chat-wrap { display:flex; flex-direction:column; height:620px; }
.chat-log { flex:1; overflow-y:auto; padding:18px; display:flex; flex-direction:column; gap:14px; background:#f9fafb; }
.msg { max-width:88%; padding:11px 14px; border-radius:12px; font-size:13.5px; line-height:1.6; white-space:pre-wrap; word-break:break-word; }
.msg.user { align-self:flex-end; background:#00b207; color:#fff; border-bottom-right-radius:3px; }
.msg.bot  { align-self:flex-start; background:#fff; color:#1f2937; border:1px solid #e5e7eb; border-bottom-left-radius:3px; }
.msg.bot .use-draft { margin-top:10px; padding:7px 13px; background:#0a0e27; color:#fff; border:none; border-radius:7px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
.msg.bot .use-draft:hover { background:#1a1f3a; }
.chat-hint { align-self:center; color:#9ca3af; font-size:12px; text-align:center; max-width:80%; }
.chat-input { display:flex; gap:8px; padding:12px; border-top:1px solid #f3f4f6; background:#fff; }
.chat-input textarea { flex:1; resize:none; border:1px solid #d1d5db; border-radius:8px; padding:10px 12px; font-size:13.5px; font-family:inherit; line-height:1.5; max-height:120px; }
.chat-input textarea:focus { outline:none; border-color:#00b207; box-shadow:0 0 0 3px rgba(0,178,7,.08); }
.chat-send { background:#00b207; color:#fff; border:none; border-radius:8px; width:46px; flex-shrink:0; cursor:pointer; font-size:16px; }
.chat-send:hover { background:#009906; } .chat-send:disabled { background:#9ca3af; cursor:not-allowed; }
.typing { align-self:flex-start; color:#6b7280; font-size:12px; display:flex; gap:5px; align-items:center; }
.typing .dot { width:6px; height:6px; background:#9ca3af; border-radius:50%; animation:blink 1.2s infinite; }
.typing .dot:nth-child(2){animation-delay:.2s} .typing .dot:nth-child(3){animation-delay:.4s}
@keyframes blink { 0%,60%,100%{opacity:.3} 30%{opacity:1} }

.quick-row { display:flex; gap:6px; flex-wrap:wrap; padding:0 12px 12px; }
.quick-chip { font-size:11px; padding:5px 10px; border:1px solid #e5e7eb; border-radius:20px; background:#fff; color:#4b5563; cursor:pointer; }
.quick-chip:hover { border-color:#00b207; color:#00b207; }

/* ---- Compose / Save ---- */
.compose-body { padding:18px; overflow-y:auto; max-height:620px; }
.fg { margin-bottom:14px; }
.fg label { display:block; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.4px; margin-bottom:5px; }
.fg input, .fg textarea, .fg select { width:100%; padding:9px 11px; font-size:13px; border:1px solid #d1d5db; border-radius:8px; box-sizing:border-box; font-family:inherit; }
.fg textarea { resize:vertical; min-height:70px; line-height:1.5; }
.fg input:focus, .fg textarea:focus, .fg select:focus { outline:none; border-color:#00b207; box-shadow:0 0 0 3px rgba(0,178,7,.08); }
.fg-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.plat-chips { display:flex; gap:7px; flex-wrap:wrap; }
.plat-chip { border:1px solid #e5e7eb; border-radius:20px; padding:6px 12px; font-size:12px; font-weight:600; cursor:pointer; color:#6b7280; user-select:none; }
.plat-chip.on { background:#f0fdf4; border-color:#00b207; color:#15803d; }

.img-gen { border:1px dashed #d1d5db; border-radius:10px; padding:14px; background:#fafafa; margin-bottom:14px; }
.img-gen .gen-row { display:flex; gap:8px; }
.img-gen input { flex:1; }
.btn-img { background:#0a0e27; color:#fff; border:none; border-radius:8px; padding:9px 14px; font-size:13px; font-weight:600; cursor:pointer; white-space:nowrap; }
.btn-img:hover { background:#1a1f3a; } .btn-img:disabled { background:#9ca3af; cursor:not-allowed; }
.img-preview { margin-top:12px; text-align:center; }
.img-preview img { max-width:100%; max-height:240px; border-radius:8px; border:1px solid #e5e7eb; }
.img-tools { margin-top:8px; display:flex; gap:8px; justify-content:center; }
.img-tools button, .img-tools a { font-size:12px; padding:5px 10px; border-radius:6px; border:1px solid #e5e7eb; background:#fff; color:#4b5563; cursor:pointer; text-decoration:none; }
.btn-video { background:#fff; color:#7c3aed; border:1px solid #ddd6fe; }

.save-bar { display:flex; gap:10px; margin-top:6px; }
.btn-save { flex:1; background:#00b207; color:#fff; border:none; border-radius:8px; padding:11px; font-size:14px; font-weight:600; cursor:pointer; }
.btn-save:hover { background:#009906; }
.btn-reset { background:#fff; color:#9ca3af; border:1px solid #e5e7eb; border-radius:8px; padding:11px 16px; font-size:13px; cursor:pointer; }

.cc-note { font-size:11px; color:#9ca3af; margin-top:4px; }
.cc-toast { position:fixed; bottom:26px; left:50%; transform:translateX(-50%) translateY(70px); background:#111827; color:#fff; padding:11px 22px; border-radius:30px; font-size:13px; font-weight:600; opacity:0; transition:.25s; z-index:1000; }
.cc-toast.show { transform:translateX(-50%) translateY(0); opacity:1; }
.gen-spin { display:inline-block; width:14px; height:14px; border:2px solid rgba(255,255,255,.4); border-top-color:#fff; border-radius:50%; animation:ccspin .7s linear infinite; }
@keyframes ccspin { to { transform:rotate(360deg); } }
</style>

<div class="cc-header">
  <h1><i class="fa-solid fa-comments" style="color:#00b207;margin-right:8px;"></i><?= $pageTitle ?></h1>
  <p><?= $fr ? 'Discutez pour creer un post, generez une image, puis enregistrez dans la bibliotheque.' : 'Chat to create a post, generate an image, then save it to the library.' ?></p>
</div>

<div class="cc-grid">

  <!-- LEFT: CHAT -->
  <div class="cc-card">
    <div class="cc-card-head">
      <h2><i class="fa-solid fa-wand-magic-sparkles" style="color:#00b207;"></i> <?= $fr ? 'Assistant de redaction' : 'Writing Assistant' ?></h2>
      <button onclick="resetChat()" class="quick-chip"><i class="fa-solid fa-rotate-left"></i> <?= $fr ? 'Nouveau' : 'New' ?></button>
    </div>
    <div class="chat-wrap">
      <div class="chat-log" id="chatLog">
        <div class="chat-hint"><?= $fr
          ? 'Decrivez le post que vous voulez. Ex : "Post Instagram pour recruter des livreurs a velo dans l\'Ouest-de-l\'Ile, ton energique, EN et FR."'
          : 'Describe the post you want. e.g. "Instagram post to recruit e-bike drivers in the West Island, energetic tone, EN and FR."' ?></div>
      </div>
      <div class="quick-row" id="quickRow">
        <span class="quick-chip" onclick="quick(this)"><?= $fr ? 'Promo Tot-le-Matin' : 'Early Bird promo' ?></span>
        <span class="quick-chip" onclick="quick(this)"><?= $fr ? 'Recrutement livreurs' : 'Driver recruitment' ?></span>
        <span class="quick-chip" onclick="quick(this)"><?= $fr ? 'Vendeur a la une' : 'Seller spotlight' ?></span>
        <span class="quick-chip" onclick="quick(this)"><?= $fr ? 'Livraison zero emission' : 'Zero-emission delivery' ?></span>
      </div>
      <div class="chat-input">
        <textarea id="chatBox" rows="1" placeholder="<?= $fr ? 'Ecrivez votre message...' : 'Type your message...' ?>"></textarea>
        <button class="chat-send" id="chatSend" onclick="sendChat()"><i class="fa-solid fa-paper-plane"></i></button>
      </div>
    </div>
  </div>

  <!-- RIGHT: COMPOSE & SAVE -->
  <div class="cc-card">
    <div class="cc-card-head">
      <h2><i class="fa-solid fa-pen-to-square" style="color:#00b207;"></i> <?= $fr ? 'Composer et enregistrer' : 'Compose & Save' ?></h2>
    </div>
    <div class="compose-body">
      <input type="hidden" id="fId" value="">
      <input type="hidden" id="fGeneratedBy" value="manual">

      <div class="fg">
        <label><?= $fr ? 'Titre interne' : 'Internal title' ?></label>
        <input id="fTitle" placeholder="<?= $fr ? 'Ex : Promo Tot-le-Matin - semaine 1' : 'e.g. Early Bird promo - week 1' ?>">
      </div>

      <div class="fg">
        <label><?= $fr ? 'Plateformes' : 'Platforms' ?></label>
        <div class="plat-chips" id="platChips">
          <?php foreach (['Instagram','Facebook','LinkedIn','TikTok','X/Twitter'] as $p): ?>
            <span class="plat-chip" data-p="<?= $p ?>" onclick="this.classList.toggle('on')"><?= $p ?></span>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Image generator -->
      <div class="img-gen">
        <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">
          <i class="fa-solid fa-image"></i> <?= $fr ? 'Generer une image (Nano Banana)' : 'Generate image (Nano Banana)' ?>
        </label>
        <div class="gen-row">
          <input id="imgPrompt" placeholder="<?= $fr ? 'Decrivez l\'image...' : 'Describe the image...' ?>">
          <button class="btn-img" id="btnImg" onclick="genImage()"><i class="fa-solid fa-sparkles"></i> <span id="imgLabel"><?= $fr ? 'Generer' : 'Generate' ?></span></button>
        </div>
        <div class="img-preview" id="imgPreview" style="display:none;">
          <img id="imgEl" src="" alt="">
          <div class="img-tools">
            <a id="imgOpen" href="#" target="_blank"><i class="fa-solid fa-up-right-from-square"></i> <?= $fr ? 'Ouvrir' : 'Open' ?></a>
            <button onclick="clearImage()"><i class="fa-solid fa-xmark"></i> <?= $fr ? 'Retirer' : 'Remove' ?></button>
            <button class="btn-video" onclick="genVideo()"><i class="fa-solid fa-film"></i> <?= $fr ? 'Video (Veo 3)' : 'Video (Veo 3)' ?></button>
          </div>
        </div>
        <input type="hidden" id="fImagePath" value="">
      </div>

      <div class="fg">
        <label>Caption (EN)</label>
        <textarea id="fCaptionEn" placeholder="English caption..."></textarea>
      </div>
      <div class="fg">
        <label>Caption (FR)</label>
        <textarea id="fCaptionFr" placeholder="Texte en francais..."></textarea>
      </div>
      <div class="fg">
        <label>Hashtags</label>
        <input id="fHashtags" placeholder="#OCSAPP #WestIsland #ShopLocal">
      </div>

      <div class="fg-row">
        <div class="fg">
          <label><?= $fr ? 'Statut' : 'Status' ?></label>
          <select id="fStatus">
            <option value="idea"><?= $fr ? 'Idee' : 'Idea' ?></option>
            <option value="approved"><?= $fr ? 'Approuve' : 'Approved' ?></option>
            <option value="scheduled"><?= $fr ? 'En file' : 'Scheduled' ?></option>
            <option value="posted"><?= $fr ? 'Publie' : 'Posted' ?></option>
          </select>
        </div>
        <div class="fg">
          <label><?= $fr ? 'Date de publication' : 'Post date' ?></label>
          <input type="date" id="fPostDate">
        </div>
      </div>

      <div class="save-bar">
        <button class="btn-save" onclick="savePost()"><i class="fa-solid fa-floppy-disk"></i> <?= $fr ? 'Enregistrer dans la bibliotheque' : 'Save to Library' ?></button>
        <button class="btn-reset" onclick="resetCompose()"><?= $fr ? 'Vider' : 'Clear' ?></button>
      </div>
      <p class="cc-note"><?= $fr ? 'Astuce : cliquez "Utiliser ce brouillon" sous une reponse pour remplir ces champs automatiquement.' : 'Tip: click "Use this draft" under a reply to auto-fill these fields.' ?></p>
    </div>
  </div>

</div>

<div class="cc-toast" id="ccToast"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const URL_CHAT  = '<?= url('admin/content/chat') ?>';
const URL_IMG   = '<?= url('admin/content/generate-image') ?>';
const URL_VIDEO = '<?= url('admin/content/generate-video') ?>';
const URL_SAVE  = '<?= url('admin/content/save') ?>';
const URL_LIB   = '<?= url('admin/content/library') ?>';
const FR = <?= $fr ? 'true' : 'false' ?>;

let history = [];

/* ---- helpers ---- */
function post(url, body){
  return fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(body)}).then(r=>r.json());
}
function toast(m){ const t=document.getElementById('ccToast'); t.textContent=m; t.classList.add('show'); clearTimeout(t._x); t._x=setTimeout(()=>t.classList.remove('show'),2400); }
function esc(s){ return (s||'').replace(/[&<>]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c])); }
/* Lightweight, XSS-safe markdown for bot replies: escape first, then only ever inject known tags.
   Line breaks + numbered/bullet lists already render via white-space:pre-wrap. */
function mdInline(s){
  let h = esc(s);
  h = h.replace(/`([^`\n]+)`/g, '<code>$1</code>');                 // `inline code`
  h = h.replace(/^\s{0,3}#{1,6}\s+(.+)$/gm, '<strong>$1</strong>'); // # heading -> bold line
  h = h.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');       // **bold**
  h = h.replace(/__([^_\n]+)__/g, '<strong>$1</strong>');           // __bold__
  h = h.replace(/(^|[^*])\*([^*\n]+)\*(?!\*)/g, '$1<em>$2</em>');    // *italic*
  return h;
}

/* ---- chat ---- */
const box = document.getElementById('chatBox');
box.addEventListener('input', ()=>{ box.style.height='auto'; box.style.height=Math.min(box.scrollHeight,120)+'px'; });
box.addEventListener('keydown', e=>{ if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendChat(); } });

function quick(el){ box.value = el.textContent.trim(); box.focus(); }

function addMsg(role, text){
  const log = document.getElementById('chatLog');
  const hint = log.querySelector('.chat-hint'); if(hint) hint.remove();
  const d = document.createElement('div');
  d.className = 'msg ' + (role==='user'?'user':'bot');
  d.innerHTML = role==='bot' ? mdInline(text) : esc(text);
  if(role==='bot' && /---\s*EN\s*---|TITLE\s*:/i.test(text)){
    const btn = document.createElement('button');
    btn.className='use-draft';
    btn.innerHTML='<i class="fa-solid fa-arrow-right-to-bracket"></i> '+(FR?'Utiliser ce brouillon':'Use this draft');
    btn.onclick = ()=>useDraft(text);
    d.appendChild(document.createElement('br'));
    d.appendChild(btn);
  }
  log.appendChild(d);
  log.scrollTop = log.scrollHeight;
  return d;
}

function sendChat(){
  const text = box.value.trim(); if(!text) return;
  box.value=''; box.style.height='auto';
  addMsg('user', text);
  history.push({role:'user', content:text});

  const log = document.getElementById('chatLog');
  const t = document.createElement('div'); t.className='typing'; t.innerHTML='<span class="dot"></span><span class="dot"></span><span class="dot"></span>';
  log.appendChild(t); log.scrollTop=log.scrollHeight;
  document.getElementById('chatSend').disabled=true;

  post(URL_CHAT, {messages:history}).then(d=>{
    t.remove(); document.getElementById('chatSend').disabled=false;
    if(!d.success){ addMsg('bot', '⚠ '+(d.error||'Error')); return; }
    addMsg('bot', d.content);
    history.push({role:'assistant', content:d.content});
  }).catch(()=>{ t.remove(); document.getElementById('chatSend').disabled=false; addMsg('bot','⚠ '+(FR?'Echec de la requete.':'Request failed.')); });
}

function resetChat(){ history=[]; document.getElementById('chatLog').innerHTML='<div class="chat-hint">'+(FR?'Nouveau brouillon. Decrivez le post voulu.':'Fresh start. Describe the post you want.')+'</div>'; }

/* ---- parse a structured draft into the form ---- */
function useDraft(text){
  const grab = (re)=>{ const m=text.match(re); return m?m[1].trim():''; };
  const title = grab(/TITLE\s*:\s*(.+)/i);
  let en = grab(/---\s*EN\s*---\s*([\s\S]*?)(?:---\s*FR\s*---|HASHTAGS\s*:|$)/i);
  let fr = grab(/---\s*FR\s*---\s*([\s\S]*?)(?:HASHTAGS\s*:|$)/i);
  const tags = grab(/HASHTAGS\s*:\s*(.+)/i);
  if(title) document.getElementById('fTitle').value = title;
  if(en) document.getElementById('fCaptionEn').value = en.trim();
  if(fr) document.getElementById('fCaptionFr').value = fr.trim();
  if(tags) document.getElementById('fHashtags').value = tags;
  document.getElementById('fGeneratedBy').value = 'claude';
  toast(FR?'Brouillon insere':'Draft loaded');
  document.querySelector('.compose-body').scrollTop = 0;
}

/* ---- image generation ---- */
function genImage(){
  const prompt = document.getElementById('imgPrompt').value.trim();
  if(!prompt){ toast(FR?'Decrivez l\'image':'Describe the image'); return; }
  const btn=document.getElementById('btnImg'); btn.disabled=true;
  document.getElementById('imgLabel').innerHTML='<span class="gen-spin"></span>';
  post(URL_IMG, {prompt}).then(d=>{
    btn.disabled=false; document.getElementById('imgLabel').textContent = FR?'Generer':'Generate';
    if(!d.success){ toast(d.error||'Error'); return; }
    document.getElementById('imgEl').src = d.url;
    document.getElementById('imgOpen').href = d.url;
    document.getElementById('fImagePath').value = d.path;
    document.getElementById('imgPreview').style.display='block';
    document.getElementById('fGeneratedBy').value = 'claude+gemini';
  }).catch(()=>{ btn.disabled=false; document.getElementById('imgLabel').textContent=FR?'Generer':'Generate'; toast(FR?'Echec':'Failed'); });
}
function clearImage(){ document.getElementById('imgPreview').style.display='none'; document.getElementById('fImagePath').value=''; document.getElementById('imgEl').src=''; }

function genVideo(){
  post(URL_VIDEO, {}).then(d=>{ toast(d.error || 'Veo 3 - Phase 2'); });
}

/* ---- save ---- */
function savePost(){
  const platforms = [...document.querySelectorAll('.plat-chip.on')].map(c=>c.dataset.p);
  const data = {
    id: document.getElementById('fId').value || 0,
    title: document.getElementById('fTitle').value,
    caption_en: document.getElementById('fCaptionEn').value,
    caption_fr: document.getElementById('fCaptionFr').value,
    hashtags: document.getElementById('fHashtags').value,
    platforms, status: document.getElementById('fStatus').value,
    post_date: document.getElementById('fPostDate').value,
    image_path: document.getElementById('fImagePath').value,
    generated_by: document.getElementById('fGeneratedBy').value
  };
  if(!data.caption_en.trim() && !data.caption_fr.trim()){ toast(FR?'Ajoutez une caption (EN ou FR)':'Add a caption (EN or FR)'); return; }
  post(URL_SAVE, data).then(d=>{
    if(!d.success){ toast(d.error||'Error'); return; }
    toast(FR?'Enregistre dans la bibliotheque ✓':'Saved to library ✓');
    setTimeout(()=>{ if(confirm(FR?'Ouvrir la bibliotheque ?':'Open the library?')) window.location.href=URL_LIB; }, 400);
    resetCompose();
  });
}

function resetCompose(){
  ['fId','fTitle','fCaptionEn','fCaptionFr','fHashtags','fPostDate','fImagePath','imgPrompt'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('fStatus').value='idea';
  document.getElementById('fGeneratedBy').value='manual';
  document.querySelectorAll('.plat-chip.on').forEach(c=>c.classList.remove('on'));
  clearImage();
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
