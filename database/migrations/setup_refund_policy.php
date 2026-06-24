<?php
/**
 * Migration: Seed Return & Refund Policy into legal_content table (EN + FR)
 * Also adds 'returns' translation key for footer link
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // ---- 1. Add 'returns' footer translation key ----
    $check = $db->prepare("SELECT COUNT(*) FROM translations WHERE `key` = 'returns'");
    $check->execute();
    if ((int)$check->fetchColumn() === 0) {
        $db->prepare("INSERT INTO translations (`key`, category, en, fr) VALUES ('returns', 'footer', 'Returns', 'Retours')")->execute();
        echo "INSERT: translations.returns\n";
    } else {
        echo "SKIP: translations.returns already exists\n";
    }

    // ---- 2. English refund policy ----
    $checkEn = $db->prepare("SELECT COUNT(*) FROM legal_content WHERE page_type = 'refund' AND language = 'en'");
    $checkEn->execute();
    if ((int)$checkEn->fetchColumn() === 0) {
        $contentEn = <<<HTML
<div class="highlight-box">
  <p><strong>Our Commitment to Your Satisfaction</strong></p>
  <p>At OCSAPP, we are committed to providing a positive shopping experience. If you are not entirely satisfied with your purchase, we are here to help.</p>
</div>

<h2>1. Return Window</h2>
<p>You have <strong>14 days</strong> from the date you receive your order to request a return or refund. After this period, we are generally unable to accept returns unless required by applicable law.</p>

<h2>2. Eligible Items for Return</h2>
<p>To be eligible for a return, the item must:</p>
<ul>
  <li>Be in its original condition — unused and undamaged</li>
  <li>Be in the original packaging with all tags attached</li>
  <li>Be accompanied by a receipt or proof of purchase</li>
  <li>Not be listed as a non-returnable item (see below)</li>
</ul>

<h2>3. Non-Returnable Items</h2>
<div class="warning-box">
  <p>The following items <strong>cannot be returned</strong> due to their nature:</p>
  <ul>
    <li>Perishable goods (fresh food, flowers, etc.)</li>
    <li>Opened health and personal care products</li>
    <li>Custom or personalized items</li>
    <li>Undergarments and swimwear for hygiene reasons</li>
    <li>Digital gift cards and digital downloads</li>
    <li>Final sale or clearance items (clearly marked as such)</li>
  </ul>
</div>

<h2>4. How to Initiate a Return</h2>
<ol>
  <li>Log in to your OCSAPP account</li>
  <li>Go to <strong>My Orders</strong> and select the relevant order</li>
  <li>Click <strong>Request a Return</strong> and provide the reason</li>
  <li>We will send a confirmation within 1–2 business days</li>
  <li>Return the item following the instructions provided</li>
</ol>
<p>You can also contact us at <strong>support@ocsapp.ca</strong> for assistance.</p>

<h2>5. Refunds</h2>
<p>Once your return is received and inspected, we will notify you of the approval or rejection of your refund. If approved, your refund will be processed and applied to your original payment method within <strong>5–10 business days</strong>.</p>
<ul>
  <li><strong>Credit/Debit Card:</strong> 3–7 business days depending on your bank</li>
  <li><strong>PayPal:</strong> 3–5 business days</li>
  <li><strong>Interac e-Transfer:</strong> 1–3 business days</li>
</ul>

<h2>6. Exchanges</h2>
<p>If you need to exchange a defective or damaged item, please contact us at <strong>support@ocsapp.ca</strong>. We will replace the same item if available, or issue a full refund.</p>

<h2>7. Defective or Incorrect Items</h2>
<p>If you received a defective, damaged, or incorrect item, please contact us immediately. We will cover return shipping costs and send a replacement or process a full refund — whichever you prefer.</p>

<h2>8. Return Shipping</h2>
<p>Return shipping costs are the customer's responsibility unless the item is defective, damaged, or incorrect. In those cases, OCSAPP covers the return shipping.</p>

<h2>9. Your Rights Under Canadian Law</h2>
<p>This policy complies with the <em>Consumer Protection Act</em> (Quebec, L.R.Q., c. P-40.1) and applicable federal consumer protection legislation. In the event of any conflict between this policy and applicable law, the law prevails.</p>

<h2>10. Contact Us</h2>
<ul>
  <li><strong>Email:</strong> support@ocsapp.ca</li>
  <li><strong>Phone:</strong> 1 (888) OCS-APP1</li>
  <li><strong>Business hours:</strong> Mon–Fri, 9 AM–5 PM (Eastern Time)</li>
</ul>
HTML;

        $db->prepare("
            INSERT INTO legal_content (page_type, language, title, content, meta_description, version, is_published, published_at)
            VALUES ('refund', 'en', 'Return & Refund Policy', ?, 'OCSAPP Return and Refund Policy — 14-day returns, full refunds on defective items.', 1, 1, NOW())
        ")->execute([$contentEn]);
        echo "INSERT: legal_content refund EN\n";
    } else {
        echo "SKIP: legal_content refund EN already exists\n";
    }

    // ---- 3. French refund policy ----
    $checkFr = $db->prepare("SELECT COUNT(*) FROM legal_content WHERE page_type = 'refund' AND language = 'fr'");
    $checkFr->execute();
    if ((int)$checkFr->fetchColumn() === 0) {
        $contentFr = <<<HTML
<div class="highlight-box">
  <p><strong>Notre engagement envers votre satisfaction</strong></p>
  <p>Chez OCSAPP, nous nous engageons à offrir une expérience d'achat positive. Si vous n'êtes pas entièrement satisfait de votre commande, nous sommes là pour vous aider.</p>
</div>

<h2>1. Délai de retour</h2>
<p>Vous disposez de <strong>14 jours</strong> suivant la réception de votre commande pour demander un retour ou un remboursement. Passé ce délai, nous ne sommes généralement pas en mesure d'accepter les retours, sauf dans les cas prévus par la loi.</p>

<h2>2. Articles admissibles aux retours</h2>
<p>Pour être admissible à un retour, l'article doit :</p>
<ul>
  <li>Être dans son état d'origine, non utilisé et non endommagé</li>
  <li>Se trouver dans l'emballage d'origine avec toutes les étiquettes</li>
  <li>Être accompagné d'un reçu ou d'une preuve d'achat</li>
  <li>Ne pas faire partie des articles non retournables ci-dessous</li>
</ul>

<h2>3. Articles non retournables</h2>
<div class="warning-box">
  <p>Les articles suivants <strong>ne peuvent pas être retournés</strong> en raison de leur nature :</p>
  <ul>
    <li>Denrées périssables (aliments frais, fleurs, etc.)</li>
    <li>Produits de santé et de beauté déjà ouverts</li>
    <li>Articles personnalisés ou sur mesure</li>
    <li>Sous-vêtements et articles de bain pour des raisons d'hygiène</li>
    <li>Cartes-cadeaux numériques et téléchargements numériques</li>
    <li>Articles en vente finale ou en liquidation (clairement indiqués)</li>
  </ul>
</div>

<h2>4. Comment initier un retour</h2>
<ol>
  <li>Connectez-vous à votre compte OCSAPP</li>
  <li>Accédez à <strong>Mes commandes</strong> et sélectionnez la commande concernée</li>
  <li>Cliquez sur <strong>Demander un retour</strong> et indiquez le motif</li>
  <li>Nous vous enverrons une confirmation sous 1 à 2 jours ouvrables</li>
  <li>Retournez l'article selon les instructions fournies</li>
</ol>
<p>Vous pouvez également nous contacter à <strong>support@ocsapp.ca</strong> pour toute assistance.</p>

<h2>5. Remboursements</h2>
<p>Une fois votre retour reçu et inspecté, nous vous informerons de l'approbation ou du rejet de votre remboursement. Si approuvé, votre remboursement sera traité dans un délai de <strong>5 à 10 jours ouvrables</strong>.</p>
<ul>
  <li><strong>Carte de crédit/débit :</strong> 3 à 7 jours ouvrables selon votre institution financière</li>
  <li><strong>PayPal :</strong> 3 à 5 jours ouvrables</li>
  <li><strong>Virement Interac :</strong> 1 à 3 jours ouvrables</li>
</ul>

<h2>6. Échanges</h2>
<p>Si vous souhaitez échanger un article défectueux ou endommagé, contactez-nous à <strong>support@ocsapp.ca</strong>. Nous remplacerons l'article identique si disponible, ou vous offrirons un remboursement complet.</p>

<h2>7. Articles défectueux ou incorrects</h2>
<p>Si vous avez reçu un article défectueux, endommagé ou incorrect, contactez-nous immédiatement. Nous couvrirons les frais de retour et vous enverrons un remplacement ou procéderons à un remboursement complet, selon votre préférence.</p>

<h2>8. Frais de retour</h2>
<p>Les frais de retour sont à la charge du client, sauf si l'article est défectueux, endommagé ou incorrect. Dans ces cas, OCSAPP prend en charge les frais d'expédition de retour.</p>

<h2>9. Vos droits en vertu de la loi québécoise</h2>
<p>Cette politique est conforme à la <em>Loi sur la protection du consommateur</em> du Québec (L.R.Q., c. P-40.1). En cas de conflit entre cette politique et la loi applicable, la loi prévaut.</p>

<h2>10. Nous contacter</h2>
<ul>
  <li><strong>Courriel :</strong> support@ocsapp.ca</li>
  <li><strong>Téléphone :</strong> 1 (888) OCS-APP1</li>
  <li><strong>Heures d'ouverture :</strong> Lun–Ven, 9h–17h (heure de l'Est)</li>
</ul>
HTML;

        $db->prepare("
            INSERT INTO legal_content (page_type, language, title, content, meta_description, version, is_published, published_at)
            VALUES ('refund', 'fr', 'Politique de retour et de remboursement', ?, 'Politique de retour et de remboursement OCSAPP — 14 jours pour les retours, remboursement complet sur les articles défectueux.', 1, 1, NOW())
        ")->execute([$contentFr]);
        echo "INSERT: legal_content refund FR\n";
    } else {
        echo "SKIP: legal_content refund FR already exists\n";
    }

    echo "\nDone — Refund policy seeded in EN and FR.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
