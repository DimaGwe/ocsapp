<?php
/**
 * OCSAPP Waitlist - Public Landing Page
 * Bilingual FR/EN, role-aware, referral tracking
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = $currentLang === 'fr';

$ref    = htmlspecialchars($ref    ?? '', ENT_QUOTES);
$joined = !empty($joined);
$pos    = (int) ($pos    ?? 0);
$myRef  = htmlspecialchars($myRef  ?? '', ENT_QUOTES);
$myRole = htmlspecialchars($myRole ?? '', ENT_QUOTES);

$refUrl = $myRef ? (rtrim(url('/waitlist'), '/') . '?ref=' . $myRef) : '';

$roleLabels = [
    'buyer'    => $fr ? 'Acheteur'            : 'Buyer',
    'seller'   => $fr ? 'Vendeur'             : 'Seller',
    'supplier' => $fr ? 'Fournisseur'         : 'Supplier',
    'driver'   => $fr ? 'Livreur'             : 'Driver',
    'business' => $fr ? 'Client Distribution' : 'Business Client',
];
$myRoleLabel = $roleLabels[$myRole] ?? '';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? 'Liste d\'attente - OCSAPP' : 'Waitlist - OCSAPP' ?></title>
  <?= csrfMeta() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    :root {
      --green:      #00b207;
      --green-dark: #007a05;
      --neon:       #00ff88;
      --text:       #374151;
      --muted:      #6b7280;
      --border:     #e5e7eb;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: var(--text); }
    a { text-decoration: none; }
    .container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }

    /* Hero */
    .hero {
      background: linear-gradient(135deg, #0a1a0a 0%, #0d2b10 60%, #0a1a0a 100%);
      padding: 100px 24px 80px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .hero::before {
      content: '';
      position: absolute; inset: 0;
      background: radial-gradient(ellipse at center top, rgba(0,255,136,.08) 0%, transparent 65%);
      pointer-events: none;
    }
    .eyebrow {
      display: inline-block; font-size: 11px; font-weight: 700;
      letter-spacing: 1.8px; text-transform: uppercase;
      padding: 5px 16px; border-radius: 20px; margin-bottom: 20px;
      background: rgba(0,255,136,.12); border: 1px solid rgba(0,255,136,.25);
      color: var(--neon);
    }
    .hero h1 {
      font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800;
      color: #fff; line-height: 1.15; margin-bottom: 20px;
    }
    .hero h1 span { color: var(--neon); }
    .hero p {
      font-size: 1.15rem; color: rgba(255,255,255,.72);
      max-width: 560px; margin: 0 auto 40px; line-height: 1.7;
    }

    /* Form card */
    .form-card {
      background: #fff;
      border-radius: 16px;
      padding: 40px;
      max-width: 520px;
      margin: 0 auto;
      box-shadow: 0 24px 64px rgba(0,0,0,.3);
    }
    .form-card h2 {
      font-size: 1.4rem; font-weight: 700; color: #111;
      margin-bottom: 8px;
    }
    .form-card .sub {
      font-size: .9rem; color: var(--muted); margin-bottom: 28px;
    }
    .form-group { margin-bottom: 18px; }
    .form-group label {
      display: block; font-size: .85rem; font-weight: 600;
      color: #374151; margin-bottom: 6px;
    }
    .form-group input,
    .form-group select {
      width: 100%; padding: 11px 14px; border: 1.5px solid var(--border);
      border-radius: 8px; font-size: .95rem; color: #111;
      transition: border-color .2s;
    }
    .form-group input:focus,
    .form-group select:focus { outline: none; border-color: var(--green); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .btn-submit {
      width: 100%; padding: 14px; background: var(--green); color: #fff;
      font-size: 1rem; font-weight: 700; border: none; border-radius: 10px;
      cursor: pointer; transition: background .2s; margin-top: 8px;
    }
    .btn-submit:hover { background: var(--green-dark); }
    .btn-submit:disabled { opacity: .6; cursor: not-allowed; }
    .privacy-note { font-size: .78rem; color: var(--muted); text-align: center; margin-top: 14px; }
    #form-error { display: none; color: #dc2626; font-size: .85rem; margin-top: 10px; text-align: center; }

    /* Success state */
    .success-card {
      background: #fff; border-radius: 16px; padding: 48px 40px;
      max-width: 520px; margin: 0 auto; text-align: center;
      box-shadow: 0 24px 64px rgba(0,0,0,.3);
    }
    .success-icon {
      width: 72px; height: 72px; background: rgba(0,178,7,.12);
      border-radius: 50%; display: flex; align-items: center;
      justify-content: center; margin: 0 auto 20px; font-size: 2rem; color: var(--green);
    }
    .success-card h2 { font-size: 1.6rem; font-weight: 800; color: #111; margin-bottom: 8px; }
    .success-card p { color: var(--muted); font-size: .95rem; margin-bottom: 24px; line-height: 1.6; }
    .position-badge {
      display: inline-block; background: linear-gradient(135deg, var(--green), #00d609);
      color: #fff; font-size: 2rem; font-weight: 900;
      padding: 12px 32px; border-radius: 12px; margin-bottom: 28px;
    }
    .position-badge small { font-size: .9rem; font-weight: 400; display: block; opacity: .85; }

    .ref-box {
      background: #f9fafb; border: 1.5px dashed var(--border);
      border-radius: 10px; padding: 16px 20px; margin-bottom: 20px;
    }
    .ref-box label { font-size: .8rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px; }
    .ref-copy-row { display: flex; gap: 8px; align-items: center; }
    .ref-copy-row input {
      flex: 1; padding: 9px 12px; border: 1.5px solid var(--border);
      border-radius: 8px; font-size: .85rem; background: #fff; color: #111;
    }
    .btn-copy {
      padding: 9px 16px; background: var(--green); color: #fff;
      border: none; border-radius: 8px; font-size: .85rem; font-weight: 600;
      cursor: pointer; white-space: nowrap;
    }
    .btn-copy:hover { background: var(--green-dark); }
    .ref-note { font-size: .78rem; color: var(--muted); margin-top: 8px; }

    /* Stats strip */
    .stats-strip {
      background: #f0fdf4; border-top: 1px solid #d1fae5;
      padding: 36px 24px; text-align: center;
    }
    .stats-strip .inner { display: flex; justify-content: center; gap: 60px; flex-wrap: wrap; }
    .stat-item .num { font-size: 2rem; font-weight: 800; color: var(--green); }
    .stat-item .lbl { font-size: .85rem; color: var(--muted); margin-top: 2px; }

    /* Roles section */
    .roles-section { padding: 64px 24px; }
    .roles-section h2 { text-align: center; font-size: 1.8rem; font-weight: 800; margin-bottom: 8px; }
    .roles-section .subtitle { text-align: center; color: var(--muted); margin-bottom: 40px; }
    .roles-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; max-width: 900px; margin: 0 auto; }
    .role-card {
      background: #fff; border: 1.5px solid var(--border); border-radius: 14px;
      padding: 28px 20px; text-align: center; transition: border-color .2s, box-shadow .2s;
      cursor: default;
    }
    .role-card:hover { border-color: var(--green); box-shadow: 0 4px 20px rgba(0,178,7,.12); }
    .role-card .icon { font-size: 2rem; margin-bottom: 12px; }
    .role-card h3 { font-size: 1rem; font-weight: 700; color: #111; margin-bottom: 6px; }
    .role-card p { font-size: .82rem; color: var(--muted); line-height: 1.5; }

    /* CTA bottom */
    .cta-section {
      background: linear-gradient(135deg, #0d2b10, #0a1a0a);
      padding: 72px 24px; text-align: center;
    }
    .cta-section h2 { font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 16px; }
    .cta-section p { color: rgba(255,255,255,.7); margin-bottom: 32px; max-width: 480px; margin-left: auto; margin-right: auto; }
    .btn-cta {
      display: inline-block; padding: 15px 36px;
      background: var(--neon); color: #0a1a0a;
      font-weight: 800; font-size: 1rem; border-radius: 50px;
    }
    .btn-cta:hover { opacity: .9; }

    footer.footer { margin-top: 0; }
  </style>
</head>
<body>
<?php require __DIR__ . '/../components/header.php'; ?>

<!-- Hero -->
<section class="hero">
  <div class="container">
    <div class="eyebrow"><?= $fr ? 'Lancement bientôt' : 'Launching soon' ?></div>
    <h1>
      <?= $fr
        ? 'Soyez <span>parmi les premiers</span><br>à rejoindre OCSAPP'
        : 'Be <span>among the first</span><br>to join OCSAPP' ?>
    </h1>
    <p>
      <?= $fr
        ? 'La plateforme locale zéro-émission du Grand Montréal. Inscrivez-vous maintenant et obtenez un accès prioritaire au lancement.'
        : 'Greater Montreal\'s local zero-emission platform. Sign up now and get priority access at launch.' ?>
    </p>

    <?php if ($joined): ?>
    <!-- Success card -->
    <div class="success-card">
      <div class="success-icon"><i class="fas fa-check"></i></div>
      <h2><?= $fr ? 'Vous êtes sur la liste !' : "You're on the list!" ?></h2>
      <?php if ($myRoleLabel): ?>
        <p style="margin-bottom:8px;">
          <?= $fr ? "Inscrit en tant que : <strong>{$myRoleLabel}</strong>" : "Registered as: <strong>{$myRoleLabel}</strong>" ?>
        </p>
      <?php endif; ?>
      <?php if ($pos > 0): ?>
      <div class="position-badge">
        #<?= $pos ?>
        <small><?= $fr ? 'votre position' : 'your position' ?></small>
      </div>
      <?php endif; ?>
      <p><?= $fr
        ? 'Confirmation envoyée par courriel. Partagez votre lien pour inviter d\'autres personnes !'
        : 'Confirmation sent by email. Share your link to invite others!' ?></p>

      <?php if ($refUrl): ?>
      <div class="ref-box">
        <label><?= $fr ? 'Votre lien de parrainage' : 'Your referral link' ?></label>
        <div class="ref-copy-row">
          <input type="text" id="ref-link" value="<?= htmlspecialchars($refUrl) ?>" readonly>
          <button class="btn-copy" onclick="copyRef()"><?= $fr ? 'Copier' : 'Copy' ?></button>
        </div>
        <div class="ref-note"><?= $fr ? 'Invitez des amis - chaque inscrit via votre lien est compté !' : 'Invite friends - every signup via your link is counted!' ?></div>
      </div>
      <?php endif; ?>

      <a href="<?= url('/') ?>" style="display:inline-block; margin-top:8px; color:var(--green); font-weight:600;">
        <?= $fr ? '← Retour à l\'accueil' : '← Back to home' ?>
      </a>
    </div>

    <?php else: ?>
    <!-- Form card -->
    <div class="form-card">
      <h2><?= $fr ? 'Réservez votre place' : 'Reserve your spot' ?></h2>
      <p class="sub"><?= $fr ? 'Accès prioritaire - gratuit et sans engagement.' : 'Priority access - free and no commitment.' ?></p>

      <form id="waitlist-form" novalidate>
        <input type="hidden" name="<?= htmlspecialchars(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>" value="<?= htmlspecialchars(csrfToken()) ?>">
        <?php if ($ref): ?>
        <input type="hidden" name="ref" value="<?= htmlspecialchars($ref) ?>">
        <?php endif; ?>

        <div class="form-row">
          <div class="form-group">
            <label><?= $fr ? 'Prénom *' : 'First name *' ?></label>
            <input type="text" name="first_name" placeholder="<?= $fr ? 'Jean' : 'John' ?>" required autocomplete="given-name">
          </div>
          <div class="form-group">
            <label><?= $fr ? 'Nom' : 'Last name' ?></label>
            <input type="text" name="last_name" placeholder="<?= $fr ? 'Dupont' : 'Smith' ?>" autocomplete="family-name">
          </div>
        </div>

        <div class="form-group">
          <label><?= $fr ? 'Courriel *' : 'Email *' ?></label>
          <input type="email" name="email" placeholder="vous@exemple.com" required autocomplete="email">
        </div>

        <div class="form-group">
          <label><?= $fr ? 'Je veux rejoindre en tant que... *' : 'I want to join as... *' ?></label>
          <select name="role" required>
            <option value=""><?= $fr ? '-- Choisir un rôle --' : '-- Select a role --' ?></option>
            <option value="buyer"    <?= $myRole === 'buyer'    ? 'selected' : '' ?>><?= $fr ? 'Acheteur - je veux magasiner' : 'Buyer - I want to shop' ?></option>
            <option value="seller"   <?= $myRole === 'seller'   ? 'selected' : '' ?>><?= $fr ? 'Vendeur - je veux vendre' : 'Seller - I want to sell' ?></option>
            <option value="supplier" <?= $myRole === 'supplier' ? 'selected' : '' ?>><?= $fr ? 'Fournisseur - je fournis des produits' : 'Supplier - I supply products' ?></option>
            <option value="driver"   <?= $myRole === 'driver'   ? 'selected' : '' ?>><?= $fr ? 'Livreur - je veux livrer' : 'Driver - I want to deliver' ?></option>
            <option value="business" <?= $myRole === 'business' ? 'selected' : '' ?>><?= $fr ? 'Client Distribution - commandes B2B' : 'Business Client - B2B orders' ?></option>
          </select>
        </div>

        <button type="submit" class="btn-submit" id="submit-btn">
          <?= $fr ? 'Rejoindre la liste d\'attente' : 'Join the waitlist' ?>
        </button>
        <div id="form-error"></div>
        <p class="privacy-note">
          <?= $fr
            ? 'Votre courriel ne sera jamais partagé. Vous pouvez vous désabonner à tout moment.'
            : 'Your email will never be shared. Unsubscribe anytime.' ?>
        </p>
      </form>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php if (!$joined): ?>
<!-- Roles section -->
<section class="roles-section">
  <div class="container">
    <h2><?= $fr ? 'Une plateforme, cinq façons de participer' : 'One platform, five ways to participate' ?></h2>
    <p class="subtitle"><?= $fr ? 'Choisissez votre rôle - rejoignez l\'écosystème local.' : 'Choose your role - join the local ecosystem.' ?></p>
    <div class="roles-grid">
      <div class="role-card">
        <div class="icon">🛒</div>
        <h3><?= $fr ? 'Acheteur' : 'Buyer' ?></h3>
        <p><?= $fr ? 'Magasinez local, livré chez vous' : 'Shop local, delivered to you' ?></p>
      </div>
      <div class="role-card">
        <div class="icon">🏪</div>
        <h3><?= $fr ? 'Vendeur' : 'Seller' ?></h3>
        <p><?= $fr ? 'Vendez vos produits en ligne' : 'Sell your products online' ?></p>
      </div>
      <div class="role-card">
        <div class="icon">📦</div>
        <h3><?= $fr ? 'Fournisseur' : 'Supplier' ?></h3>
        <p><?= $fr ? 'Fournissez des produits à la plateforme' : 'Supply products to the platform' ?></p>
      </div>
      <div class="role-card">
        <div class="icon">🚴</div>
        <h3><?= $fr ? 'Livreur' : 'Driver' ?></h3>
        <p><?= $fr ? 'Livraisons zéro-émission, horaire flexible' : 'Zero-emission deliveries, flexible schedule' ?></p>
      </div>
      <div class="role-card">
        <div class="icon">🏢</div>
        <h3><?= $fr ? 'Distribution B2B' : 'B2B Distribution' ?></h3>
        <p><?= $fr ? 'Approvisionnement professionnel en gros' : 'Professional wholesale procurement' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- CTA bottom -->
<section class="cta-section">
  <div class="container">
    <h2><?= $fr ? 'Le lancement approche.' : 'Launch is coming.' ?></h2>
    <p><?= $fr
      ? 'Rejoignez la liste maintenant et soyez notifié dès l\'ouverture officielle.'
      : 'Join the list now and be notified the moment we open officially.' ?></p>
    <a href="#top" class="btn-cta" onclick="window.scrollTo({top:0,behavior:'smooth'}); return false;">
      <?= $fr ? 'Rejoindre la liste' : 'Join the waitlist' ?>
    </a>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
document.getElementById('waitlist-form')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('submit-btn');
  const err = document.getElementById('form-error');
  err.style.display = 'none';
  btn.disabled = true;
  btn.textContent = '<?= $fr ? 'Envoi...' : 'Sending...' ?>';

  const data = new FormData(this);

  try {
    const res  = await fetch('<?= url('/waitlist') ?>', { method: 'POST', body: data });
    const json = await res.json();
    if (json.success && json.redirect) {
      window.location.href = json.redirect;
    } else {
      err.textContent = json.message || '<?= $fr ? 'Une erreur est survenue.' : 'An error occurred.' ?>';
      err.style.display = 'block';
      btn.disabled = false;
      btn.textContent = '<?= $fr ? "Rejoindre la liste d\'attente" : 'Join the waitlist' ?>';
    }
  } catch (_) {
    err.textContent = '<?= $fr ? 'Erreur réseau. Réessayez.' : 'Network error. Please try again.' ?>';
    err.style.display = 'block';
    btn.disabled = false;
    btn.textContent = '<?= $fr ? "Rejoindre la liste d\'attente" : 'Join the waitlist' ?>';
  }
});

function copyRef() {
  const inp = document.getElementById('ref-link');
  inp.select();
  navigator.clipboard?.writeText(inp.value).catch(() => document.execCommand('copy'));
  const btn = inp.nextElementSibling;
  btn.textContent = '<?= $fr ? 'Copié !' : 'Copied!' ?>';
  setTimeout(() => btn.textContent = '<?= $fr ? 'Copier' : 'Copy' ?>', 2000);
}
</script>
</body>
</html>
