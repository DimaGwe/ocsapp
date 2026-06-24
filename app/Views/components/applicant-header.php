<?php
$currentLang  = $_SESSION['language'] ?? 'fr';
$_ahFr        = ($currentLang === 'fr');
$_ahBadge     = $_ahFr ? 'Candidature livreur' : 'Driver Application';
$_ahSignOut   = $_ahFr ? 'Déconnexion' : 'Sign out';
$_ahLangLabel = $_ahFr ? 'EN' : 'FR';
$_ahLangVal   = $_ahFr ? 'en' : 'fr';
?>
<header style="
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 60px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
">
    <a href="<?= url('/') ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
        <img src="<?= asset('images/logo.png') ?>" alt="OCSAPP" style="height:32px;">
        <span style="font-family:'Inter','Segoe UI',sans-serif;font-weight:700;font-size:16px;color:#1f2937;">OCSAPP</span>
    </a>

    <span style="
        font-family:'Inter','Segoe UI',sans-serif;
        font-size:12px;
        font-weight:600;
        color:#6b7280;
        background:#f3f4f6;
        border:1px solid #e5e7eb;
        border-radius:20px;
        padding:4px 14px;
        letter-spacing:0.3px;
    "><?= $_ahBadge ?></span>

    <div style="display:flex;align-items:center;gap:14px;">

        <!-- Language toggle -->
        <button type="button"
            onclick="(function(btn){
                btn.disabled=true;
                fetch('<?= url('set-language') ?>',{
                    method:'POST',
                    headers:{'Content-Type':'application/x-www-form-urlencoded'},
                    body:'language=<?= $_ahLangVal ?>&<?= env('CSRF_TOKEN_NAME','_csrf_token') ?>=<?= generateCsrfToken() ?>'
                }).then(function(r){return r.json();}).then(function(d){if(d.success)location.reload();else btn.disabled=false;}).catch(function(){btn.disabled=false;});
            })(this)"
            style="
                background:none;
                border:1px solid #e5e7eb;
                color:#6b7280;
                font-family:'Inter','Segoe UI',sans-serif;
                font-size:12px;
                font-weight:700;
                padding:5px 12px;
                border-radius:8px;
                cursor:pointer;
                letter-spacing:0.5px;
                transition:all 0.15s;
            " onmouseover="this.style.borderColor='#9ca3af';this.style.color='#374151';"
               onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#6b7280';"
               title="<?= $_ahFr ? 'Switch to English' : 'Passer en français' ?>">
            <?= $_ahLangLabel ?>
        </button>

        <?php if (!empty($_SESSION['user']['first_name'])): ?>
            <span style="font-family:'Inter','Segoe UI',sans-serif;font-size:13px;color:#6b7280;">
                <?= htmlspecialchars($_SESSION['user']['first_name']) ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($_SESSION['user'])): ?>
            <form method="POST" action="<?= url('logout') ?>" style="margin:0;">
                <?= csrfField() ?>
                <button type="submit" style="
                    background: none;
                    border: 1px solid #e5e7eb;
                    color: #6b7280;
                    font-family:'Inter','Segoe UI',sans-serif;
                    font-size:13px;
                    font-weight:500;
                    padding:6px 14px;
                    border-radius:8px;
                    cursor:pointer;
                    display:flex;
                    align-items:center;
                    gap:6px;
                    transition:all 0.15s;
                " onmouseover="this.style.borderColor='#9ca3af';this.style.color='#374151';"
                   onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#6b7280';">
                    <i class="fa-solid fa-right-from-bracket" style="font-size:12px;"></i>
                    <?= $_ahSignOut ?>
                </button>
            </form>
        <?php endif; ?>
    </div>
</header>
