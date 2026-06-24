<?php
/**
 * Migration: Add French Privacy Policy + French Terms of Service to legal_content
 * Also fixes the typo "Privacy Policyee" in the EN title
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // ── Fix typo in EN privacy title ──────────────────────────────────────────
    $db->prepare("UPDATE legal_content SET title = 'Privacy Policy' WHERE page_type = 'privacy' AND language = 'en' AND title = 'Privacy Policyee'")->execute();

    // ── French Privacy Policy ─────────────────────────────────────────────────
    $checkFrPrivacy = $db->prepare("SELECT COUNT(*) FROM legal_content WHERE page_type = 'privacy' AND language = 'fr'");
    $checkFrPrivacy->execute();
    if ((int)$checkFrPrivacy->fetchColumn() === 0) {

        $privacyFr = <<<HTML
<div style="background:#f0fdf4;border-left:4px solid #00b207;padding:20px;margin:20px 0;border-radius:4px;">
<p><strong>Votre vie privée nous tient à cœur</strong></p>
<p>OCSAPP s'engage à protéger vos renseignements personnels. La présente politique de confidentialité explique comment nous collectons, utilisons, divulguons et protégeons vos données lorsque vous utilisez notre plateforme.</p>
</div>

<h2>1. Renseignements que nous collectons</h2>
<h3>1.1 Renseignements personnels</h3>
<p>Nous collectons les renseignements personnels que vous fournissez volontairement lorsque vous :</p>
<ul>
<li>Créez un compte (nom, adresse courriel, numéro de téléphone)</li>
<li>Effectuez un achat (adresse de livraison, informations de paiement)</li>
<li>Contactez le service à la clientèle</li>
<li>Vous abonnez à des infolettres ou à des promotions</li>
<li>Laissez des avis ou des évaluations</li>
</ul>
<h3>1.2 Renseignements commerciaux (vendeurs)</h3>
<p>Pour les comptes vendeurs, nous collectons :</p>
<ul>
<li>Les coordonnées d'inscription commerciale</li>
<li>Les numéros d'identification fiscale</li>
<li>Les informations bancaires pour les paiements</li>
<li>Les coordonnées professionnelles</li>
<li>Les données sur les produits et les stocks</li>
</ul>
<h3>1.3 Renseignements collectés automatiquement</h3>
<p>Lorsque vous utilisez notre plateforme, nous collectons automatiquement :</p>
<ul>
<li>Les informations sur l'appareil (adresse IP, type de navigateur, type d'appareil)</li>
<li>Les données d'utilisation (pages visitées, temps passé, schémas de clics)</li>
<li>Les données de localisation (avec votre autorisation)</li>
<li>Les témoins de connexion (cookies) et technologies similaires</li>
</ul>

<h2>2. Comment nous utilisons vos renseignements</h2>
<h3>2.1 Prestation de services</h3>
<ul>
<li>Traiter et exécuter les commandes</li>
<li>Gérer votre compte</li>
<li>Traiter les paiements et les remboursements</li>
<li>Assurer le service à la clientèle</li>
<li>Envoyer les confirmations de commande et les mises à jour</li>
</ul>
<h3>2.2 Amélioration de la plateforme</h3>
<ul>
<li>Analyser les habitudes d'utilisation</li>
<li>Développer de nouvelles fonctionnalités</li>
<li>Améliorer l'expérience utilisateur</li>
<li>Mener des recherches et des analyses</li>
</ul>
<h3>2.3 Communications et marketing</h3>
<ul>
<li>Envoyer des courriels promotionnels (avec votre consentement)</li>
<li>Fournir des recommandations personnalisées</li>
<li>Vous informer de l'activité de votre compte</li>
</ul>
<h3>2.4 Sécurité et prévention de la fraude</h3>
<ul>
<li>Détecter et prévenir les transactions frauduleuses</li>
<li>Surveiller les activités suspectes</li>
<li>Faire respecter nos conditions d'utilisation</li>
<li>Respecter les obligations légales</li>
</ul>

<h2>3. Communication et divulgation des renseignements</h2>
<h3>3.1 Avec les vendeurs</h3>
<p>Lors d'un achat, nous communiquons au vendeur les renseignements nécessaires :</p>
<ul>
<li>Votre nom et adresse de livraison</li>
<li>Les coordonnées pour la livraison</li>
<li>Les détails de la commande</li>
</ul>
<h3>3.2 Avec les fournisseurs de services</h3>
<ul>
<li>Processeurs de paiement (Stripe, PayPal, etc.)</li>
<li>Services de livraison</li>
<li>Fournisseurs de services courriel</li>
<li>Fournisseurs d'hébergement infonuagique (AWS)</li>
<li>Fournisseurs d'analyse</li>
</ul>
<h3>3.3 Exigences légales</h3>
<p>Nous pouvons divulguer des renseignements lorsque la loi l'exige ou pour :</p>
<ul>
<li>Nous conformer à des procédures légales ou à des demandes gouvernementales</li>
<li>Faire respecter nos conditions d'utilisation</li>
<li>Protéger nos droits, nos biens ou notre sécurité</li>
<li>Prévenir la fraude ou les menaces à la sécurité</li>
</ul>

<h2>4. Sécurité des données</h2>
<p>Nous mettons en œuvre des mesures de sécurité conformes aux normes de l'industrie :</p>
<ul>
<li>Chiffrement SSL/TLS pour la transmission des données</li>
<li>Stockage chiffré des données sensibles</li>
<li>Traitement sécurisé des paiements (conforme à la norme PCI DSS)</li>
<li>Audits de sécurité et mises à jour réguliers</li>
<li>Contrôles d'accès et authentification</li>
</ul>
<div style="background:#f0fdf4;border-left:4px solid #00b207;padding:20px;margin:20px 0;border-radius:4px;">
<p><strong>Important :</strong> Bien que nous nous efforcions de protéger vos renseignements, aucune méthode de transmission ou de stockage n'est fiable à 100 %. Nous ne pouvons pas garantir une sécurité absolue.</p>
</div>

<h2>5. Vos droits et vos choix</h2>
<h3>5.1 Accès et rectification</h3>
<p>Vous avez le droit de :</p>
<ul>
<li>Accéder à vos renseignements personnels</li>
<li>Corriger les données inexactes</li>
<li>Mettre à jour les détails de votre compte</li>
<li>Demander une copie de vos données</li>
</ul>
<h3>5.2 Retrait du consentement aux communications marketing</h3>
<ul>
<li>Vous désabonner des courriels promotionnels via le lien dans chaque courriel</li>
<li>Mettre à jour vos préférences de communication dans les paramètres du compte</li>
<li>Nous contacter directement pour vous retirer des communications marketing</li>
</ul>
<h3>5.3 Suppression du compte</h3>
<p>Vous pouvez demander la suppression de votre compte en nous contactant. Veuillez noter :</p>
<ul>
<li>Nous pouvons conserver certains renseignements à des fins légales et commerciales</li>
<li>L'historique des commandes peut être conservé à des fins comptables et fiscales</li>
<li>Les comptes supprimés ne peuvent pas être récupérés</li>
</ul>

<h2>6. Témoins de connexion (cookies)</h2>
<h3>6.1 Types de témoins utilisés</h3>
<ul>
<li><strong>Témoins essentiels :</strong> Nécessaires au fonctionnement de la plateforme</li>
<li><strong>Témoins de performance :</strong> Nous aident à comprendre l'utilisation de la plateforme</li>
<li><strong>Témoins fonctionnels :</strong> Mémorisent vos préférences</li>
<li><strong>Témoins publicitaires :</strong> Diffusent des publicités pertinentes</li>
</ul>
<h3>6.2 Gestion des témoins</h3>
<p>Vous pouvez contrôler les témoins via les paramètres de votre navigateur. La désactivation des témoins peut affecter le fonctionnement de la plateforme.</p>

<h2>7. Protection des renseignements des mineurs</h2>
<p>Notre plateforme n'est pas destinée aux personnes de moins de 18 ans. Nous ne collectons pas sciemment de renseignements auprès de mineurs. Si vous pensez qu'un enfant nous a fourni des renseignements personnels, veuillez nous contacter immédiatement.</p>

<h2>8. Transferts internationaux de données</h2>
<p>Vos renseignements peuvent être transférés et traités dans des pays autres que le Canada. Nous nous assurons que des mesures de protection appropriées sont en place.</p>

<h2>9. Conservation des données</h2>
<ul>
<li>Informations du compte : Comptes actifs + 7 ans après la fermeture</li>
<li>Enregistrements de transactions : 7 ans (exigences fiscales et comptables)</li>
<li>Données marketing : Jusqu'au retrait du consentement + 30 jours</li>
</ul>

<h2>10. Lois canadiennes sur la protection des renseignements personnels</h2>
<p>Nous nous conformons aux lois canadiennes sur la protection des renseignements personnels, notamment :</p>
<ul>
<li>Loi sur la protection des renseignements personnels et les documents électroniques (LPRPDE)</li>
<li>Loi 25 du Québec (anciennement le projet de loi 64)</li>
<li>La <em>Loi sur la protection des renseignements personnels dans le secteur privé</em> du Québec (L.R.Q., c. P-39.1)</li>
</ul>
<p>Conformément à la Loi 25, vous pouvez exercer vos droits en matière de protection des renseignements personnels en contactant notre responsable de la protection des renseignements personnels.</p>

<h2>11. Modifications de la politique de confidentialité</h2>
<p>Nous pouvons mettre à jour la présente politique de temps à autre. Les modifications seront publiées sur cette page avec une date de mise à jour. L'utilisation continue de la plateforme après les modifications constitue une acceptation de la politique mise à jour.</p>

<h2>12. Nous contacter</h2>
<p>Pour toute question relative à cette politique ou pour exercer vos droits :</p>
<ul>
<li><strong>Responsable de la protection des renseignements personnels :</strong> <a style="color:#00b207;" href="mailto:privacy@ocsapp.ca">privacy@ocsapp.ca</a></li>
<li><strong>Soutien général :</strong> <a style="color:#00b207;" href="mailto:support@ocsapp.ca">support@ocsapp.ca</a></li>
<li><strong>Téléphone :</strong> +1 (888) OCS-APP1</li>
</ul>
<div style="background:#f0fdf4;border-left:4px solid #00b207;padding:20px;margin:40px 0 0;border-radius:4px;">
<p><strong>Votre consentement</strong></p>
<p>En utilisant OCSAPP, vous consentez à la collecte, à l'utilisation et à la divulgation de vos renseignements tels que décrits dans la présente politique de confidentialité.</p>
</div>
HTML;

        $db->prepare("
            INSERT INTO legal_content (page_type, language, title, content, meta_description, version, is_published, published_at)
            VALUES ('privacy', 'fr', 'Politique de confidentialité', ?, 'Politique de confidentialité OCSAPP — Comment nous collectons, utilisons et protégeons vos renseignements personnels.', 1, 1, NOW())
        ")->execute([$privacyFr]);
        echo "INSERT: privacy/fr\n";
    } else {
        echo "SKIP: privacy/fr already exists\n";
    }

    // ── French Terms of Service ───────────────────────────────────────────────
    $checkFrTerms = $db->prepare("SELECT COUNT(*) FROM legal_content WHERE page_type = 'terms' AND language = 'fr'");
    $checkFrTerms->execute();
    if ((int)$checkFrTerms->fetchColumn() === 0) {

        $termsFr = <<<HTML
<div style="background:#f0fdf4;border-left:4px solid #00b207;padding:20px;margin:20px 0;border-radius:4px;">
<p><strong>Bienvenue sur OCSAPP !</strong></p>
<p>Les présentes conditions d'utilisation régissent votre utilisation de notre plateforme. En accédant à OCSAPP ou en l'utilisant, vous acceptez d'être lié par ces conditions. Veuillez les lire attentivement.</p>
</div>

<h2>1. Acceptation des conditions</h2>
<p>En créant un compte, en accédant à OCSAPP (« la Plateforme ») ou en l'utilisant, vous acceptez de vous conformer aux présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser nos services.</p>

<h2>2. Définitions</h2>
<ul>
<li><strong>« Plateforme »</strong> désigne OCSAPP, y compris le site Web, les applications mobiles et tous les services associés.</li>
<li><strong>« Utilisateur »</strong> désigne toute personne accédant à la Plateforme ou l'utilisant.</li>
<li><strong>« Acheteur »</strong> désigne les utilisateurs qui achètent des produits via la Plateforme.</li>
<li><strong>« Vendeur »</strong> désigne les entreprises canadiennes ou québécoises enregistrées qui répertorient et vendent des produits sur la Plateforme.</li>
<li><strong>« Produits »</strong> désigne les biens mis en vente sur la Plateforme.</li>
</ul>

<h2>3. Inscription au compte</h2>
<h3>3.1 Comptes acheteurs</h3>
<ul>
<li>Vous devez avoir au moins 18 ans pour créer un compte acheteur</li>
<li>Vous devez fournir des renseignements exacts, à jour et complets</li>
<li>Vous êtes responsable du maintien de la confidentialité de vos identifiants de connexion</li>
<li>Vous êtes responsable de toutes les activités effectuées dans votre compte</li>
<li>Les comptes acheteurs sont activés immédiatement à l'inscription</li>
</ul>
<h3>3.2 Comptes vendeurs</h3>
<ul>
<li>Les vendeurs doivent être des entreprises canadiennes ou québécoises dûment enregistrées</li>
<li>Toutes les candidatures de vendeurs font l'objet d'un examen et d'une approbation</li>
<li>Les vendeurs doivent fournir des documents d'enregistrement commerciaux valides</li>
<li>Les vendeurs doivent se conformer à toutes les lois et réglementations applicables</li>
<li>OCSAPP se réserve le droit de rejeter toute candidature de vendeur</li>
<li>Les comptes vendeurs ne sont activés qu'après approbation administrative</li>
</ul>

<h2>4. Conduite des utilisateurs</h2>
<p>Vous vous engagez à ne pas :</p>
<ul>
<li>Enfreindre les lois, réglementations ou droits de tiers</li>
<li>Publier des contenus faux, inexacts, trompeurs ou frauduleux</li>
<li>Adopter tout comportement de harcèlement, d'abus ou de menace</li>
<li>Tenter d'obtenir un accès non autorisé à la Plateforme ou aux comptes d'autres utilisateurs</li>
<li>Distribuer des virus, des logiciels malveillants ou tout autre code nuisible</li>
<li>Utiliser la Plateforme à des fins illégales ou non autorisées</li>
<li>Contourner des fonctions de sécurité ou des restrictions</li>
</ul>

<h2>5. Obligations des vendeurs</h2>
<h3>5.1 Fiches produits</h3>
<ul>
<li>Toutes les informations sur les produits doivent être exactes et véridiques</li>
<li>Les images des produits doivent représenter fidèlement les articles mis en vente</li>
<li>Les prix doivent être clairement indiqués et inclure toutes les taxes applicables</li>
<li>Les vendeurs doivent maintenir des niveaux de stock exacts</li>
</ul>
<h3>5.2 Exécution des commandes</h3>
<ul>
<li>Les vendeurs doivent exécuter les commandes en temps opportun</li>
<li>Les vendeurs doivent fournir des informations de suivi lorsqu'elles sont disponibles</li>
<li>Les vendeurs sont responsables de l'emballage sécurisé des produits</li>
<li>Les vendeurs doivent traiter les demandes des clients de manière professionnelle</li>
</ul>
<h3>5.3 Articles interdits</h3>
<p>Les vendeurs ne peuvent pas inscrire :</p>
<ul>
<li>Des articles illégaux ou réglementés</li>
<li>Des produits contrefaits ou non autorisés</li>
<li>Des matières dangereuses sans certification appropriée</li>
<li>Des articles qui portent atteinte aux droits de propriété intellectuelle</li>
<li>Tout produit interdit par la loi canadienne</li>
</ul>

<h2>6. Obligations des acheteurs</h2>
<ul>
<li>Fournir des informations de livraison et de paiement exactes</li>
<li>Effectuer les paiements en temps voulu pour les achats</li>
<li>Accepter la livraison des produits commandés</li>
<li>Évaluer les produits de façon juste et honnête</li>
<li>Contacter les vendeurs ou le soutien pour tout problème avant de déposer un litige</li>
</ul>

<h2>7. Paiements et frais</h2>
<h3>7.1 Paiements des acheteurs</h3>
<ul>
<li>Tous les prix sont en dollars canadiens (CAD)</li>
<li>Le paiement est traité à la caisse</li>
<li>Nous acceptons les principales cartes de crédit et autres modes de paiement approuvés</li>
<li>Les acheteurs sont responsables de toutes les taxes applicables (TPS/TVQ)</li>
</ul>
<h3>7.2 Frais des vendeurs</h3>
<ul>
<li>OCSAPP peut facturer des frais de commission sur les ventes</li>
<li>La structure des frais sera communiquée aux vendeurs lors de l'approbation du compte</li>
<li>Les vendeurs recevront leur paiement après la finalisation de la commande</li>
<li>OCSAPP se réserve le droit de modifier la structure des frais moyennant un préavis</li>
</ul>

<h2>8. Retours et remboursements</h2>
<ul>
<li>Les politiques de retour sont définies par chaque vendeur</li>
<li>Les acheteurs doivent consulter la politique de retour du vendeur avant l'achat</li>
<li>OCSAPP peut servir de médiateur dans les litiges entre acheteurs et vendeurs</li>
<li>Veuillez consulter notre <a style="color:#00b207;font-weight:600;" href="/returns">Politique de retour et de remboursement</a> pour plus de détails</li>
</ul>

<h2>9. Propriété intellectuelle</h2>
<ul>
<li>Tout le contenu de la Plateforme, y compris les logos, les designs et les logiciels, appartient à OCSAPP</li>
<li>Les utilisateurs conservent la propriété des contenus qu'ils téléchargent</li>
<li>En téléchargeant du contenu, les utilisateurs accordent à OCSAPP une licence pour l'utiliser, l'afficher et le distribuer</li>
<li>Les utilisateurs ne doivent pas porter atteinte aux droits de propriété intellectuelle d'autrui</li>
</ul>

<h2>10. Vie privée et protection des données</h2>
<p>Votre vie privée est importante pour nous. Veuillez consulter notre <a style="color:#00b207;font-weight:600;" href="/privacy">Politique de confidentialité</a> pour comprendre comment nous collectons, utilisons et protégeons vos renseignements personnels.</p>

<h2>11. Résolution des litiges</h2>
<ul>
<li>Les utilisateurs s'engagent à tenter de résoudre les litiges à l'amiable</li>
<li>OCSAPP peut fournir des services de médiation</li>
<li>Les litiges seront régis par les lois du Canada et de la province de Québec</li>
<li>Les utilisateurs acceptent l'arbitrage obligatoire pour les litiges non résolus</li>
</ul>

<h2>12. Limitation de responsabilité</h2>
<p>OCSAPP est une plateforme mettant en relation acheteurs et vendeurs. Nous ne sommes pas responsables de :</p>
<ul>
<li>La qualité, la sécurité ou la légalité des produits répertoriés</li>
<li>L'exactitude des fiches produits ou du contenu des utilisateurs</li>
<li>La capacité des vendeurs à exécuter les commandes</li>
<li>La capacité des acheteurs à effectuer des transactions</li>
<li>Tout litige entre acheteurs et vendeurs</li>
<li>Tout dommage indirect, accessoire ou consécutif</li>
</ul>

<h2>13. Résiliation du compte</h2>
<p>OCSAPP se réserve le droit de :</p>
<ul>
<li>Suspendre ou résilier des comptes pour violation des présentes conditions</li>
<li>Retirer les fiches produits qui enfreignent nos politiques</li>
<li>Refuser le service pour n'importe quelle raison</li>
<li>Modifier ou interrompre la Plateforme à tout moment</li>
</ul>

<h2>14. Modifications des conditions</h2>
<p>Nous nous réservons le droit de modifier les présentes conditions à tout moment. Les modifications prennent effet immédiatement après leur publication. L'utilisation continue de la Plateforme constitue une acceptation des conditions modifiées.</p>

<h2>15. Coordonnées</h2>
<ul>
<li><strong>Courriel :</strong> <a style="color:#00b207;" href="mailto:legal@ocsapp.ca">legal@ocsapp.ca</a></li>
<li><strong>Soutien :</strong> <a style="color:#00b207;" href="mailto:support@ocsapp.ca">support@ocsapp.ca</a></li>
<li><strong>Téléphone :</strong> +1 (888) OCS-APP1</li>
</ul>

<h2>16. Droit applicable</h2>
<p>Les présentes conditions d'utilisation sont régies et interprétées conformément aux lois du Canada et de la province de Québec, sans égard aux dispositions relatives aux conflits de lois.</p>

<div style="background:#f0fdf4;border-left:4px solid #00b207;padding:20px;margin:40px 0 0;border-radius:4px;">
<p><strong>En utilisant OCSAPP, vous reconnaissez avoir lu, compris et accepté d'être lié par les présentes conditions d'utilisation.</strong></p>
</div>
HTML;

        $db->prepare("
            INSERT INTO legal_content (page_type, language, title, content, meta_description, version, is_published, published_at)
            VALUES ('terms', 'fr', 'Conditions d\'utilisation', ?, 'Conditions d\'utilisation OCSAPP — Règles et politiques régissant l\'utilisation de notre plateforme de commerce.', 1, 1, NOW())
        ")->execute([$termsFr]);
        echo "INSERT: terms/fr\n";
    } else {
        echo "SKIP: terms/fr already exists\n";
    }

    echo "\nDone.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
