<?php
/**
 * OCSAPP Contact Page
 * Bilingual: EN / FR
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? 'Contactez-nous - OCSAPP' : 'Contact Us - OCSAPP' ?></title>
  <meta name="description" content="<?= $fr ? 'Contactez l\'équipe OCSAPP. Nous sommes là pour vous aider.' : 'Get in touch with the OCSAPP team. We\'re here to help.' ?>">
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
      --light:      #f9fafb;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: var(--text); line-height: 1.65; }
    a { text-decoration: none; }
    .container { max-width: 1160px; margin: 0 auto; padding: 0 24px; }

    /* ── HERO ── */
    .hero {
      background: linear-gradient(140deg, #060a1a 0%, #0a1a0c 55%, #0c2e10 100%);
      padding: 96px 24px 88px; text-align: center; position: relative; overflow: hidden;
    }
    .hero::before {
      content: ''; position: absolute; border-radius: 50%; pointer-events: none;
      width: 700px; height: 700px; top: -200px; right: -150px;
      background: radial-gradient(circle, rgba(0,178,7,.16) 0%, transparent 65%);
    }
    .hero::after {
      content: ''; position: absolute; border-radius: 50%; pointer-events: none;
      width: 500px; height: 500px; bottom: -120px; left: -80px;
      background: radial-gradient(circle, rgba(0,255,136,.08) 0%, transparent 65%);
    }
    .hero-inner { position: relative; z-index: 1; max-width: 700px; margin: 0 auto; }
    .eyebrow {
      display: inline-block; font-size: 11px; font-weight: 700;
      letter-spacing: 1.8px; text-transform: uppercase;
      padding: 5px 16px; border-radius: 20px; margin-bottom: 20px;
      background: rgba(0,255,136,.12); border: 1px solid rgba(0,255,136,.25); color: var(--neon);
    }
    .hero h1 {
      font-size: clamp(36px, 5.5vw, 58px); font-weight: 900; color: white;
      line-height: 1.05; letter-spacing: -1.5px; margin-bottom: 20px;
    }
    .hero h1 span { color: var(--neon); }
    .hero p {
      font-size: 18px; color: rgba(255,255,255,.72); max-width: 560px;
      margin: 0 auto; line-height: 1.7;
    }

    /* ── INFO CARDS ── */
    .info-section { background: white; padding: 80px 24px; }
    .info-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 28px; margin-bottom: 72px;
    }
    .info-card {
      background: var(--light); border: 1px solid var(--border);
      border-radius: 16px; padding: 32px 28px; text-align: center;
      transition: box-shadow .2s, transform .2s;
    }
    .info-card:hover { box-shadow: 0 8px 32px rgba(0,178,7,.12); transform: translateY(-3px); }
    .info-card .icon {
      width: 56px; height: 56px; border-radius: 14px;
      background: rgba(0,178,7,.1); color: var(--green);
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; margin: 0 auto 18px;
    }
    .info-card h3 { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 8px; }
    .info-card p { font-size: 14px; color: var(--muted); line-height: 1.6; }
    .info-card a { color: var(--green); font-weight: 600; }
    .info-card a:hover { text-decoration: underline; }

    /* ── FORM LAYOUT ── */
    .form-layout {
      display: grid; grid-template-columns: 1fr 1fr; gap: 64px;
      align-items: start;
    }
    @media (max-width: 860px) { .form-layout { grid-template-columns: 1fr; gap: 48px; } }

    .form-aside h2 {
      font-size: clamp(26px, 3.5vw, 34px); font-weight: 800; color: #111827;
      line-height: 1.2; margin-bottom: 16px;
    }
    .form-aside h2 span { color: var(--green); }
    .form-aside p { font-size: 16px; color: var(--muted); line-height: 1.75; margin-bottom: 28px; }

    .response-times { list-style: none; }
    .response-times li {
      display: flex; align-items: center; gap: 12px;
      font-size: 14px; color: #374151; padding: 10px 0;
      border-bottom: 1px solid var(--border);
    }
    .response-times li:last-child { border-bottom: none; }
    .response-times li i { color: var(--green); width: 18px; text-align: center; }

    /* ── CONTACT FORM ── */
    .contact-form-wrap {
      background: white; border: 1px solid var(--border);
      border-radius: 20px; padding: 40px 36px;
      box-shadow: 0 4px 24px rgba(0,0,0,.07);
    }
    .form-group { margin-bottom: 20px; }
    .form-group label {
      display: block; font-size: 13px; font-weight: 600; color: #374151;
      margin-bottom: 6px; letter-spacing: .2px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%; padding: 12px 16px; border: 1.5px solid var(--border);
      border-radius: 10px; font-size: 15px; color: #111827;
      background: white; transition: border-color .2s, box-shadow .2s;
      font-family: inherit; outline: none;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: var(--green); box-shadow: 0 0 0 3px rgba(0,178,7,.1);
    }
    .form-group textarea { resize: vertical; min-height: 130px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }

    .btn-submit {
      width: 100%; padding: 14px 24px;
      background: var(--green); color: white;
      border: none; border-radius: 10px; font-size: 16px; font-weight: 700;
      cursor: pointer; transition: background .2s, transform .15s;
      display: flex; align-items: center; justify-content: center; gap: 10px;
    }
    .btn-submit:hover { background: var(--green-dark); transform: translateY(-1px); }
    .btn-submit:disabled { opacity: .65; cursor: not-allowed; transform: none; }

    .form-notice {
      margin-top: 16px; padding: 14px 18px; border-radius: 10px;
      font-size: 14px; display: none;
    }
    .form-notice.success { background: rgba(0,178,7,.08); color: #065f46; border: 1px solid rgba(0,178,7,.2); display: block; }
    .form-notice.error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; display: block; }

    /* ── FAQ STRIP ── */
    .faq-section { background: var(--light); padding: 80px 24px; }
    .section-title {
      text-align: center; font-size: clamp(26px,4vw,36px);
      font-weight: 800; color: #111827; margin-bottom: 12px;
    }
    .section-sub {
      text-align: center; font-size: 16px; color: var(--muted);
      max-width: 560px; margin: 0 auto 52px; line-height: 1.7;
    }
    .faq-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; }
    .faq-item {
      background: white; border: 1px solid var(--border);
      border-radius: 14px; padding: 28px 24px;
    }
    .faq-item h4 { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 10px; }
    .faq-item p { font-size: 14px; color: var(--muted); line-height: 1.7; }

    /* ── CTA ── */
    .cta-section {
      background: linear-gradient(140deg, #060a1a 0%, #0a1a0c 55%, #0c2e10 100%);
      padding: 80px 24px; text-align: center; position: relative; overflow: hidden;
    }
    .cta-section::before {
      content: ''; position: absolute; border-radius: 50%; pointer-events: none;
      width: 500px; height: 500px; top: -120px; right: -80px;
      background: radial-gradient(circle, rgba(0,178,7,.15) 0%, transparent 65%);
    }
    .cta-inner { position: relative; z-index: 1; }
    .cta-section h2 { font-size: clamp(28px,4vw,42px); font-weight: 900; color: white; margin-bottom: 14px; }
    .cta-section p { font-size: 17px; color: rgba(255,255,255,.7); max-width: 520px; margin: 0 auto 36px; }
    .btn-cta {
      display: inline-flex; align-items: center; gap: 10px;
      background: var(--green); color: white;
      padding: 15px 36px; border-radius: 10px; font-size: 16px; font-weight: 700;
      transition: background .2s, transform .15s;
    }
    .btn-cta:hover { background: var(--green-dark); transform: translateY(-2px); }

    footer.footer { margin-top: 0; }
  </style>
</head>
<body>
<?php include __DIR__ . '/../components/header.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <span class="eyebrow"><?= $fr ? 'Support & Contact' : 'Support & Contact' ?></span>
    <h1><?= $fr ? 'Comment pouvons-nous <span>vous aider?</span>' : 'How can we <span>help you?</span>' ?></h1>
    <p><?= $fr
      ? 'Notre équipe est là pour répondre à vos questions, résoudre vos problèmes et vous accompagner dans votre succès sur OCSAPP.'
      : 'Our team is here to answer your questions, resolve issues, and support your success on OCSAPP.' ?></p>
  </div>
</section>

<!-- INFO CARDS + FORM -->
<section class="info-section">
  <div class="container">

    <!-- Contact Info Cards -->
    <div class="info-grid">
      <div class="info-card">
        <div class="icon"><i class="fas fa-envelope"></i></div>
        <h3><?= $fr ? 'Courriel' : 'Email' ?></h3>
        <p><a href="mailto:support@ocsapp.ca">support@ocsapp.ca</a></p>
        <p style="margin-top:6px;"><?= $fr ? 'Réponse sous 24 h' : 'Reply within 24 hours' ?></p>
      </div>
      <div class="info-card">
        <div class="icon"><i class="fas fa-clock"></i></div>
        <h3><?= $fr ? 'Heures d\'assistance' : 'Support Hours' ?></h3>
        <p><?= $fr ? 'Lun – Ven : 9h – 18h' : 'Mon – Fri: 9 AM – 6 PM' ?></p>
        <p><?= $fr ? 'Sam : 10h – 15h' : 'Sat: 10 AM – 3 PM' ?></p>
      </div>
      <div class="info-card">
        <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
        <h3><?= $fr ? 'Région desservie' : 'Service Area' ?></h3>
        <p><?= $fr ? 'Montréal & West Island, Québec, Canada' : 'Montreal & West Island, Quebec, Canada' ?></p>
      </div>
      <div class="info-card">
        <div class="icon"><i class="fas fa-comments"></i></div>
        <h3><?= $fr ? 'Langue' : 'Languages' ?></h3>
        <p><?= $fr ? 'Nous répondons en français et en anglais.' : 'We respond in French and English.' ?></p>
      </div>
    </div>

    <!-- Form + Aside -->
    <div class="form-layout">
      <!-- Aside -->
      <div class="form-aside">
        <h2><?= $fr ? 'Envoyez-nous <span>un message</span>' : 'Send us <span>a message</span>' ?></h2>
        <p><?= $fr
          ? 'Remplissez le formulaire et un membre de notre équipe vous répondra dans les plus brefs délais. Tous les champs marqués sont requis.'
          : 'Fill out the form and a member of our team will get back to you as soon as possible. All marked fields are required.' ?></p>

        <ul class="response-times">
          <li><i class="fas fa-user-circle"></i> <?= $fr ? 'Questions générales : 24 h' : 'General questions: 24 hours' ?></li>
          <li><i class="fas fa-store"></i> <?= $fr ? 'Support vendeur / fournisseur : 12 h' : 'Seller / supplier support: 12 hours' ?></li>
          <li><i class="fas fa-truck"></i> <?= $fr ? 'Problèmes de livraison : 6 h' : 'Delivery issues: 6 hours' ?></li>
          <li><i class="fas fa-exclamation-circle"></i> <?= $fr ? 'Urgences techniques : 2 h' : 'Technical emergencies: 2 hours' ?></li>
          <li><i class="fas fa-building"></i> <?= $fr ? 'Partenariats & distribution : 48 h' : 'Partnerships & distribution: 48 hours' ?></li>
        </ul>
      </div>

      <!-- Form -->
      <div class="contact-form-wrap">
        <form id="contactForm" novalidate>
          <input type="hidden" name="<?= htmlspecialchars(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>" value="<?= htmlspecialchars(csrfToken()) ?>">

          <div class="form-row">
            <div class="form-group">
              <label for="name"><?= $fr ? 'Nom complet *' : 'Full name *' ?></label>
              <input type="text" id="name" name="name" required
                     placeholder="<?= $fr ? 'Votre nom' : 'Your name' ?>">
            </div>
            <div class="form-group">
              <label for="email"><?= $fr ? 'Courriel *' : 'Email *' ?></label>
              <input type="email" id="email" name="email" required
                     placeholder="<?= $fr ? 'votre@courriel.ca' : 'you@email.ca' ?>">
            </div>
          </div>

          <div class="form-group">
            <label for="subject"><?= $fr ? 'Sujet' : 'Subject' ?></label>
            <select id="subject" name="subject">
              <?php if ($fr): ?>
                <option value="">Sélectionnez un sujet</option>
                <option value="General Inquiry">Question générale</option>
                <option value="Order Issue">Problème de commande</option>
                <option value="Seller Support">Support vendeur</option>
                <option value="Supplier Support">Support fournisseur</option>
                <option value="Driver Support">Support chauffeur</option>
                <option value="Partnership">Partenariat & distribution</option>
                <option value="Technical Issue">Problème technique</option>
                <option value="Billing">Facturation</option>
                <option value="Other">Autre</option>
              <?php else: ?>
                <option value="">Select a subject</option>
                <option value="General Inquiry">General inquiry</option>
                <option value="Order Issue">Order issue</option>
                <option value="Seller Support">Seller support</option>
                <option value="Supplier Support">Supplier support</option>
                <option value="Driver Support">Driver support</option>
                <option value="Partnership">Partnership & distribution</option>
                <option value="Technical Issue">Technical issue</option>
                <option value="Billing">Billing</option>
                <option value="Other">Other</option>
              <?php endif; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="message"><?= $fr ? 'Message *' : 'Message *' ?></label>
            <textarea id="message" name="message" required
                      placeholder="<?= $fr ? 'Décrivez votre question ou problème...' : 'Describe your question or issue...' ?>"></textarea>
          </div>

          <button type="submit" class="btn-submit" id="submitBtn">
            <i class="fas fa-paper-plane"></i>
            <?= $fr ? 'Envoyer le message' : 'Send message' ?>
          </button>

          <div class="form-notice" id="formNotice"></div>
        </form>
      </div>
    </div>

  </div>
</section>

<!-- FAQ STRIP -->
<section class="faq-section">
  <div class="container">
    <h2 class="section-title"><?= $fr ? 'Questions fréquentes' : 'Frequently Asked Questions' ?></h2>
    <p class="section-sub"><?= $fr
      ? 'Trouvez rapidement des réponses aux questions les plus courantes.'
      : 'Quickly find answers to the most common questions.' ?></p>

    <div class="faq-grid">
      <div class="faq-item">
        <h4><?= $fr ? 'Comment puis-je suivre ma commande?' : 'How do I track my order?' ?></h4>
        <p><?= $fr
          ? 'Connectez-vous à votre compte acheteur et accédez à la section "Mes commandes" pour voir le statut en temps réel.'
          : 'Log in to your buyer account and go to "My Orders" to see real-time status updates.' ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? 'Comment devenir vendeur sur OCSAPP?' : 'How do I become a seller on OCSAPP?' ?></h4>
        <p><?= $fr
          ? 'Visitez notre page Vendeur Central et remplissez le formulaire d\'inscription. Notre équipe examine votre demande sous 1 à 3 jours ouvrables.'
          : 'Visit our Seller Central page and complete the registration form. Our team reviews your application within 1-3 business days.' ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? 'Quels sont les délais de livraison?' : 'What are the delivery timeframes?' ?></h4>
        <p><?= $fr
          ? 'Les livraisons sont effectuées le jour même ou le lendemain dans la région de Montréal et le West Island, selon votre zone.'
          : 'Deliveries are made same-day or next-day in the Montreal and West Island area, depending on your zone.' ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? 'Comment annuler ou modifier une commande?' : 'How do I cancel or modify an order?' ?></h4>
        <p><?= $fr
          ? 'Contactez-nous dès que possible via ce formulaire ou par courriel. Les modifications ne sont possibles que si la commande n\'est pas encore en préparation.'
          : 'Contact us as soon as possible via this form or by email. Changes are only possible if the order has not yet entered preparation.' ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? 'Comment devenir chauffeur?' : 'How do I become a driver?' ?></h4>
        <p><?= $fr
          ? 'Rendez-vous sur notre page Chauffeur Central pour postuler. Vous aurez besoin d\'un permis de conduire valide, d\'une assurance véhicule et d\'un téléphone intelligent.'
          : 'Visit our Driver Central page to apply. You\'ll need a valid driver\'s licence, vehicle insurance, and a smartphone.' ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? 'Acceptez-vous les partenariats de distribution?' : 'Do you accept distribution partnerships?' ?></h4>
        <p><?= $fr
          ? 'Oui — visitez notre page Distribution pour en savoir plus sur nos solutions B2B, ou sélectionnez "Partenariat" dans le formulaire ci-dessus.'
          : 'Yes — visit our Distribution page to learn about our B2B solutions, or select "Partnership" in the form above.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <div class="cta-inner">
      <h2><?= $fr ? 'Prêt à rejoindre OCSAPP?' : 'Ready to join OCSAPP?' ?></h2>
      <p><?= $fr
        ? 'Que vous soyez acheteur, vendeur, fournisseur ou chauffeur, notre plateforme est conçue pour vous.'
        : 'Whether you\'re a buyer, seller, supplier, or driver - our platform is built for you.' ?></p>
      <a href="<?= url('/register') ?>" class="btn-cta">
        <i class="fas fa-arrow-right"></i>
        <?= $fr ? 'Créer un compte gratuitement' : 'Create a free account' ?>
      </a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../components/footer.php'; ?>

<script>
(function () {
  const form    = document.getElementById('contactForm');
  const btn     = document.getElementById('submitBtn');
  const notice  = document.getElementById('formNotice');
  const lang    = <?= json_encode($currentLang) ?>;
  const isFr    = lang === 'fr';

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    notice.className = 'form-notice';
    notice.textContent = '';

    const name    = form.name.value.trim();
    const email   = form.email.value.trim();
    const message = form.message.value.trim();

    if (!name || !email || !message) {
      notice.className = 'form-notice error';
      notice.textContent = isFr ? 'Veuillez remplir tous les champs obligatoires.' : 'Please fill in all required fields.';
      return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + (isFr ? 'Envoi en cours...' : 'Sending...');

    try {
      const data = new FormData(form);
      const res  = await fetch('/contact/submit', { method: 'POST', body: data });
      const json = await res.json();

      if (json.success) {
        notice.className = 'form-notice success';
        notice.textContent = isFr
          ? 'Merci! Votre message a été envoyé. Nous vous répondrons bientôt.'
          : 'Thank you! Your message has been sent. We\'ll be in touch soon.';
        form.reset();
      } else {
        notice.className = 'form-notice error';
        notice.textContent = json.message || (isFr ? 'Une erreur est survenue.' : 'An error occurred.');
      }
    } catch (err) {
      notice.className = 'form-notice error';
      notice.textContent = isFr ? 'Erreur réseau. Veuillez réessayer.' : 'Network error. Please try again.';
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-paper-plane"></i> ' + (isFr ? 'Envoyer le message' : 'Send message');
    }
  });
})();
</script>
</body>
</html>
