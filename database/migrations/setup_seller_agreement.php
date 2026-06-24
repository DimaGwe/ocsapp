<?php
/**
 * Migration: Setup Seller Agreement
 * 1. Add 'seller_agreement' to legal_content.page_type ENUM
 * 2. Add seller_agreement_accepted_at column to users table
 * 3. Seed EN + FR seller agreement content
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // ── 1. Alter ENUM to add seller_agreement ──────────────────────────────
    $db->exec("ALTER TABLE legal_content MODIFY COLUMN page_type ENUM('terms','privacy','cookies','refund','shipping','seller_agreement') NOT NULL");
    echo "ALTER: legal_content.page_type ENUM updated\n";

    // ── 2. Add seller_agreement_accepted_at to users ───────────────────────
    $colCheck = $db->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'seller_agreement_accepted_at'");
    $colCheck->execute();
    if ((int)$colCheck->fetchColumn() === 0) {
        $db->exec("ALTER TABLE users ADD COLUMN seller_agreement_accepted_at TIMESTAMP NULL DEFAULT NULL AFTER verification_submitted_at");
        echo "ALTER: users.seller_agreement_accepted_at added\n";
    } else {
        echo "SKIP: users.seller_agreement_accepted_at already exists\n";
    }

    // ── 3. Seed EN seller agreement ────────────────────────────────────────
    $checkEn = $db->prepare("SELECT COUNT(*) FROM legal_content WHERE page_type = 'seller_agreement' AND language = 'en'");
    $checkEn->execute();
    if ((int)$checkEn->fetchColumn() === 0) {
        $contentEn = <<<HTML
<div style="background:#f0fdf4;border-left:4px solid #00b207;padding:20px;margin:20px 0;border-radius:4px;">
<p><strong>Important — Please Read Before Registering as a Seller</strong></p>
<p>This Seller Agreement sets out the terms under which you operate a store on OCSAPP. By checking "I agree" during registration, you enter into a binding agreement with OCSAPP Inc.</p>
</div>

<h2>1. Eligibility</h2>
<p>To sell on OCSAPP, you must:</p>
<ul>
<li>Be a legally registered business in Canada (federal or provincial)</li>
<li>Have a valid GST/HST number (if annual revenue exceeds $30,000 CAD)</li>
<li>Be at least 18 years of age</li>
<li>Provide accurate business registration documents for verification</li>
<li>Operate from a verifiable Canadian address</li>
</ul>

<h2>2. Commission & Fees</h2>
<p>OCSAPP charges the following on each completed sale:</p>
<ul>
<li><strong>Platform Commission:</strong> 8% of the order subtotal (before taxes)</li>
<li><strong>Payment Processing:</strong> Passed through at cost (Stripe: 2.9% + $0.30 CAD per transaction)</li>
<li><strong>No monthly subscription fee</strong> during Phase 1 (invite-only beta)</li>
</ul>
<p>OCSAPP reserves the right to update the commission structure with <strong>30 days' written notice</strong> to active sellers.</p>

<h2>3. Seller Obligations</h2>
<h3>3.1 Product Listings</h3>
<ul>
<li>All products must be accurately described, including condition, dimensions, and materials</li>
<li>Photos must be original or properly licensed images of the actual product</li>
<li>Prices must be in Canadian Dollars (CAD) and include all applicable taxes at checkout</li>
<li>Inventory must be kept up to date to avoid overselling</li>
</ul>
<h3>3.2 Order Fulfillment</h3>
<ul>
<li>Orders must be confirmed or marked as ready within <strong>24 hours</strong> of placement</li>
<li>Products must be packaged securely and ready for pickup by OCSAPP drivers on the scheduled date</li>
<li>Sellers are responsible for the accuracy and safety of packaged orders</li>
</ul>
<h3>3.3 Customer Service</h3>
<ul>
<li>Sellers must respond to buyer inquiries within 48 hours</li>
<li>Sellers must honor their stated return policy</li>
<li>Disputes must be handled professionally; OCSAPP may mediate when necessary</li>
</ul>

<h2>4. Prohibited Products</h2>
<p>The following may not be listed on OCSAPP under any circumstances:</p>
<ul>
<li>Illegal or controlled substances (drugs, weapons, hazardous materials)</li>
<li>Counterfeit, pirated, or stolen goods</li>
<li>Products that infringe on trademarks, patents, or copyrights</li>
<li>Adult-only content without OCSAPP's explicit prior approval</li>
<li>Live animals</li>
<li>Any product prohibited by Canadian federal or Quebec provincial law</li>
</ul>

<h2>5. Payments to Sellers</h2>
<ul>
<li>Seller payouts are processed <strong>weekly</strong> (every Friday) for orders completed and delivered in the prior period</li>
<li>Payment is issued via EFT (direct deposit), Interac e-Transfer, or cheque to the banking information on file</li>
<li>OCSAPP deducts its commission and any applicable fees before remitting</li>
<li>A detailed payment statement is provided with each payout</li>
</ul>

<h2>6. Returns & Refunds</h2>
<ul>
<li>Sellers must accept returns for items that are defective, damaged, or materially different from the listing</li>
<li>Sellers may set their own return window (minimum 7 days from delivery)</li>
<li>In cases of proven seller fault, OCSAPP may process a refund and recover the amount from the seller's next payout</li>
</ul>

<h2>7. Account Standards & Suspension</h2>
<p>OCSAPP monitors seller performance. Accounts may be suspended or terminated for:</p>
<ul>
<li>Order cancellation rate above 5% in any 30-day period</li>
<li>Verified fraudulent listings or misrepresentation</li>
<li>Persistent negative buyer feedback</li>
<li>Failure to fulfill orders without communication</li>
<li>Any violation of this Agreement or OCSAPP's Terms of Service</li>
</ul>
<p>OCSAPP will make reasonable efforts to notify sellers before suspension. In cases of fraud or serious policy violations, accounts may be suspended immediately without prior notice.</p>

<h2>8. Intellectual Property</h2>
<p>By uploading product images and descriptions, you grant OCSAPP a non-exclusive, royalty-free license to display, reproduce, and promote your listings on the platform and in marketing materials.</p>
<p>You represent and warrant that you own or have the right to use all content you upload.</p>

<h2>9. Tax Obligations</h2>
<p>Sellers are solely responsible for collecting, reporting, and remitting all applicable taxes (GST/HST, QST) on their sales. OCSAPP provides sales records to assist with reporting but is not responsible for your tax compliance.</p>

<h2>10. Privacy of Buyer Data</h2>
<p>Buyer information shared with you (name, address, order details) is provided solely for order fulfillment. You may not use buyer data for marketing purposes, sell it to third parties, or retain it beyond what is necessary for the transaction.</p>

<h2>11. Limitation of Liability</h2>
<p>OCSAPP acts as a platform intermediary. We are not liable for:</p>
<ul>
<li>Disputes between buyers and sellers</li>
<li>Product defects or safety issues</li>
<li>Losses arising from account suspension due to policy violations</li>
<li>Interruptions in payment processing due to third-party provider issues</li>
</ul>

<h2>12. Amendments</h2>
<p>OCSAPP reserves the right to update this Agreement at any time. Sellers will be notified by email at least 30 days before material changes take effect. Continued selling after the effective date constitutes acceptance.</p>

<h2>13. Governing Law</h2>
<p>This Agreement is governed by the laws of Canada and the Province of Quebec. Any disputes arising under this Agreement shall be resolved in the courts of the Province of Quebec.</p>

<h2>14. Contact</h2>
<ul>
<li><strong>Seller Support:</strong> <a style="color:#00b207;" href="mailto:sellers@ocsapp.ca">sellers@ocsapp.ca</a></li>
<li><strong>Legal Inquiries:</strong> <a style="color:#00b207;" href="mailto:legal@ocsapp.ca">legal@ocsapp.ca</a></li>
</ul>
HTML;

        $db->prepare("
            INSERT INTO legal_content (page_type, language, title, content, meta_description, version, is_published, published_at)
            VALUES ('seller_agreement', 'en', 'Seller Agreement', ?, 'OCSAPP Seller Agreement — Commission rates, obligations, and policies for selling on our marketplace.', 1, 1, NOW())
        ")->execute([$contentEn]);
        echo "INSERT: seller_agreement/en\n";
    } else {
        echo "SKIP: seller_agreement/en already exists\n";
    }

    // ── 4. Seed FR seller agreement ────────────────────────────────────────
    $checkFr = $db->prepare("SELECT COUNT(*) FROM legal_content WHERE page_type = 'seller_agreement' AND language = 'fr'");
    $checkFr->execute();
    if ((int)$checkFr->fetchColumn() === 0) {
        $contentFr = <<<HTML
<div style="background:#f0fdf4;border-left:4px solid #00b207;padding:20px;margin:20px 0;border-radius:4px;">
<p><strong>Important — Veuillez lire avant de vous inscrire comme vendeur</strong></p>
<p>La présente entente de vendeur définit les conditions selon lesquelles vous exploitez une boutique sur OCSAPP. En cochant « J'accepte » lors de l'inscription, vous concluez un accord contraignant avec OCSAPP Inc.</p>
</div>

<h2>1. Admissibilité</h2>
<p>Pour vendre sur OCSAPP, vous devez :</p>
<ul>
<li>Être une entreprise légalement enregistrée au Canada (fédéral ou provincial)</li>
<li>Posséder un numéro de TPS/TVH valide (si le chiffre d'affaires annuel dépasse 30 000 $ CAD)</li>
<li>Avoir au moins 18 ans</li>
<li>Fournir des documents d'enregistrement commercial exacts pour vérification</li>
<li>Opérer depuis une adresse canadienne vérifiable</li>
</ul>

<h2>2. Commission et frais</h2>
<p>OCSAPP facture les frais suivants sur chaque vente complétée :</p>
<ul>
<li><strong>Commission de la plateforme :</strong> 8 % du sous-total de la commande (avant taxes)</li>
<li><strong>Traitement des paiements :</strong> Transmis au coût réel (Stripe : 2,9 % + 0,30 $ CAD par transaction)</li>
<li><strong>Pas de frais d'abonnement mensuel</strong> durant la Phase 1 (bêta sur invitation)</li>
</ul>
<p>OCSAPP se réserve le droit de mettre à jour la structure de commission avec un préavis écrit de <strong>30 jours</strong> aux vendeurs actifs.</p>

<h2>3. Obligations du vendeur</h2>
<h3>3.1 Fiches produits</h3>
<ul>
<li>Tous les produits doivent être décrits avec précision (état, dimensions, matériaux)</li>
<li>Les photos doivent être des images originales ou sous licence appropriée du produit réel</li>
<li>Les prix doivent être en dollars canadiens (CAD) et inclure toutes les taxes applicables</li>
<li>Le stock doit être maintenu à jour pour éviter les ventes excédentaires</li>
</ul>
<h3>3.2 Exécution des commandes</h3>
<ul>
<li>Les commandes doivent être confirmées ou marquées comme prêtes dans les <strong>24 heures</strong> suivant leur réception</li>
<li>Les produits doivent être emballés de manière sécurisée et prêts pour la collecte par les livreurs OCSAPP</li>
<li>Les vendeurs sont responsables de l'exactitude et de la sécurité des commandes emballées</li>
</ul>
<h3>3.3 Service à la clientèle</h3>
<ul>
<li>Les vendeurs doivent répondre aux demandes des acheteurs dans les 48 heures</li>
<li>Les vendeurs doivent honorer leur politique de retour déclarée</li>
<li>Les litiges doivent être traités de manière professionnelle ; OCSAPP peut servir de médiateur si nécessaire</li>
</ul>

<h2>4. Produits interdits</h2>
<p>Les éléments suivants ne peuvent en aucun cas être mis en vente sur OCSAPP :</p>
<ul>
<li>Substances illégales ou contrôlées (drogues, armes, matières dangereuses)</li>
<li>Marchandises contrefaites, piratées ou volées</li>
<li>Produits portant atteinte à des marques de commerce, brevets ou droits d'auteur</li>
<li>Contenu réservé aux adultes sans approbation préalable explicite d'OCSAPP</li>
<li>Animaux vivants</li>
<li>Tout produit interdit par la loi fédérale canadienne ou la loi provinciale québécoise</li>
</ul>

<h2>5. Paiements aux vendeurs</h2>
<ul>
<li>Les versements aux vendeurs sont traités <strong>chaque semaine</strong> (tous les vendredis) pour les commandes complétées et livrées au cours de la période précédente</li>
<li>Le paiement est effectué par TVF (dépôt direct), virement Interac ou chèque selon les informations bancaires enregistrées</li>
<li>OCSAPP déduit sa commission et les frais applicables avant le versement</li>
<li>Un relevé de paiement détaillé est fourni avec chaque versement</li>
</ul>

<h2>6. Retours et remboursements</h2>
<ul>
<li>Les vendeurs doivent accepter les retours pour les articles défectueux, endommagés ou sensiblement différents de la fiche produit</li>
<li>Les vendeurs peuvent définir leur propre délai de retour (minimum 7 jours à compter de la livraison)</li>
<li>En cas de faute avérée du vendeur, OCSAPP peut traiter un remboursement et récupérer le montant sur le prochain versement du vendeur</li>
</ul>

<h2>7. Normes du compte et suspension</h2>
<p>OCSAPP surveille les performances des vendeurs. Les comptes peuvent être suspendus ou résiliés en cas de :</p>
<ul>
<li>Taux d'annulation de commandes supérieur à 5 % au cours d'une période de 30 jours</li>
<li>Fiches produits frauduleuses ou déclarations inexactes avérées</li>
<li>Commentaires négatifs persistants des acheteurs</li>
<li>Non-exécution des commandes sans communication</li>
<li>Toute violation de la présente entente ou des conditions d'utilisation d'OCSAPP</li>
</ul>

<h2>8. Propriété intellectuelle</h2>
<p>En téléchargeant des images et des descriptions de produits, vous accordez à OCSAPP une licence non exclusive et libre de redevances pour afficher, reproduire et promouvoir vos fiches sur la plateforme et dans les supports marketing.</p>

<h2>9. Obligations fiscales</h2>
<p>Les vendeurs sont seuls responsables de la collecte, de la déclaration et du versement de toutes les taxes applicables (TPS/TVH, TVQ) sur leurs ventes. OCSAPP fournit des enregistrements de ventes pour faciliter la déclaration, mais n'est pas responsable de votre conformité fiscale.</p>

<h2>10. Protection des données des acheteurs</h2>
<p>Les informations sur les acheteurs qui vous sont communiquées (nom, adresse, détails de la commande) sont fournies uniquement pour l'exécution des commandes. Vous ne pouvez pas utiliser les données des acheteurs à des fins de marketing, les vendre à des tiers ou les conserver au-delà de ce qui est nécessaire pour la transaction.</p>

<h2>11. Modification de l'entente</h2>
<p>OCSAPP se réserve le droit de mettre à jour la présente entente à tout moment. Les vendeurs seront informés par courriel au moins 30 jours avant l'entrée en vigueur des modifications importantes. La poursuite des activités de vente après la date d'entrée en vigueur vaut acceptation.</p>

<h2>12. Droit applicable</h2>
<p>La présente entente est régie par les lois du Canada et de la province de Québec. Tout litige découlant de la présente entente sera résolu devant les tribunaux de la province de Québec.</p>

<h2>13. Contact</h2>
<ul>
<li><strong>Soutien aux vendeurs :</strong> <a style="color:#00b207;" href="mailto:sellers@ocsapp.ca">sellers@ocsapp.ca</a></li>
<li><strong>Questions juridiques :</strong> <a style="color:#00b207;" href="mailto:legal@ocsapp.ca">legal@ocsapp.ca</a></li>
</ul>
HTML;

        $db->prepare("
            INSERT INTO legal_content (page_type, language, title, content, meta_description, version, is_published, published_at)
            VALUES ('seller_agreement', 'fr', 'Entente de vendeur', ?, 'Entente de vendeur OCSAPP — Taux de commission, obligations et politiques pour vendre sur notre marché.', 1, 1, NOW())
        ")->execute([$contentFr]);
        echo "INSERT: seller_agreement/fr\n";
    } else {
        echo "SKIP: seller_agreement/fr already exists\n";
    }

    echo "\nDone.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
