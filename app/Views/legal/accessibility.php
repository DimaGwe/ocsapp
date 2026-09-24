<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$fr = ($currentLang === 'fr');
$cartCount = 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrfMeta() ?>
    <title><?= $fr ? 'Déclaration d\'accessibilité' : 'Accessibility Statement' ?> - OCSAPP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { background: #fff; color: #333; }
        .legal-wrap { max-width: 860px; margin: 40px auto; padding: 0 24px 60px; }
        .legal-card { background: #fff; border-radius: 16px; padding: 48px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        h1 { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        .updated { font-size: 13px; color: #888; margin-bottom: 32px; }
        h2 { font-size: 18px; font-weight: 600; margin: 28px 0 10px; color: #00b207; }
        .legal-card p, .legal-card li { font-size: 14px; line-height: 1.8; color: #555; margin-bottom: 10px; }
        .legal-card ul { padding-left: 20px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-left: 8px; }
        .status-partial { background: #fff3cd; color: #856404; }
        .status-compliant { background: #d1e7dd; color: #0f5132; }
        .status-pending { background: #f8d7da; color: #842029; }
        .contact-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 24px; margin-top: 24px; }
        .legal-card a { color: #00b207; }
        @media (max-width: 640px) { .legal-card { padding: 24px; } h1 { font-size: 22px; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../components/header.php'; ?>

<div class="legal-wrap">
    <div class="legal-card">

        <?php if ($fr): ?>

        <h1>Déclaration d'accessibilité</h1>
        <p class="updated">Dernière mise à jour : août 2026</p>

        <p>OCSAPP s'engage à rendre son site Web accessible à tous les utilisateurs, y compris les personnes en situation de handicap. Nous visons la conformité aux <strong>Règles pour l'accessibilité des contenus Web (WCAG) 2.0, niveau AA</strong>.</p>

        <h2>Notre engagement</h2>
        <p>Nous travaillons en continu pour améliorer l'accessibilité de notre plateforme et pour offrir une expérience équitable à tous nos utilisateurs, conformément à :</p>
        <ul>
            <li>La <em>Charte des droits et libertés de la personne</em> du Québec, qui interdit la discrimination fondée sur le handicap</li>
            <li>Le standard <em>SGQRI 008</em> sur l'accessibilité des sites Web du gouvernement du Québec</li>
            <li>La <em>Loi canadienne sur l'accessibilité</em> (fédérale), à titre de repère volontaire pour les organisations qui n'y sont pas autrement assujetties</li>
            <li>Les normes WCAG 2.0 AA du W3C</li>
        </ul>

        <h2>État de conformité actuel <span class="status-badge status-partial">Partiel</span></h2>
        <p>OCSAPP est <strong>partiellement conforme</strong> aux WCAG 2.0 niveau AA. Les zones déjà conformes comprennent :</p>
        <ul>
            <li>Navigation au clavier sur les pages principales</li>
            <li>Attribut <code>lang</code> sur toutes les pages</li>
            <li>Étiquettes de formulaire sur les formulaires d'authentification</li>
            <li>Contraste de couleur adéquat sur les éléments principaux</li>
            <li>Interface bilingue (français / anglais)</li>
        </ul>

        <h2>Limitations connues</h2>
        <p>Nous travaillons activement à corriger les éléments suivants :</p>
        <ul>
            <li>Certaines images de produits générées par des vendeurs tiers peuvent manquer de texte alternatif</li>
            <li>Certains composants de l'interface d'administration ne sont pas encore entièrement navigables au clavier</li>
        </ul>

        <h2>Mécanisme de retour d'information</h2>
        <p>Si vous rencontrez des obstacles d'accessibilité sur notre site, nous voulons le savoir. Contactez-nous :</p>
        <div class="contact-box">
            <p><strong>Courriel :</strong> <a href="mailto:accessibility@ocsapp.ca">accessibility@ocsapp.ca</a></p>
            <p><strong>Délai de réponse :</strong> Nous nous engageons à répondre dans les <strong>5 jours ouvrables</strong>.</p>
            <p><strong>Processus :</strong> Décrivez la difficulté rencontrée et la page concernée. Nous évaluerons le problème et vous informerons des mesures prises.</p>
        </div>

        <h2>Voies de recours</h2>
        <p>Si vous n'êtes pas satisfait de notre réponse, vous pouvez contacter :</p>
        <ul>
            <li>La <a href="https://www.cdpdj.qc.ca/" target="_blank" rel="noopener">Commission des droits de la personne et des droits de la jeunesse</a> (CDPDJ), l'organisme québécois de défense des droits de la personne</li>
            <li><a href="https://accessibilitycanada.ca/fr/" target="_blank" rel="noopener">Normes d'accessibilité Canada</a></li>
        </ul>

        <h2>Mises à jour</h2>
        <p>Cette déclaration est révisée annuellement ou lors de changements importants apportés à la plateforme.</p>

        <?php else: ?>

        <h1>Accessibility Statement</h1>
        <p class="updated">Last updated: August 2026</p>

        <p>OCSAPP is committed to ensuring digital accessibility for people with disabilities. We are continually working to improve the user experience for everyone and to apply the <strong>Web Content Accessibility Guidelines (WCAG) 2.0, Level AA</strong>.</p>

        <h2>Our Commitment</h2>
        <p>We aim to ensure our platform is accessible and usable by all visitors, in compliance with:</p>
        <ul>
            <li>Quebec's <em>Charter of human rights and freedoms</em>, which prohibits discrimination on the basis of disability</li>
            <li>Quebec's <em>SGQRI 008</em> web accessibility standard</li>
            <li>The <em>Accessible Canada Act</em> (federal), as a voluntary benchmark for organizations not otherwise bound by it</li>
            <li>W3C WCAG 2.0 Level AA guidelines</li>
        </ul>

        <h2>Current Compliance Status <span class="status-badge status-partial">Partial</span></h2>
        <p>OCSAPP is <strong>partially conformant</strong> with WCAG 2.0 Level AA. Areas already conformant include:</p>
        <ul>
            <li>Keyboard navigation on main public pages</li>
            <li>Language attribute (<code>lang</code>) set on all pages</li>
            <li>Proper form labels on authentication forms</li>
            <li>Adequate color contrast on primary UI elements</li>
            <li>Bilingual interface (French / English)</li>
        </ul>

        <h2>Known Limitations</h2>
        <p>We are actively working to address the following:</p>
        <ul>
            <li>Some product images uploaded by third-party sellers may lack alt text</li>
            <li>Certain admin-facing UI components are not yet fully keyboard navigable</li>
        </ul>

        <h2>Feedback Mechanism</h2>
        <p>If you encounter accessibility barriers on our site, please let us know. We welcome your feedback:</p>
        <div class="contact-box">
            <p><strong>Email:</strong> <a href="mailto:accessibility@ocsapp.ca">accessibility@ocsapp.ca</a></p>
            <p><strong>Response time:</strong> We are committed to responding within <strong>5 business days</strong>.</p>
            <p><strong>Process:</strong> Describe the barrier you encountered and the page where it occurred. We will assess the issue and inform you of the steps taken.</p>
        </div>

        <h2>Escalation</h2>
        <p>If you are not satisfied with our response, you may contact:</p>
        <ul>
            <li>The <a href="https://www.cdpdj.qc.ca/en" target="_blank" rel="noopener">Commission des droits de la personne et des droits de la jeunesse</a> (CDPDJ), Quebec's human rights commission</li>
            <li><a href="https://accessibilitycanada.ca/" target="_blank" rel="noopener">Accessibility Standards Canada</a></li>
        </ul>

        <h2>Updates to This Statement</h2>
        <p>This statement is reviewed annually or following significant changes to the platform.</p>

        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
