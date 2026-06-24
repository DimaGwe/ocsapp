<?php
$activePage  = 'marketing';
$currentPage = 'marketing';
$currentLang = $_SESSION['language'] ?? 'fr';

$t = [
    'en' => [
        'page_title'    => 'Marketing Generator',
        'subtitle'      => 'AI-powered content for all your platforms',
        'platform'      => 'Platform',
        'content_type'  => 'Content Type',
        'language'      => 'Language',
        'tone'          => 'Tone',
        'context'       => 'What to promote',
        'context_ph'    => "Describe what you're promoting. Example: \"Fatima's handmade soy candles — 20% off this weekend only. Available on OCSAPP.\"",
        'generate'      => 'Generate Content',
        'generating'    => 'Generating...',
        'copy'          => 'Copy',
        'copied'        => 'Copied!',
        'regenerate'    => 'Regenerate',
        'result_title'  => 'Generated Content',
        'clear'         => 'Clear',
        'tip'           => 'Tip: Be specific in your context. The more detail you give, the better the output.',
        // Platforms
        'instagram'     => 'Instagram',
        'facebook'      => 'Facebook',
        'tiktok'        => 'TikTok',
        'linkedin'      => 'LinkedIn',
        'email'         => 'Email',
        // Content types
        'product_spotlight' => 'Product Spotlight',
        'seller_spotlight'  => 'Seller Spotlight',
        'promo_deal'        => 'Promo / Deal',
        'b2b_tip'           => 'B2B Tip',
        'behind_scenes'     => 'Behind the Scenes',
        'driver_recruitment'=> 'Driver Recruitment',
        'seller_recruitment'=> 'Seller Recruitment',
        // Languages
        'lang_en'   => 'English only',
        'lang_fr'   => 'French only (fr-CA)',
        'lang_both' => 'Both (EN + FR)',
        // Tones
        'tone_friendly'     => 'Friendly',
        'tone_professional' => 'Professional',
        'tone_urgent'       => 'Urgent',
    ],
    'fr' => [
        'page_title'    => 'Générateur Marketing',
        'subtitle'      => 'Contenu IA pour toutes vos plateformes',
        'platform'      => 'Plateforme',
        'content_type'  => 'Type de contenu',
        'language'      => 'Langue',
        'tone'          => 'Ton',
        'context'       => 'Quoi promouvoir',
        'context_ph'    => "Décrivez ce que vous promouvez. Exemple : \"Bougies artisanales de Fatima - 20 % de rabais ce weekend seulement. Disponible sur OCSAPP.\"",
        'generate'      => 'Générer le contenu',
        'generating'    => 'Génération en cours...',
        'copy'          => 'Copier',
        'copied'        => 'Copié !',
        'regenerate'    => 'Régénérer',
        'result_title'  => 'Contenu généré',
        'clear'         => 'Effacer',
        'tip'           => 'Conseil : Soyez précis dans votre contexte. Plus vous donnez de détails, meilleur sera le résultat.',
        // Platforms
        'instagram'     => 'Instagram',
        'facebook'      => 'Facebook',
        'tiktok'        => 'TikTok',
        'linkedin'      => 'LinkedIn',
        'email'         => 'Courriel',
        // Content types
        'product_spotlight' => 'Mise en avant produit',
        'seller_spotlight'  => 'Vendeur à la une',
        'promo_deal'        => 'Promo / Rabais',
        'b2b_tip'           => 'Conseil B2B',
        'behind_scenes'     => 'Dans les coulisses',
        'driver_recruitment'=> 'Recrutement livreurs',
        'seller_recruitment'=> 'Recrutement vendeurs',
        // Languages
        'lang_en'   => 'Anglais seulement',
        'lang_fr'   => 'Français seulement (fr-CA)',
        'lang_both' => 'Les deux (EN + FR)',
        // Tones
        'tone_friendly'     => 'Amical',
        'tone_professional' => 'Professionnel',
        'tone_urgent'       => 'Urgent',
    ],
];
$t         = $t[$currentLang] ?? $t['en'];
$pageTitle = $t['page_title'];
$generateUrl = url('admin/marketing/generate');

ob_start();
?>
<style>
.mkt-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 28px;
    gap: 12px;
}
.mkt-header h1 { font-size: 22px; font-weight: 700; color: #111827; margin: 0 0 4px; }
.mkt-header p  { font-size: 13px; color: #6b7280; margin: 0; }

.mkt-grid {
    display: grid;
    grid-template-columns: 420px 1fr;
    gap: 24px;
    align-items: start;
}
@media (max-width: 900px) {
    .mkt-grid { grid-template-columns: 1fr; }
}

.mkt-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
}
.mkt-card h2 {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin: 0 0 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group { margin-bottom: 16px; }
.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.form-control {
    width: 100%;
    padding: 9px 12px;
    font-size: 13px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    color: #111827;
    background: #fff;
    box-sizing: border-box;
    transition: border-color .15s;
}
.form-control:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,.08); }
textarea.form-control { resize: vertical; min-height: 110px; line-height: 1.5; }

.mkt-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.btn-generate {
    width: 100%;
    padding: 11px 20px;
    background: #00b207;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background .15s, transform .1s;
    margin-top: 20px;
}
.btn-generate:hover  { background: #009906; }
.btn-generate:active { transform: scale(.98); }
.btn-generate:disabled { background: #9ca3af; cursor: not-allowed; transform: none; }

.tip-box {
    margin-top: 14px;
    padding: 10px 12px;
    background: #f0fdf4;
    border-left: 3px solid #00b207;
    border-radius: 0 6px 6px 0;
    font-size: 12px;
    color: #166534;
}

/* Result panel */
.result-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    color: #9ca3af;
    text-align: center;
    gap: 12px;
}
.result-empty i { font-size: 36px; color: #d1d5db; }
.result-empty p { font-size: 13px; margin: 0; }

.result-content { display: none; }

.result-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}
.result-toolbar h2 {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.result-meta {
    display: flex;
    gap: 6px;
    align-items: center;
    flex-wrap: wrap;
}
.result-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 20px;
    background: #f3f4f6;
    color: #374151;
}
.result-badge.green { background: #dcfce7; color: #166534; }

.output-box {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 18px;
    font-size: 13.5px;
    line-height: 1.7;
    color: #1f2937;
    white-space: pre-wrap;
    word-break: break-word;
    min-height: 200px;
}

.result-actions {
    display: flex;
    gap: 8px;
    margin-top: 14px;
    flex-wrap: wrap;
}
.btn-copy, .btn-regen, .btn-clear-result {
    padding: 8px 16px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    border: none;
    transition: background .15s;
}
.btn-copy  { background: #00b207; color: #fff; }
.btn-copy:hover  { background: #009906; }
.btn-copy.copied { background: #059669; }
.btn-regen { background: #f3f4f6; color: #374151; }
.btn-regen:hover { background: #e5e7eb; }
.btn-clear-result { background: #fff; color: #9ca3af; border: 1px solid #e5e7eb; }
.btn-clear-result:hover { color: #ef4444; border-color: #fca5a5; background: #fff5f5; }

/* Spinner */
.spinner-wrap {
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    gap: 16px;
    color: #6b7280;
    font-size: 13px;
}
.spinner {
    width: 36px; height: 36px;
    border: 3px solid #e5e7eb;
    border-top-color: #00b207;
    border-radius: 50%;
    animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.error-box {
    display: none;
    padding: 12px 16px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    color: #b91c1c;
    font-size: 13px;
    align-items: center;
    gap: 8px;
}
</style>

<div class="mkt-header">
    <div>
        <h1><i class="fa-solid fa-wand-magic-sparkles" style="color:#00b207;margin-right:8px;"></i><?= $t['page_title'] ?></h1>
        <p><?= $t['subtitle'] ?></p>
    </div>
</div>

<div class="mkt-grid">

    <!-- LEFT: Form -->
    <div class="mkt-card">
        <h2><i class="fa-solid fa-sliders"></i> <?= $currentLang === 'fr' ? 'Paramètres' : 'Settings' ?></h2>

        <form id="mktForm">

            <div class="form-group">
                <label><?= $t['platform'] ?></label>
                <select name="platform" id="fPlatform" class="form-control" required>
                    <option value=""><?= $currentLang === 'fr' ? '-- Choisir --' : '-- Select --' ?></option>
                    <option value="Instagram"><?= $t['instagram'] ?></option>
                    <option value="Facebook"><?= $t['facebook'] ?></option>
                    <option value="TikTok"><?= $t['tiktok'] ?></option>
                    <option value="LinkedIn"><?= $t['linkedin'] ?></option>
                    <option value="Email"><?= $t['email'] ?></option>
                </select>
            </div>

            <div class="form-group">
                <label><?= $t['content_type'] ?></label>
                <select name="content_type" id="fContentType" class="form-control" required>
                    <option value=""><?= $currentLang === 'fr' ? '-- Choisir --' : '-- Select --' ?></option>
                    <option value="product spotlight"><?= $t['product_spotlight'] ?></option>
                    <option value="seller spotlight"><?= $t['seller_spotlight'] ?></option>
                    <option value="promotional deal"><?= $t['promo_deal'] ?></option>
                    <option value="B2B tip"><?= $t['b2b_tip'] ?></option>
                    <option value="behind the scenes"><?= $t['behind_scenes'] ?></option>
                    <option value="driver recruitment"><?= $t['driver_recruitment'] ?></option>
                    <option value="seller recruitment"><?= $t['seller_recruitment'] ?></option>
                </select>
            </div>

            <div class="mkt-row">
                <div class="form-group">
                    <label><?= $t['language'] ?></label>
                    <select name="language" id="fLanguage" class="form-control">
                        <option value="en"><?= $t['lang_en'] ?></option>
                        <option value="fr"><?= $t['lang_fr'] ?></option>
                        <option value="both" <?= $currentLang === 'fr' ? 'selected' : '' ?>><?= $t['lang_both'] ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label><?= $t['tone'] ?></label>
                    <select name="tone" id="fTone" class="form-control">
                        <option value="friendly"><?= $t['tone_friendly'] ?></option>
                        <option value="professional"><?= $t['tone_professional'] ?></option>
                        <option value="urgent"><?= $t['tone_urgent'] ?></option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label><?= $t['context'] ?> <span style="color:#ef4444;">*</span></label>
                <textarea name="context" id="fContext" class="form-control" placeholder="<?= htmlspecialchars($t['context_ph']) ?>" required></textarea>
            </div>

            <div class="tip-box"><i class="fa-solid fa-lightbulb" style="margin-right:6px;"></i><?= $t['tip'] ?></div>

            <button type="submit" class="btn-generate" id="btnGenerate">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span id="btnLabel"><?= $t['generate'] ?></span>
            </button>

        </form>
    </div>

    <!-- RIGHT: Output -->
    <div class="mkt-card" id="resultCard">

        <!-- Error -->
        <div class="error-box" id="errorBox">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span id="errorMsg"></span>
        </div>

        <!-- Spinner -->
        <div class="spinner-wrap" id="spinnerWrap">
            <div class="spinner"></div>
            <span><?= $currentLang === 'fr' ? 'Génération en cours...' : 'Generating content...' ?></span>
        </div>

        <!-- Empty state -->
        <div class="result-empty" id="emptyState">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <p><?= $currentLang === 'fr' ? 'Remplissez le formulaire et cliquez sur Générer.' : 'Fill in the form and click Generate.' ?></p>
        </div>

        <!-- Result -->
        <div class="result-content" id="resultContent">
            <div class="result-toolbar">
                <h2><i class="fa-solid fa-check-circle" style="color:#00b207;"></i> <?= $t['result_title'] ?></h2>
                <div class="result-meta" id="resultMeta"></div>
            </div>
            <div class="output-box" id="outputBox"></div>
            <div class="result-actions">
                <button class="btn-copy" id="btnCopy" onclick="copyOutput()">
                    <i class="fa-regular fa-copy"></i> <?= $t['copy'] ?>
                </button>
                <button class="btn-regen" id="btnRegen" onclick="regenerate()">
                    <i class="fa-solid fa-rotate"></i> <?= $t['regenerate'] ?>
                </button>
                <button class="btn-clear-result" onclick="clearResult()">
                    <i class="fa-solid fa-xmark"></i> <?= $t['clear'] ?>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
const GENERATE_URL = '<?= $generateUrl ?>';
const TXT_COPY     = '<?= $t['copy'] ?>';
const TXT_COPIED   = '<?= $t['copied'] ?>';
const TXT_GEN      = '<?= $t['generate'] ?>';
const TXT_GENNING  = '<?= $t['generating'] ?>';

document.getElementById('mktForm').addEventListener('submit', function(e) {
    e.preventDefault();
    generate();
});

function generate() {
    const form = document.getElementById('mktForm');
    const fd   = new FormData(form);

    setLoading(true);
    hideError();

    fetch(GENERATE_URL, { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            setLoading(false);
            if (!data.success) {
                showError(data.error || 'Unknown error');
                return;
            }
            showResult(data.content, fd);
        })
        .catch(function(err) {
            setLoading(false);
            showError('Request failed. Check your connection.');
        });
}

function regenerate() {
    generate();
}

function showResult(content, fd) {
    document.getElementById('emptyState').style.display  = 'none';
    document.getElementById('resultContent').style.display = 'block';
    document.getElementById('outputBox').textContent = content;

    const meta = document.getElementById('resultMeta');
    meta.innerHTML = '';
    [fd.get('platform'), fd.get('content_type'), fd.get('language').toUpperCase()].forEach(function(v) {
        if (v) {
            const b = document.createElement('span');
            b.className = 'result-badge green';
            b.textContent = v;
            meta.appendChild(b);
        }
    });

    // Reset copy button
    const btnCopy = document.getElementById('btnCopy');
    btnCopy.innerHTML = '<i class="fa-regular fa-copy"></i> ' + TXT_COPY;
    btnCopy.classList.remove('copied');
}

function copyOutput() {
    const text = document.getElementById('outputBox').textContent;
    navigator.clipboard.writeText(text).then(function() {
        const btn = document.getElementById('btnCopy');
        btn.innerHTML = '<i class="fa-solid fa-check"></i> ' + TXT_COPIED;
        btn.classList.add('copied');
        setTimeout(function() {
            btn.innerHTML = '<i class="fa-regular fa-copy"></i> ' + TXT_COPY;
            btn.classList.remove('copied');
        }, 2000);
    });
}

function clearResult() {
    document.getElementById('resultContent').style.display = 'none';
    document.getElementById('emptyState').style.display    = 'flex';
}

function setLoading(on) {
    document.getElementById('spinnerWrap').style.display   = on ? 'flex'  : 'none';
    document.getElementById('emptyState').style.display    = on ? 'none'  : 'flex';
    document.getElementById('resultContent').style.display = 'none';
    document.getElementById('btnGenerate').disabled        = on;
    document.getElementById('btnLabel').textContent        = on ? TXT_GENNING : TXT_GEN;
}

function showError(msg) {
    const box = document.getElementById('errorBox');
    document.getElementById('errorMsg').textContent = msg;
    box.style.display = 'flex';
    document.getElementById('spinnerWrap').style.display   = 'none';
    document.getElementById('emptyState').style.display    = 'flex';
}

function hideError() {
    document.getElementById('errorBox').style.display = 'none';
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
