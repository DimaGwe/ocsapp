<?php
/**
 * OCSAPP Waitlist - Public Landing Page
 * Bilingual FR/EN, role-aware, referral tracking
 * Standardized to the OCSAPP ecosystem visual system (2026-09)
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
    'partner'  => $fr ? 'Partenaire'          : 'Partner',
];
$myRoleLabel = $roleLabels[$myRole] ?? '';
// Positions are numbered per role ("#1 among sellers")
$rolePlural = [
    'buyer'    => ['les acheteurs', 'buyers'],
    'seller'   => ['les vendeurs', 'sellers'],
    'supplier' => ['les fournisseurs', 'suppliers'],
    'driver'   => ['les livreurs', 'drivers'],
    'business' => ['les entreprises', 'businesses'],
    'partner'  => ['les partenaires', 'partners'],
][$myRole] ?? null;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? "Liste d'attente - OCSAPP" : 'Waitlist - OCSAPP' ?></title>
  <?= csrfMeta() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/pages/home.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    :root{
      --ocs-green:#00B207;
      --ocs-green-rgb:0,178,7;
      --ocs-green-dark:#007a05;
      --ocs-forest:#0D3F10;
      --ocs-forest-2:#0A3310;
      --ocs-neon:#00ff88;
      --ocs-text:#17181A;
      --ocs-muted:#66706A;
      --ocs-muted-2:#919994;
      --ocs-border:#E5E7E4;
      --ocs-bg:#F7F8F7;
      --ocs-surface:#FFFFFF;
      --ocs-shadow-md:0 18px 54px rgba(16,24,18,.11);
    }
    *,*::before,*::after{box-sizing:border-box}
    .wl-container{max-width:1120px;margin:0 auto;padding:0 24px}

    /* Hero */
    .wl-hero{background:radial-gradient(circle at 50% 0%,rgba(0,178,7,.13),transparent 36%),linear-gradient(145deg,var(--ocs-forest),var(--ocs-forest-2));padding:88px 24px 76px;text-align:center;position:relative;overflow:hidden}
    .wl-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at center top,rgba(0,255,136,.08) 0%,transparent 65%);pointer-events:none}
    .wl-eyebrow{position:relative;z-index:1;display:inline-block;font-size:11px;font-weight:700;letter-spacing:1.8px;text-transform:uppercase;padding:5px 16px;border-radius:20px;margin-bottom:20px;background:rgba(0,255,136,.12);border:1px solid rgba(0,255,136,.25);color:var(--ocs-neon)}
    .wl-hero h1{position:relative;z-index:1;font-family:'Poppins',sans-serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:800;color:#fff;line-height:1.15;margin-bottom:20px}
    .wl-hero h1 span{color:var(--ocs-neon)}
    .wl-hero p{position:relative;z-index:1;font-size:1.05rem;color:rgba(255,255,255,.72);max-width:700px;margin:0 auto 40px;line-height:1.7}

    /* Form card */
    .wl-card{position:relative;z-index:1;background:#fff;border:1px solid rgba(229,231,228,.95);border-radius:22px;padding:38px;max-width:680px;margin:0 auto;text-align:left;box-shadow:0 24px 64px rgba(0,0,0,.22)}
    .wl-card h2{font-family:'Poppins',sans-serif;font-size:1.4rem;font-weight:700;color:var(--ocs-text);margin-bottom:8px;text-align:center}
    .wl-card .wl-sub{font-size:.9rem;color:var(--ocs-muted);margin-bottom:28px;text-align:center}
    .wl-form-group{margin-bottom:18px}
    .wl-form-group label{display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:6px}
    .wl-form-group input,.wl-form-group select,.wl-form-group textarea{width:100%;padding:12px 14px;border:1.5px solid var(--ocs-border);border-radius:11px;font:14px 'Inter',sans-serif;color:#111;background:#FBFCFB;outline:none;transition:border-color .2s,background .2s,box-shadow .2s}
    .wl-form-group textarea{min-height:88px;resize:vertical}
    .wl-form-group input:focus,.wl-form-group select:focus,.wl-form-group textarea:focus{border-color:rgba(0,178,7,.55);background:#fff;box-shadow:0 0 0 3px rgba(0,178,7,.08)}
    .wl-field-help{display:block;margin-top:5px;color:var(--ocs-muted);font-size:.76rem;line-height:1.4}
    .wl-form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .wl-section-title{margin:26px 0 14px;padding-top:22px;border-top:1px solid var(--ocs-border);font-size:.86rem;font-weight:800;color:var(--ocs-text);text-transform:uppercase;letter-spacing:.06em}
    .wl-conditional{display:none}
    .wl-conditional.active{display:block}
    .wl-consent-row{display:flex;gap:10px;align-items:flex-start;margin:20px 0 4px}
    .wl-consent-row input{width:auto;margin-top:3px;accent-color:var(--ocs-green)}
    .wl-consent-row label{margin:0;font-size:.79rem;line-height:1.5;color:#4b5563;font-weight:400}
    .wl-btn-submit{width:100%;padding:14px;background:var(--ocs-green);color:#fff;font:700 1rem 'Poppins',sans-serif;border:none;border-radius:12px;cursor:pointer;transition:background .2s;margin-top:8px;box-shadow:0 12px 28px rgba(0,178,7,.22)}
    .wl-btn-submit:hover{background:var(--ocs-green-dark)}
    .wl-btn-submit:disabled{opacity:.6;cursor:not-allowed}
    .wl-privacy{font-size:.78rem;color:var(--ocs-muted);text-align:center;margin-top:14px}
    #form-error{display:none;color:#dc2626;font-size:.85rem;margin-top:10px;text-align:center}

    /* Success state */
    .wl-success-card{background:#fff;border-radius:16px;padding:48px 40px;max-width:520px;margin:0 auto;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,.28)}
    .wl-success-icon{width:72px;height:72px;background:rgba(0,178,7,.12);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:2rem;color:var(--ocs-green)}
    .wl-success-card h2{font-family:'Poppins',sans-serif;font-size:1.6rem;font-weight:800;color:var(--ocs-text);margin-bottom:8px}
    .wl-success-card p{color:var(--ocs-muted);font-size:.95rem;margin-bottom:24px;line-height:1.6}
    .wl-position-badge{display:inline-block;background:linear-gradient(135deg,var(--ocs-green),#00d609);color:#fff;font-family:'Poppins',sans-serif;font-size:2rem;font-weight:900;padding:12px 32px;border-radius:12px;margin-bottom:28px}
    .wl-position-badge small{font-size:.9rem;font-weight:400;display:block;opacity:.85}
    .wl-ref-box{background:#f9fafb;border:1.5px dashed var(--ocs-border);border-radius:10px;padding:16px 20px;margin-bottom:20px;text-align:left}
    .wl-ref-box label{font-size:.8rem;font-weight:700;color:var(--ocs-muted);text-transform:uppercase;letter-spacing:1px;display:block;margin-bottom:8px}
    .wl-ref-copy-row{display:flex;gap:8px;align-items:center}
    .wl-ref-copy-row input{flex:1;padding:9px 12px;border:1.5px solid var(--ocs-border);border-radius:8px;font-size:.85rem;background:#fff;color:#111}
    .wl-btn-copy{padding:9px 16px;background:var(--ocs-green);color:#fff;border:none;border-radius:8px;font-size:.85rem;font-weight:600;cursor:pointer;white-space:nowrap}
    .wl-btn-copy:hover{background:var(--ocs-green-dark)}
    .wl-ref-note{font-size:.78rem;color:var(--ocs-muted);margin-top:8px}

    /* Roles section */
    .wl-roles{padding:82px 24px 86px;background:var(--ocs-bg)}
    .wl-roles .wl-container{max-width:1060px}
    .wl-roles-kicker{text-align:center;margin-bottom:10px;color:var(--ocs-green);font:700 11px 'Poppins',sans-serif;letter-spacing:.14em}
    .wl-roles h2{text-align:center;font-family:'Poppins',sans-serif;font-size:clamp(28px,3.5vw,40px);letter-spacing:-.03em;color:var(--ocs-text)}
    .wl-roles .wl-subtitle{text-align:center;max-width:660px;margin:9px auto 42px;font-size:14px;line-height:1.65;color:var(--ocs-muted)}
    .wl-roles-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}
    .wl-role-card{min-height:300px;padding:26px 24px 24px;border:1px solid var(--ocs-border);border-radius:22px;background:var(--ocs-surface);text-align:center;box-shadow:0 6px 24px rgba(17,24,18,.04);position:relative;overflow:hidden;transition:transform .22s ease,box-shadow .22s ease,border-color .22s ease}
    .wl-role-card::before{content:"";position:absolute;width:120px;height:120px;border-radius:50%;right:-56px;top:-56px;background:rgba(var(--ocs-green-rgb),.055);pointer-events:none}
    .wl-role-card:hover{transform:translateY(-4px);border-color:#D5DDD6;box-shadow:var(--ocs-shadow-md)}
    .wl-central-art{position:relative;z-index:1;width:116px;height:116px;margin:0 auto 20px;border-radius:50%;overflow:hidden;background:#080C09;border:1px solid rgba(255,255,255,.06);box-shadow:0 0 0 9px rgba(var(--ocs-green-rgb),.045),0 12px 30px rgba(13,28,16,.16),inset 0 0 0 1px rgba(var(--ocs-green-rgb),.08)}
    .wl-central-art img{width:100%;height:100%;object-fit:cover;display:block;filter:saturate(1.04) contrast(1.02)}
    .wl-role-card h3{position:relative;z-index:1;margin:0 0 8px;color:var(--ocs-text);font:600 17px 'Poppins',sans-serif}
    .wl-role-card p{position:relative;z-index:1;margin:0 auto;max-width:255px;color:var(--ocs-muted);font-size:11.5px;line-height:1.6}

    /* CTA */
    .wl-cta{background:radial-gradient(circle at 50% 0%,rgba(0,178,7,.13),transparent 36%),linear-gradient(145deg,var(--ocs-forest),var(--ocs-forest-2));border-top:1px solid rgba(255,255,255,.05);padding:72px 24px;text-align:center}
    .wl-cta h2{font-family:'Poppins',sans-serif;font-size:2rem;font-weight:800;color:#fff;margin-bottom:16px}
    .wl-cta p{color:rgba(255,255,255,.7);margin-bottom:32px;max-width:480px;margin-left:auto;margin-right:auto}
    .wl-btn-cta{display:inline-block;padding:15px 36px;background:var(--ocs-green);color:#fff;font:800 1rem 'Poppins',sans-serif;border-radius:12px;text-decoration:none;box-shadow:0 12px 28px rgba(0,178,7,.22)}
    .wl-btn-cta:hover{opacity:.92}

    /* Footer (matches ocsapp.ca landing page footer) */
    .wl-site-footer{background:#fff;border-top:1px solid var(--ocs-border);padding:68px 0 30px}
    .wl-footer-top{display:grid;grid-template-columns:1.65fr .85fr 1fr .85fr;gap:46px;padding-bottom:42px;border-bottom:1px solid var(--ocs-border)}
    .wl-footer-brand{display:flex;align-items:center;gap:10px;margin-bottom:14px}
    .wl-footer-brand img{width:38px;height:38px;object-fit:contain}
    .wl-footer-logo-text{font-family:'Poppins',sans-serif;font-weight:700;color:var(--ocs-green);font-size:18px}
    .wl-foot-tagline{font-size:13.5px;color:var(--ocs-muted);max-width:340px;margin-bottom:12px}
    .wl-footer-brand-col p:not(.wl-foot-tagline){font-size:11.5px;color:var(--ocs-muted-2);margin-bottom:4px;line-height:1.6}
    .wl-footer-col h5{font-family:'Poppins',sans-serif;font-size:11.5px;color:var(--ocs-text);margin-bottom:14px;letter-spacing:.06em;text-transform:uppercase}
    .wl-footer-col a{display:block;color:var(--ocs-muted);font-size:13px;margin-bottom:9px;text-decoration:none}
    .wl-footer-col a:hover{color:var(--ocs-green)}
    .wl-footer-bottom{display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;align-items:center;padding-top:24px}
    .wl-footer-bottom p{font-size:12px;color:var(--ocs-muted-2)}
    .wl-footer-legal{display:flex;flex-wrap:wrap;gap:4px 16px}
    .wl-footer-legal a{color:var(--ocs-muted-2);font-size:11.5px;text-decoration:none}
    .wl-footer-legal a:hover{color:var(--ocs-green)}
    @media(max-width:1060px){
      .wl-footer-top{grid-template-columns:1.25fr 1fr 1fr}
      .wl-footer-brand-col{grid-column:1/-1}
    }
    @media(max-width:760px){
      .wl-footer-top{grid-template-columns:1fr 1fr}
      .wl-footer-brand-col{grid-column:1/-1}
    }
    @media(max-width:560px){
      .wl-footer-top{grid-template-columns:1fr}
      .wl-footer-brand-col{grid-column:auto}
      .wl-footer-bottom{align-items:flex-start;flex-direction:column}
      .wl-footer-legal{gap:6px 14px}
    }

    @media(max-width:820px){
      .wl-roles-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:760px){
      .wl-card{padding:28px 20px}
      .wl-form-row{grid-template-columns:1fr}
    }
    @media(max-width:520px){
      .wl-roles-grid{grid-template-columns:1fr}
      .wl-central-art{width:108px;height:108px}
    }
    @media(max-width:480px){
      .wl-hero{padding-left:16px;padding-right:16px}
    }
  </style>
</head>
<body>
<?php $useMarcheHeader = true; ?>
<?php require __DIR__ . '/../components/header.php'; ?>

<!-- Hero -->
<section class="wl-hero" id="top">
  <div class="wl-container">
    <span class="wl-eyebrow"><?= $fr ? 'Accès prioritaire · Lancement bientôt' : 'Priority access · Launching soon' ?></span>
    <h1>
      <?= $fr
        ? 'Soyez <span>parmi les premiers</span><br>à rejoindre OCSAPP'
        : 'Be <span>among the first</span><br>to join OCSAPP' ?>
    </h1>
    <p>
      <?= $fr
        ? "L'écosystème numérique du commerce d'ici. Inscrivez-vous pour un accès prioritaire et découvrez un réseau local conçu pour évoluer vers un objectif de livraison zéro émission."
        : "The digital ecosystem for local commerce. Sign up for priority access and discover a local network built toward a zero-emission delivery goal." ?>
    </p>

    <?php if ($joined): ?>
    <!-- Success card -->
    <div class="wl-success-card">
      <div class="wl-success-icon"><i class="fa-solid fa-check"></i></div>
      <h2><?= $fr ? 'Vous êtes sur la liste !' : "You're on the list!" ?></h2>
      <?php if ($myRoleLabel): ?>
        <p style="margin-bottom:8px;font-size:13px;">
          <?= $fr ? "Inscrit en tant que : <strong>{$myRoleLabel}</strong>" : "Registered as: <strong>{$myRoleLabel}</strong>" ?>
        </p>
      <?php endif; ?>
      <?php if ($pos > 0): ?>
      <div class="wl-position-badge">
        #<?= $pos ?>
        <small><?= $rolePlural ? ($fr ? 'votre position parmi ' . $rolePlural[0] : 'your position among ' . $rolePlural[1]) : ($fr ? 'votre position' : 'your position') ?></small>
      </div>
      <?php endif; ?>
      <p style="font-size:13px;line-height:1.6;"><?= $fr
        ? "Confirmation envoyée par courriel. Partagez votre lien pour inviter d'autres personnes !"
        : 'Confirmation sent by email. Share your link to invite others!' ?></p>

      <?php if ($refUrl): ?>
      <div class="wl-ref-box">
        <label><?= $fr ? 'Votre code de parrainage' : 'Your referral code' ?></label>
        <div class="wl-ref-copy-row" style="margin-bottom:14px">
          <input type="text" id="ref-code-mine" value="<?= $myRef ?>" readonly style="font-family:'Poppins',sans-serif;font-size:1.1rem;font-weight:700;letter-spacing:.12em;text-align:center">
          <button class="wl-btn-copy" onclick="copyRef('ref-code-mine')"><?= $fr ? 'Copier' : 'Copy' ?></button>
        </div>
        <label><?= $fr ? 'Votre lien de parrainage' : 'Your referral link' ?></label>
        <div class="wl-ref-copy-row">
          <input type="text" id="ref-link" value="<?= htmlspecialchars($refUrl) ?>" readonly>
          <button class="wl-btn-copy" onclick="copyRef()"><?= $fr ? 'Copier' : 'Copy' ?></button>
        </div>
        <div class="wl-ref-note"><?= $fr ? 'Invitez des amis - chaque inscrit via votre lien est compté !' : 'Invite friends - every signup via your link is counted!' ?></div>
      </div>
      <?php endif; ?>

      <a href="<?= url('/') ?>" style="display:inline-block;margin-top:6px;color:var(--ocs-green);font-weight:600;font-size:13px;">
        <?= $fr ? "← Retour à l'accueil" : '← Back to home' ?>
      </a>
    </div>

    <?php else: ?>
    <!-- Form card -->
    <div class="wl-card">
      <h2><?= $fr ? 'Réservez votre place' : 'Reserve your spot' ?></h2>
      <p class="wl-sub"><?= $fr ? 'Quelques renseignements nous aideront à mieux préparer votre accès OCSAPP.' : 'A few details will help us better prepare your OCSAPP access.' ?></p>

      <form id="waitlist-form" novalidate>
        <input type="hidden" name="<?= htmlspecialchars(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="utm_source" id="utm_source">
        <input type="hidden" name="utm_medium" id="utm_medium">
        <input type="hidden" name="utm_campaign" id="utm_campaign">
        <input type="hidden" name="utm_content" id="utm_content">
        <input type="hidden" name="referral" id="referral">

        <div class="wl-form-row">
          <div class="wl-form-group">
            <label><?= $fr ? 'Prénom *' : 'First name *' ?></label>
            <input type="text" name="first_name" placeholder="<?= $fr ? 'Jean' : 'John' ?>" required autocomplete="given-name">
          </div>
          <div class="wl-form-group">
            <label><?= $fr ? 'Nom *' : 'Last name *' ?></label>
            <input type="text" name="last_name" placeholder="<?= $fr ? 'Dupont' : 'Smith' ?>" required autocomplete="family-name">
          </div>
        </div>

        <div class="wl-form-row">
          <div class="wl-form-group">
            <label><?= $fr ? 'Courriel *' : 'Email *' ?></label>
            <input type="email" name="email" placeholder="vous@exemple.com" required autocomplete="email">
          </div>
          <div class="wl-form-group">
            <label><?= $fr ? 'Téléphone' : 'Phone' ?> <span style="font-weight:400;color:#6b7280"><?= $fr ? '(facultatif)' : '(optional)' ?></span></label>
            <input type="tel" name="phone" placeholder="514 555-0123" autocomplete="tel">
          </div>
        </div>

        <div class="wl-form-row">
          <div class="wl-form-group">
            <label><?= $fr ? 'Ville / région *' : 'City / region *' ?></label>
            <input type="text" name="city_region" placeholder="<?= $fr ? "Montréal, Laval, Ouest-de-l'Île..." : "Montreal, Laval, West Island..." ?>" required autocomplete="address-level2">
          </div>
          <div class="wl-form-group">
            <label><?= $fr ? 'Langue préférée *' : 'Preferred language *' ?></label>
            <select name="preferred_language" required>
              <option value="fr" <?= $currentLang === 'fr' ? 'selected' : '' ?>><?= $fr ? 'Français' : 'French' ?></option>
              <option value="en" <?= $currentLang === 'en' ? 'selected' : '' ?>><?= $fr ? 'Anglais' : 'English' ?></option>
            </select>
          </div>
        </div>

        <div class="wl-form-group">
          <label><?= $fr ? 'Je souhaite rejoindre OCSAPP comme... *' : 'I want to join OCSAPP as... *' ?></label>
          <select name="role" id="waitlist-role" required>
            <option value=""><?= $fr ? '-- Choisir un rôle --' : '-- Choose a role --' ?></option>
            <option value="buyer" <?= $myRole === 'buyer' ? 'selected' : '' ?>><?= $fr ? 'Acheteur - je veux magasiner local' : 'Buyer - I want to shop local' ?></option>
            <option value="seller" <?= $myRole === 'seller' ? 'selected' : '' ?>><?= $fr ? 'Vendeur - je veux vendre sur OCSAPP' : 'Seller - I want to sell on OCSAPP' ?></option>
            <option value="supplier" <?= $myRole === 'supplier' ? 'selected' : '' ?>><?= $fr ? 'Fournisseur - je fournis des produits' : 'Supplier - I supply products' ?></option>
            <option value="business" <?= $myRole === 'business' ? 'selected' : '' ?>><?= $fr ? 'Entreprise - besoins professionnels / B2B' : 'Business - professional / B2B needs' ?></option>
            <option value="driver" <?= $myRole === 'driver' ? 'selected' : '' ?>><?= $fr ? 'Livreur · ODA - je veux livrer' : 'Driver · ODA - I want to deliver' ?></option>
            <option value="partner" <?= $myRole === 'partner' ? 'selected' : '' ?>><?= $fr ? 'Autre / Partenaire' : 'Other / Partner' ?></option>
          </select>
        </div>

        <div id="role-details">
          <div class="wl-conditional" data-role="seller">
            <div class="wl-section-title"><?= $fr ? 'À propos de votre commerce' : 'About your shop' ?></div>
            <div class="wl-form-row">
              <div class="wl-form-group"><label><?= $fr ? "Nom de l'entreprise" : 'Business name' ?></label><input type="text" name="seller_business_name"></div>
              <div class="wl-form-group"><label><?= $fr ? 'Type de commerce' : 'Type of business' ?></label><input type="text" name="seller_business_type" placeholder="<?= $fr ? 'Restaurant, épicerie, boutique...' : 'Restaurant, grocery store, boutique...' ?>"></div>
            </div>
            <div class="wl-form-group">
              <label><?= $fr ? 'Avez-vous déjà une boutique en ligne ?' : 'Do you already have an online store?' ?></label>
              <select name="seller_online_store">
                <option value=""><?= $fr ? '-- Sélectionner --' : '-- Select --' ?></option>
                <option value="yes"><?= $fr ? 'Oui' : 'Yes' ?></option>
                <option value="no"><?= $fr ? 'Non' : 'No' ?></option>
              </select>
            </div>
          </div>

          <div class="wl-conditional" data-role="supplier">
            <div class="wl-section-title"><?= $fr ? 'À propos de votre entreprise' : 'About your business' ?></div>
            <div class="wl-form-row">
              <div class="wl-form-group"><label><?= $fr ? "Nom de l'entreprise" : 'Business name' ?></label><input type="text" name="supplier_business_name"></div>
              <div class="wl-form-group"><label><?= $fr ? 'Secteur / produits fournis' : 'Sector / products supplied' ?></label><input type="text" name="supplier_products"></div>
            </div>
            <div class="wl-form-group"><label><?= $fr ? 'Zone desservie' : 'Service area' ?></label><input type="text" name="supplier_service_area" placeholder="<?= $fr ? 'Grand Montréal, Québec...' : 'Greater Montreal, Quebec...' ?>"></div>
          </div>

          <div class="wl-conditional" data-role="business">
            <div class="wl-section-title"><?= $fr ? 'À propos de votre entreprise' : 'About your business' ?></div>
            <div class="wl-form-row">
              <div class="wl-form-group"><label><?= $fr ? "Nom de l'entreprise" : 'Business name' ?></label><input type="text" name="business_name"></div>
              <div class="wl-form-group"><label><?= $fr ? "Secteur d'activité" : 'Industry' ?></label><input type="text" name="business_sector"></div>
            </div>
            <div class="wl-form-group">
              <label><?= $fr ? 'Besoin principal' : 'Main need' ?></label>
              <select name="business_need">
                <option value=""><?= $fr ? '-- Sélectionner --' : '-- Select --' ?></option>
                <option value="procurement"><?= $fr ? 'Approvisionnement' : 'Procurement' ?></option>
                <option value="employee"><?= $fr ? 'Achats / avantages employés' : 'Employee purchases / perks' ?></option>
                <option value="delivery"><?= $fr ? 'Livraison' : 'Delivery' ?></option>
                <option value="other"><?= $fr ? 'Autre' : 'Other' ?></option>
              </select>
            </div>
          </div>

          <div class="wl-conditional" data-role="driver">
            <div class="wl-section-title"><?= $fr ? 'Votre intérêt pour la livraison' : 'Your interest in delivery' ?></div>
            <div class="wl-form-row">
              <div class="wl-form-group"><label><?= $fr ? 'Ville / zone souhaitée' : 'Desired city / area' ?></label><input type="text" name="driver_area"></div>
              <div class="wl-form-group">
                <label><?= $fr ? 'Type de véhicule' : 'Vehicle type' ?></label>
                <select name="driver_vehicle">
                  <option value=""><?= $fr ? '-- Sélectionner --' : '-- Select --' ?></option>
                  <option value="bike"><?= $fr ? 'Vélo / vélo électrique' : 'Bike / e-bike' ?></option>
                  <option value="car"><?= $fr ? 'Voiture' : 'Car' ?></option>
                  <option value="van"><?= $fr ? 'Fourgonnette' : 'Van' ?></option>
                  <option value="other"><?= $fr ? 'Autre' : 'Other' ?></option>
                </select>
              </div>
            </div>
            <div class="wl-form-group"><label><?= $fr ? 'Disponibilité générale' : 'General availability' ?></label><input type="text" name="driver_availability" placeholder="<?= $fr ? 'Soirs, fins de semaine, temps plein...' : 'Evenings, weekends, full-time...' ?>"></div>
          </div>

          <div class="wl-conditional" data-role="buyer">
            <div class="wl-section-title"><?= $fr ? 'Vos intérêts' : 'Your interests' ?></div>
            <div class="wl-form-group">
              <label><?= $fr ? "Qu'aimeriez-vous surtout trouver sur OCSAPP ?" : 'What would you most like to find on OCSAPP?' ?> <span style="font-weight:400;color:#6b7280"><?= $fr ? '(facultatif)' : '(optional)' ?></span></label>
              <textarea name="buyer_interest" placeholder="<?= $fr ? 'Commerces, produits ou services locaux...' : 'Local shops, products or services...' ?>"></textarea>
            </div>
          </div>

          <div class="wl-conditional" data-role="partner">
            <div class="wl-section-title"><?= $fr ? 'Votre intérêt' : 'Your interest' ?></div>
            <div class="wl-form-group">
              <label><?= $fr ? 'Comment souhaitez-vous participer ?' : 'How would you like to participate?' ?></label>
              <textarea name="partner_interest"></textarea>
            </div>
          </div>
        </div>

        <div class="wl-form-group">
          <label><?= $fr ? "Comment avez-vous entendu parler d'OCSAPP ?" : 'How did you hear about OCSAPP?' ?></label>
          <select name="discovery_source">
            <option value=""><?= $fr ? '-- Sélectionner --' : '-- Select --' ?></option>
            <option value="representative"><?= $fr ? "Visite d'un représentant OCSAPP" : 'Visit from an OCSAPP representative' ?></option>
            <option value="social"><?= $fr ? 'Réseaux sociaux' : 'Social media' ?></option>
            <option value="referral"><?= $fr ? 'Ami / collègue' : 'Friend / colleague' ?></option>
            <option value="local_business"><?= $fr ? 'Commerce local' : 'Local business' ?></option>
            <option value="event"><?= $fr ? 'Événement' : 'Event' ?></option>
            <option value="web"><?= $fr ? 'Recherche Web' : 'Web search' ?></option>
            <option value="other"><?= $fr ? 'Autre' : 'Other' ?></option>
          </select>
        </div>

        <div class="wl-form-group">
          <label for="ref-code"><?= $fr ? 'Code de parrainage' : 'Referral code' ?> <span style="font-weight:400;color:#6b7280"><?= $fr ? '(facultatif)' : '(optional)' ?></span></label>
          <input type="text" id="ref-code" name="ref" value="<?= $ref ?>" maxlength="12" autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="<?= $fr ? 'Ex. : A3F9C21B' : 'e.g. A3F9C21B' ?>" style="text-transform:uppercase;letter-spacing:.08em">
          <span class="wl-field-help"><?= $fr ? "Quelqu'un vous a invité ? Entrez le code à 8 caractères qu'il vous a transmis." : 'Did someone invite you? Enter the 8-character code they shared with you.' ?></span>
        </div>

        <div class="wl-consent-row">
          <input type="checkbox" id="marketing-consent" name="marketing_consent" value="yes">
          <label for="marketing-consent"><?= $fr
            ? "J'accepte de recevoir des nouvelles, des renseignements sur le lancement et des communications promotionnelles d'OCSAPP. Je peux retirer mon consentement en tout temps."
            : 'I agree to receive news, launch updates and promotional communications from OCSAPP. I can withdraw my consent at any time.' ?></label>
        </div>

        <button type="submit" class="wl-btn-submit" id="submit-btn">
          <?= $fr ? "Rejoindre la liste d'attente" : 'Join the waitlist' ?>
        </button>
        <div id="form-error"></div>
        <p class="wl-privacy">
          <?= $fr
            ? 'Vos renseignements sont utilisés pour gérer votre inscription, préparer votre accès et, si vous y consentez, vous transmettre des communications OCSAPP. Consultez notre'
            : 'Your information is used to manage your registration, prepare your access and, if you consent, send you OCSAPP communications. See our' ?>
          <a href="<?= url('/privacy') ?>" style="color:var(--ocs-green);font-weight:600"><?= $fr ? 'politique de confidentialité' : 'privacy policy' ?></a>.
        </p>
      </form>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php if (!$joined): ?>
<!-- Roles section -->
<section class="wl-roles">
  <div class="wl-container">
    <div class="wl-roles-kicker"><?= $fr ? 'ÉCOSYSTÈME OCSAPP' : 'OCSAPP ECOSYSTEM' ?></div>
    <h2><?= $fr ? 'Un écosystème, six Centrales' : 'One ecosystem, six Centrals' ?></h2>
    <p class="wl-subtitle"><?= $fr ? "OCSAPP relie les participants du commerce local dans un même écosystème numérique." : 'OCSAPP connects local commerce participants within one digital ecosystem.' ?></p>
    <div class="wl-roles-grid">
      <div class="wl-role-card">
        <div class="wl-central-art"><img src="<?= asset('images/centrals/icon-marketplace.jpg') ?>" alt="<?= $fr ? 'Icône Marché Central' : 'Marketplace Central icon' ?>"></div>
        <h3><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></h3>
        <p><?= $fr ? "Découvrez et magasinez auprès des commerces d'ici." : 'Discover and shop at businesses from here.' ?></p>
      </div>
      <div class="wl-role-card">
        <div class="wl-central-art"><img src="<?= asset('images/centrals/icon-seller.jpg') ?>" alt="<?= $fr ? 'Icône Vendeur Central' : 'Seller Central icon' ?>"></div>
        <h3><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></h3>
        <p><?= $fr ? 'Présentez, gérez et développez votre commerce sur OCSAPP.' : 'List, manage and grow your business on OCSAPP.' ?></p>
      </div>
      <div class="wl-role-card">
        <div class="wl-central-art"><img src="<?= asset('images/centrals/icon-supplier.jpg') ?>" alt="<?= $fr ? 'Icône Fournisseur Central' : 'Supplier Central icon' ?>"></div>
        <h3><?= $fr ? 'Fournisseur Central' : 'Supplier Central' ?></h3>
        <p><?= $fr ? "Connectez votre offre aux vendeurs et entreprises de l'écosystème." : 'Connect your offering to sellers and businesses in the ecosystem.' ?></p>
      </div>
      <div class="wl-role-card">
        <div class="wl-central-art"><img src="<?= asset('images/centrals/icon-buyer.jpg') ?>" alt="<?= $fr ? 'Icône Acheteur Central' : 'Buyer Central icon' ?>"></div>
        <h3><?= $fr ? 'Acheteur Central' : 'Buyer Central' ?></h3>
        <p><?= $fr ? "Accédez au commerce local à partir d'une expérience unifiée." : 'Access local commerce from one unified experience.' ?></p>
      </div>
      <div class="wl-role-card">
        <div class="wl-central-art"><img src="<?= asset('images/centrals/icon-business.jpg') ?>" alt="<?= $fr ? 'Icône Entreprise Centrale' : 'Business Central icon' ?>"></div>
        <h3><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></h3>
        <p><?= $fr ? 'Centralisez vos besoins professionnels et votre approvisionnement.' : 'Centralize your professional needs and procurement.' ?></p>
      </div>
      <div class="wl-role-card">
        <div class="wl-central-art"><img src="<?= asset('images/centrals/icon-driver.jpg') ?>" alt="<?= $fr ? 'Icône Livreur Central · ODA' : 'Driver Central · ODA icon' ?>"></div>
        <h3><?= $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA' ?></h3>
        <p><?= $fr ? 'Participez au réseau de livraison avec un objectif de devenir zéro émission.' : 'Join the delivery network working toward a zero-emission goal.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- CTA bottom -->
<section class="wl-cta">
  <div class="wl-container">
    <h2><?= $fr ? 'Le lancement approche.' : 'Launch is approaching.' ?></h2>
    <p><?= $fr
      ? "Inscrivez-vous maintenant pour recevoir votre accès prioritaire et les prochaines étapes correspondant à votre rôle."
      : 'Sign up now to get your priority access and the next steps for your role.' ?></p>
    <a href="#top" class="wl-btn-cta" onclick="window.scrollTo({top:0,behavior:'smooth'}); return false;">
      <?= $fr ? 'Réserver ma place' : 'Reserve my spot' ?>
    </a>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/../components/auth-popup.php'; ?>

<!-- Footer (matches ocsapp.ca landing page footer) -->
<footer class="wl-site-footer">
  <div class="wl-container">
    <div class="wl-footer-top">
      <div class="wl-footer-brand-col">
        <div class="wl-footer-brand">
          <img src="<?= asset('images/logo.png') ?>" alt="<?= $fr ? 'Logo OCSAPP' : 'OCSAPP Logo' ?>">
          <span class="wl-footer-logo-text">OCSAPP</span>
        </div>
        <p class="wl-foot-tagline"><?= $fr ? "L'infrastructure numérique tout-en-un du commerce local." : 'The all-in-one digital infrastructure for local commerce.' ?></p>
        <p><?= $fr
          ? 'OCSAPP Inc. · Constituée sous le régime fédéral de la Loi canadienne sur les sociétés par actions (n<sup>o</sup> de société 1750354-7) · Numéro d\'entreprise du Québec (NEQ) 1181584997'
          : 'OCSAPP Inc. · Federally incorporated under the Canada Business Corporations Act (Corporation No. 1750354-7) · Quebec enterprise number (NEQ) 1181584997'
        ?></p>
        <p><?= $fr ? 'Siège social : Laval, Québec (H7H)' : 'Registered office: Laval, Québec (H7H)' ?></p>
      </div>

      <div class="wl-footer-col">
        <h5><?= $fr ? 'Apprenez à nous connaître' : 'Get to Know Us' ?></h5>
        <a href="<?= url('about') ?>"><?= $fr ? "À propos d'OCSAPP" : 'About OCSAPP' ?></a>
        <a href="<?= url('contact') ?>"><?= $fr ? 'Contactez-nous' : 'Contact Us' ?></a>
      </div>

      <div class="wl-footer-col">
        <h5><?= $fr ? 'Écosystème OCSAPP' : 'OCSAPP Ecosystem' ?></h5>
        <a href="<?= url('marketplace-central') ?>"><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></a>
        <a href="<?= url('buyer-central') ?>"><?= $fr ? 'Acheteur Central' : 'Buyer Central' ?></a>
        <a href="<?= url('seller-central') ?>"><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></a>
        <a href="<?= url('supplier-central') ?>"><?= $fr ? 'Fournisseur Central' : 'Supplier Central' ?></a>
        <a href="<?= url('driver-central') ?>"><?= $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA' ?></a>
        <a href="<?= url('distribution') ?>"><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></a>
      </div>

      <div class="wl-footer-col">
        <h5><?= $fr ? 'Connectez-vous avec nous' : 'Connect With Us' ?></h5>
        <a href="https://www.facebook.com/ocsapp.ca" target="_blank" rel="noopener">Facebook</a>
        <a href="https://www.instagram.com/ocsapp.ca" target="_blank" rel="noopener">Instagram</a>
        <a href="https://www.linkedin.com/company/ocsapp" target="_blank" rel="noopener">LinkedIn</a>
      </div>
    </div>

    <div class="wl-footer-bottom">
      <p>OCSAPP &copy; <?= date('Y') ?>. <?= $fr ? 'Tous droits réservés.' : 'All rights reserved.' ?></p>
      <div class="wl-footer-legal">
        <a href="<?= url('privacy') ?>"><?= $fr ? 'Politique de confidentialité' : 'Privacy Policy' ?></a>
        <a href="<?= url('terms') ?>"><?= $fr ? "Conditions d'utilisation" : 'Terms of Service' ?></a>
        <a href="<?= url('cookies') ?>"><?= $fr ? 'Politique de cookies' : 'Cookie Policy' ?></a>
        <a href="<?= url('returns') ?>"><?= $fr ? 'Retours' : 'Returns' ?></a>
        <a href="<?= url('accessibility') ?>"><?= $fr ? 'Accessibilité' : 'Accessibility' ?></a>
      </div>
    </div>
  </div>
</footer>
<script>
document.querySelectorAll('[data-auto-dismiss]').forEach(function(el) {
    setTimeout(function() {
        el.style.opacity = '0';
        setTimeout(function() { el.style.display = 'none'; }, 600);
    }, 4000);
});
</script>

<script>
(function() {
  const role = document.getElementById('waitlist-role');
  const groups = document.querySelectorAll('.wl-conditional');
  function syncRoleFields() {
    const value = role ? role.value : '';
    groups.forEach(function(group) {
      const active = group.dataset.role === value;
      group.classList.toggle('active', active);
      group.querySelectorAll('input,select,textarea').forEach(function(el) { el.disabled = !active; });
    });
  }
  if (role) { role.addEventListener('change', syncRoleFields); syncRoleFields(); }

  const params = new URLSearchParams(window.location.search);
  ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'referral'].forEach(function(key) {
    const el = document.getElementById(key);
    if (el) el.value = params.get(key) || '';
  });
})();

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

function copyRef(id = 'ref-link') {
  const inp = document.getElementById(id);
  inp.select();
  navigator.clipboard?.writeText(inp.value).catch(() => document.execCommand('copy'));
  const btn = inp.nextElementSibling;
  btn.textContent = '<?= $fr ? 'Copié !' : 'Copied!' ?>';
  setTimeout(() => btn.textContent = '<?= $fr ? 'Copier' : 'Copy' ?>', 2000);
}
</script>
</body>
</html>
